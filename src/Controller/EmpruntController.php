<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Entity\Exemplaire;
use App\Form\EmpruntType;
use App\Repository\EmpruntRepository;
use App\Repository\PenaliteRepository;
use App\Service\EmpruntService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/emprunt')]
#[IsGranted('ROLE_USER')]
class EmpruntController extends AbstractController
{
    public function __construct(
        private EmpruntService $empruntService,
        private EmpruntRepository $empruntRepository,
        private PenaliteRepository $penaliteRepository
    ) {}

    #[Route('/mes-emprunts', name: 'app_emprunt_mes_emprunts', methods: ['GET'])]
    public function mesEmprunts(): Response
    {
        $user = $this->getUser();
        $empruntsActifs = $this->empruntRepository->findActiveByUser($user);
        $penalitesImpayees = $this->penaliteRepository->findUnpaidByUser($user);
        $totalPenalites = $this->penaliteRepository->getTotalUnpaidByUser($user);

        return $this->render('emprunt/mes_emprunts.html.twig', [
            'empruntsActifs' => $empruntsActifs,
            'penalites' => $penalitesImpayees,
            'totalPenalites' => $totalPenalites,
        ]);
    }

    #[Route('/nouveau/{id}', name: 'app_emprunt_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Exemplaire $exemplaire): Response
    {
        if (!$exemplaire->isDisponible()) {
            $this->addFlash('error', 'Cet exemplaire n\'est pas disponible.');
            return $this->redirectToRoute('app_ouvrage_show', ['id' => $exemplaire->getOuvrage()->getId()]);
        }

        $user = $this->getUser();
        if (!$this->empruntService->peutEmprunter($user)) {
            $this->addFlash('error', 'Vous avez atteint le nombre maximum d\'emprunts simultanés.');
            return $this->redirectToRoute('app_emprunt_mes_emprunts');
        }

        $emprunt = new Emprunt();
        $emprunt->setExemplaire($exemplaire);
        $emprunt->setDateRetourPrevue($this->empruntService->calculerDateRetourDefaut($exemplaire));

        $form = $this->createForm(EmpruntType::class, $emprunt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->empruntService->creerEmprunt(
                    $user,
                    $exemplaire,
                    $emprunt->getDateRetourPrevue()
                );

                $this->addFlash('success', 'Emprunt enregistré avec succès !');
                return $this->redirectToRoute('app_emprunt_mes_emprunts');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('emprunt/new.html.twig', [
            'exemplaire' => $exemplaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/retour', name: 'app_emprunt_retour', methods: ['POST'])]
    public function retour(Request $request, Emprunt $emprunt, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que c'est bien l'emprunt de l'utilisateur ou un librarian
        if ($emprunt->getUser() !== $this->getUser() && !$this->isGranted('ROLE_LIBRARIAN')) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('retour'.$emprunt->getId(), $request->request->get('_token'))) {
            try {
                $this->empruntService->retournerEmprunt($emprunt);
                $this->addFlash('success', 'Retour enregistré avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->redirectToRoute('app_emprunt_mes_emprunts');
    }

    #[Route('/{id}/renouveler', name: 'app_emprunt_renouveler', methods: ['POST'])]
    public function renouveler(Request $request, Emprunt $emprunt): Response
    {
        // Vérifier que c'est bien l'emprunt de l'utilisateur
        if ($emprunt->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('renouveler'.$emprunt->getId(), $request->request->get('_token'))) {
            try {
                $this->empruntService->renouvelerEmprunt($emprunt);
                $this->addFlash('success', 'Emprunt renouvelé avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->redirectToRoute('app_emprunt_mes_emprunts');
    }

    #[Route('/historique', name: 'app_emprunt_historique', methods: ['GET'])]
    public function historique(): Response
    {
        $user = $this->getUser();

        $emprunts = $this->empruntRepository->createQueryBuilder('e')
            ->andWhere('e.user = :user')
            ->andWhere('e.dateRetourEffective IS NOT NULL')
            ->setParameter('user', $user)
            ->orderBy('e.dateRetourEffective', 'DESC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();

        return $this->render('emprunt/historique.html.twig', [
            'emprunts' => $emprunts,
        ]);
    }

    #[Route('/gestion', name: 'app_emprunt_gestion', methods: ['GET'])]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function gestion(): Response
    {
        $empruntsActifs = $this->empruntRepository->createQueryBuilder('e')
            ->andWhere('e.dateRetourEffective IS NULL')
            ->orderBy('e.dateRetourPrevue', 'ASC')
            ->getQuery()
            ->getResult();

        $empruntsEnRetard = $this->empruntRepository->findEmpruntsEnRetard();

        return $this->render('emprunt/gestion.html.twig', [
            'empruntsActifs' => $empruntsActifs,
            'empruntsEnRetard' => $empruntsEnRetard,
        ]);
    }
}
