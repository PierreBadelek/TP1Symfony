<?php

namespace App\MessageHandler;

use App\Message\PurgeAuditLogsMessage;
use App\Repository\AuditLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class PurgeAuditLogsMessageHandler
{
    public function __construct(
        private AuditLogRepository $auditLogRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {}

    public function __invoke(PurgeAuditLogsMessage $message): void
    {
        $this->logger->info('Début de la purge des logs d\'audit de plus de 50 jours');

        // Date limite : il y a 50 jours
        $dateLimite = new \DateTime('-50 days');

        // Récupérer les logs à supprimer
        $logsToDelete = $this->auditLogRepository->createQueryBuilder('al')
            ->andWhere('al.dateAction < :dateLimite')
            ->setParameter('dateLimite', $dateLimite)
            ->getQuery()
            ->getResult();

        $count = count($logsToDelete);

        if ($count === 0) {
            $this->logger->info('Aucun log à purger');
            return;
        }

        // Supprimer les logs
        foreach ($logsToDelete as $log) {
            $this->entityManager->remove($log);
        }

        $this->entityManager->flush();

        $this->logger->info(sprintf('%d logs d\'audit ont été purgés', $count));
    }
}
