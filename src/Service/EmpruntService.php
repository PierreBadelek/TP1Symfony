<?php

namespace App\Service;

use App\Entity\Emprunt;
use App\Entity\Exemplaire;
use App\Entity\Penalite;
use App\Entity\User;
use App\Repository\BibliothequeConfigRepository;
use App\Repository\EmpruntRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class EmpruntService
{
    private const DUREE_EMPRUNT_DEFAUT = 21; // 21 jours par défaut
    private const PENALITE_PAR_JOUR = 0.50; // 0.50€ par jour de retard
    private const MAX_EMPRUNTS_SIMULTANEES = 5;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private EmpruntRepository $empruntRepository,
        private BibliothequeConfigRepository $configRepository,
        private AuditLogService $auditLogService
    ) {}

    /**
     * Créer un nouvel emprunt
     */
    public function creerEmprunt(User $user, Exemplaire $exemplaire, \DateTimeInterface $dateRetourPrevue): Emprunt
    {
        // Vérifications
        if (!$exemplaire->isDisponible()) {
            throw new \RuntimeException('Cet exemplaire n\'est pas disponible.');
        }

        $empruntsActifs = $this->empruntRepository->findActiveByUser($user);
        $maxEmprunts = $this->getConfig('nombre_max_emprunts', self::MAX_EMPRUNTS_SIMULTANEES);

        if (count($empruntsActifs) >= $maxEmprunts) {
            throw new \RuntimeException(sprintf('Vous avez déjà %d emprunts en cours. Maximum autorisé : %d', count($empruntsActifs), $maxEmprunts));
        }

        // Créer l'emprunt
        $emprunt = new Emprunt();
        $emprunt->setUser($user);
        $emprunt->setExemplaire($exemplaire);
        $emprunt->setDateEmprunt(new \DateTime());
        $emprunt->setDateRetourPrevue($dateRetourPrevue);
        $emprunt->setStatut('en_cours');

        // Marquer l'exemplaire comme indisponible
        $exemplaire->setDisponible(false);

        $this->entityManager->persist($emprunt);
        $this->entityManager->flush();

        // Log audit
        $this->auditLogService->log(
            'EMPRUNT_CREATE',
            'Emprunt',
            $emprunt->getId(),
            sprintf('Emprunt de "%s" par %s', $exemplaire->getOuvrage()->getTitre(), $user->getEmail())
        );

        return $emprunt;
    }

    /**
     * Retourner un emprunt
     */
    public function retournerEmprunt(Emprunt $emprunt): void
    {
        if ($emprunt->getDateRetourEffective()) {
            throw new \RuntimeException('Cet emprunt a déjà été retourné.');
        }

        $dateRetour = new \DateTime();
        $emprunt->setDateRetourEffective($dateRetour);

        // Vérifier s'il y a du retard et calculer la pénalité
        if ($emprunt->isEnRetard()) {
            $joursRetard = $emprunt->getJoursRetard();
            $montantPenalite = $joursRetard * $this->getConfig('penalite_par_jour', self::PENALITE_PAR_JOUR);

            $penalite = new Penalite();
            $penalite->setEmprunt($emprunt);
            $penalite->setMontant((string)$montantPenalite);
            $penalite->setDateCreation($dateRetour);
            $penalite->setJoursRetard($joursRetard);
            $penalite->setStatut('impayee');

            $this->entityManager->persist($penalite);
            $emprunt->setStatut('termine_avec_retard');
        } else {
            $emprunt->setStatut('termine');
        }

        // Rendre l'exemplaire disponible
        $emprunt->getExemplaire()->setDisponible(true);

        $this->entityManager->flush();

        // Log audit
        $this->auditLogService->log(
            'EMPRUNT_RETOUR',
            'Emprunt',
            $emprunt->getId(),
            sprintf('Retour de "%s" par %s', $emprunt->getExemplaire()->getOuvrage()->getTitre(), $emprunt->getUser()->getEmail())
        );
    }

    /**
     * Renouveler un emprunt (si pas de retard)
     */
    public function renouvelerEmprunt(Emprunt $emprunt): void
    {
        if ($emprunt->getDateRetourEffective()) {
            throw new \RuntimeException('Cet emprunt a déjà été retourné.');
        }

        if ($emprunt->isEnRetard()) {
            throw new \RuntimeException('Impossible de renouveler un emprunt en retard.');
        }

        // Utiliser la durée de la catégorie si définie
        $dureeEmprunt = $this->getConfig('duree_emprunt_defaut', self::DUREE_EMPRUNT_DEFAUT);
        $exemplaire = $emprunt->getExemplaire();

        if ($exemplaire && $exemplaire->getOuvrage()) {
            foreach ($exemplaire->getOuvrage()->getCategories() as $categorie) {
                $dureeCategorie = $categorie->getDureeEmprunt();
                if ($dureeCategorie !== null) {
                    $dureeEmprunt = $dureeCategorie;
                    break;
                }
            }
        }

        $nouvelleDate = (clone $emprunt->getDateRetourPrevue())->modify("+{$dureeEmprunt} days");

        $emprunt->setDateRetourPrevue($nouvelleDate);
        $this->entityManager->flush();

        // Log audit
        $this->auditLogService->log(
            'EMPRUNT_RENOUVELE',
            'Emprunt',
            $emprunt->getId(),
            sprintf('Renouvellement jusqu\'au %s', $nouvelleDate->format('d/m/Y'))
        );
    }

    /**
     * Obtenir les emprunts actifs d'un utilisateur
     */
    public function getEmpruntsActifs(User $user): array
    {
        return $this->empruntRepository->findActiveByUser($user);
    }

    /**
     * Calculer la date de retour prévue par défaut
     * Si un exemplaire est fourni, vérifie si une de ses catégories a une durée spécifique
     */
    public function calculerDateRetourDefaut(?Exemplaire $exemplaire = null): \DateTime
    {
        $duree = $this->getConfig('duree_emprunt_defaut', self::DUREE_EMPRUNT_DEFAUT);

        // Si un exemplaire est fourni, vérifier les durées des catégories
        if ($exemplaire && $exemplaire->getOuvrage()) {
            foreach ($exemplaire->getOuvrage()->getCategories() as $categorie) {
                $dureeCategorie = $categorie->getDureeEmprunt();
                if ($dureeCategorie !== null) {
                    $duree = $dureeCategorie;
                    break; // Utiliser la première catégorie avec une durée définie
                }
            }
        }

        return (new \DateTime())->modify("+{$duree} days");
    }

    /**
     * Vérifier si un utilisateur peut emprunter
     */
    public function peutEmprunter(User $user): bool
    {
        $empruntsActifs = $this->empruntRepository->findActiveByUser($user);
        $maxEmprunts = $this->getConfig('nombre_max_emprunts', self::MAX_EMPRUNTS_SIMULTANEES);

        return count($empruntsActifs) < $maxEmprunts;
    }

    /**
     * Obtenir une valeur de configuration
     */
    private function getConfig(string $cle, mixed $defaut): mixed
    {
        $config = $this->configRepository->findOneByCle($cle);
        return $config ? $config->getValeur() : $defaut;
    }
}
