<?php

namespace App\Service;

use App\Entity\BibliothequeConfig;
use App\Repository\BibliothequeConfigRepository;
use Doctrine\ORM\EntityManagerInterface;

class ConfigurationService
{
    private const DEFAULT_CONFIGS = [
        'duree_emprunt_defaut' => [
            'label' => 'Durée d\'emprunt par défaut (jours)',
            'valeur' => '21'
        ],
        'penalite_par_jour' => [
            'label' => 'Pénalité par jour de retard (€)',
            'valeur' => '0.50'
        ],
        'nombre_max_emprunts' => [
            'label' => 'Nombre maximum d\'emprunts simultanés',
            'valeur' => '5'
        ],
        'delai_reservation' => [
            'label' => 'Délai pour récupérer une réservation (jours)',
            'valeur' => '3'
        ],
    ];

    public function __construct(
        private BibliothequeConfigRepository $configRepository,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Retourne toutes les configurations avec leurs valeurs par défaut si nécessaire
     */
    public function getAllConfigurations(): array
    {
        $configs = [];

        foreach (self::DEFAULT_CONFIGS as $cle => $default) {
            $config = $this->configRepository->findOneByCle($cle);

            if (!$config) {
                $config = $this->createDefaultConfig($cle, $default);
            }

            $configs[$cle] = $config;
        }

        $this->entityManager->flush();

        return $configs;
    }

    /**
     * Retourne les configurations par défaut
     */
    public function getDefaultConfigurations(): array
    {
        return self::DEFAULT_CONFIGS;
    }

    /**
     * Met à jour les configurations avec les nouvelles valeurs
     */
    public function updateConfigurations(array $data): void
    {
        $configs = $this->getAllConfigurations();

        foreach ($configs as $cle => $config) {
            if (isset($data[$cle])) {
                $config->setValeur($data[$cle]);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * Récupère une configuration spécifique par sa clé
     */
    public function getConfigValue(string $cle, mixed $default = null): ?string
    {
        $config = $this->configRepository->findOneByCle($cle);

        return $config?->getValeur() ?? $default;
    }

    /**
     * Crée une configuration par défaut
     */
    private function createDefaultConfig(string $cle, array $default): BibliothequeConfig
    {
        $config = new BibliothequeConfig();
        $config->setCle($cle);
        $config->setValeur($default['valeur']);
        $config->setDescription($default['label']);

        $this->entityManager->persist($config);

        return $config;
    }
}
