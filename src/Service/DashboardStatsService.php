<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\EmpruntRepository;
use App\Repository\ExemplaireRepository;
use App\Repository\UserRepository;

class DashboardStatsService
{
    public function __construct(
        private EmpruntRepository $empruntRepository,
        private ExemplaireRepository $exemplaireRepository,
        private UserRepository $userRepository
    ) {}

    public function getAdminStats(): array
    {
        $totalExemplaires = $this->exemplaireRepository->count([]);
        $exemplairesEmpruntes = $this->exemplaireRepository->count(['disponible' => false]);
        $pourcentageEmprunts = $totalExemplaires > 0
            ? round(($exemplairesEmpruntes / $totalExemplaires) * 100, 1)
            : 0;

        $empruntsActifs = $this->empruntRepository->count(['dateRetourEffective' => null]);
        $empruntsEnRetard = count($this->empruntRepository->findEmpruntsEnRetard());
        $delaiMoyen = $this->empruntRepository->getAverageLoanDuration();
        $totalUtilisateurs = $this->userRepository->count([]);

        return [
            'totalExemplaires' => $totalExemplaires,
            'exemplairesEmpruntes' => $exemplairesEmpruntes,
            'exemplairesDisponibles' => $totalExemplaires - $exemplairesEmpruntes,
            'pourcentageEmprunts' => $pourcentageEmprunts,
            'empruntsActifs' => $empruntsActifs,
            'empruntsEnRetard' => $empruntsEnRetard,
            'delaiMoyen' => $delaiMoyen,
            'totalUtilisateurs' => $totalUtilisateurs,
        ];
    }

    public function getMemberStats(User $user): array
    {
        $empruntsActifs = $this->empruntRepository->findActiveByUser($user);
        $empruntsEnRetard = array_filter($empruntsActifs, fn($e) => $e->isEnRetard());

        return [
            'empruntsActifs' => $empruntsActifs,
            'nombreEmpruntsActifs' => count($empruntsActifs),
            'empruntsEnRetard' => $empruntsEnRetard,
            'nombreEmpruntsEnRetard' => count($empruntsEnRetard),
        ];
    }
}
