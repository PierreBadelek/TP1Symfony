<?php

namespace App\MessageHandler;

use App\Message\EnvoyerRappelsMessage;
use App\Repository\EmpruntRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;

#[AsMessageHandler]
class EnvoyerRappelsMessageHandler
{
    public function __construct(
        private EmpruntRepository $empruntRepository,
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer,
        private LoggerInterface $logger
    ) {}

    public function __invoke(EnvoyerRappelsMessage $message): void
    {
        $this->logger->info('Début de l\'envoi des rappels d\'emprunt');

        $now = new \DateTime();
        $today = (clone $now)->setTime(0, 0, 0);
        $sentCount = 0;

        // J-3 : Rappel 3 jours avant la date de retour
        $dateJ3Start = (clone $today)->modify('+3 days');
        $dateJ3End = (clone $dateJ3Start)->setTime(23, 59, 59);
        $empruntsJ3 = $this->empruntRepository->createQueryBuilder('e')
            ->where('e.dateRetourEffective IS NULL')
            ->andWhere('e.dateRappelJ3 IS NULL')
            ->andWhere('e.dateRetourPrevue >= :dateJ3Start')
            ->andWhere('e.dateRetourPrevue <= :dateJ3End')
            ->setParameter('dateJ3Start', $dateJ3Start)
            ->setParameter('dateJ3End', $dateJ3End)
            ->getQuery()
            ->getResult();

        foreach ($empruntsJ3 as $emprunt) {
            $this->sendReminderEmail($emprunt, 'j3');
            $emprunt->setDateRappelJ3($now);
            $sentCount++;
        }

        // J-0 : Rappel le jour de la date de retour
        $dateJ0End = (clone $today)->setTime(23, 59, 59);
        $empruntsJ0 = $this->empruntRepository->createQueryBuilder('e')
            ->where('e.dateRetourEffective IS NULL')
            ->andWhere('e.dateRappelJ0 IS NULL')
            ->andWhere('e.dateRetourPrevue >= :dateJ0Start')
            ->andWhere('e.dateRetourPrevue <= :dateJ0End')
            ->setParameter('dateJ0Start', $today)
            ->setParameter('dateJ0End', $dateJ0End)
            ->getQuery()
            ->getResult();

        foreach ($empruntsJ0 as $emprunt) {
            $this->sendReminderEmail($emprunt, 'j0');
            $emprunt->setDateRappelJ0($now);
            $sentCount++;
        }

        // J+7 : Rappel 7 jours après la date de retour (retard)
        $dateJ7Start = (clone $today)->modify('-7 days');
        $dateJ7End = (clone $dateJ7Start)->setTime(23, 59, 59);
        $empruntsJ7 = $this->empruntRepository->createQueryBuilder('e')
            ->where('e.dateRetourEffective IS NULL')
            ->andWhere('e.dateRappelJ7 IS NULL')
            ->andWhere('e.dateRetourPrevue >= :dateJ7Start')
            ->andWhere('e.dateRetourPrevue <= :dateJ7End')
            ->setParameter('dateJ7Start', $dateJ7Start)
            ->setParameter('dateJ7End', $dateJ7End)
            ->getQuery()
            ->getResult();

        foreach ($empruntsJ7 as $emprunt) {
            $this->sendReminderEmail($emprunt, 'j7');
            $emprunt->setDateRappelJ7($now);
            $emprunt->setStatut('en_retard');
            $sentCount++;
        }

        $this->entityManager->flush();

        $this->logger->info(sprintf('%d email(s) de rappel envoyé(s)', $sentCount));
    }

    private function sendReminderEmail($emprunt, string $type): void
    {
        $user = $emprunt->getUser();
        $exemplaire = $emprunt->getExemplaire();
        $ouvrage = $exemplaire->getOuvrage();

        $subjects = [
            'j3' => 'Rappel : Retour de votre emprunt dans 3 jours',
            'j0' => 'Rappel : Retour de votre emprunt aujourd\'hui',
            'j7' => 'URGENT : Votre emprunt est en retard de 7 jours',
        ];

        $email = (new TemplatedEmail())
            ->from(new Address('noreply@librashelf.local', 'LibraShelf'))
            ->to(new Address($user->getEmail(), $user->getEmail()))
            ->subject($subjects[$type])
            ->htmlTemplate("emprunt/emails/rappel_{$type}.html.twig")
            ->context([
                'user' => $user,
                'emprunt' => $emprunt,
                'exemplaire' => $exemplaire,
                'ouvrage' => $ouvrage,
            ]);

        try {
            $this->mailer->send($email);
            $this->logger->info(sprintf('Email %s envoyé à %s pour l\'emprunt #%d', $type, $user->getEmail(), $emprunt->getId()));
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Erreur lors de l\'envoi de l\'email %s à %s: %s', $type, $user->getEmail(), $e->getMessage()));
        }
    }
}
