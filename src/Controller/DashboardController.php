<?php

namespace App\Controller;

use App\Service\DashboardStatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    public function __construct(
        private DashboardStatsService $dashboardStatsService
    ) {}

    #[Route('/', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();
        $isAdmin = $this->isGranted('ROLE_LIBRARIAN') || $this->isGranted('ROLE_ADMIN');

        if ($isAdmin) {
            $stats = $this->dashboardStatsService->getAdminStats();
            return $this->render('dashboard/index.html.twig', [
                'isAdmin' => true,
                'stats' => $stats,
            ]);
        }

        $stats = $this->dashboardStatsService->getMemberStats($user);
        return $this->render('dashboard/index.html.twig', [
            'isAdmin' => false,
            'stats' => $stats,
        ]);
    }
}
