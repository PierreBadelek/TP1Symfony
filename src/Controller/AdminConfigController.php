<?php

namespace App\Controller;

use App\Entity\BibliothequeConfig;
use App\Repository\BibliothequeConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/config')]
#[IsGranted('ROLE_ADMIN')]
class AdminConfigController extends AbstractController
{
    #[Route('/', name: 'app_admin_config_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        BibliothequeConfigRepository $configRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Définir les configurations par défaut
        $defaultConfigs = [
            'duree_emprunt_defaut' => ['label' => 'Durée d\'emprunt par défaut (jours)', 'valeur' => '21'],
            'penalite_par_jour' => ['label' => 'Pénalité par jour de retard (€)', 'valeur' => '0.50'],
            'nombre_max_emprunts' => ['label' => 'Nombre maximum d\'emprunts simultanés', 'valeur' => '5'],
            'delai_reservation' => ['label' => 'Délai pour récupérer une réservation (jours)', 'valeur' => '3'],
        ];

        // Charger les configurations existantes
        $configs = [];
        foreach ($defaultConfigs as $cle => $default) {
            $config = $configRepository->findOneByCle($cle);
            if (!$config) {
                $config = new BibliothequeConfig();
                $config->setCle($cle);
                $config->setValeur($default['valeur']);
                $config->setDescription($default['label']);
                $entityManager->persist($config);
            }
            $configs[$cle] = $config;
        }
        $entityManager->flush();

        // Traitement du formulaire
        if ($request->isMethod('POST')) {
            foreach ($configs as $cle => $config) {
                $valeur = $request->request->get($cle);
                if ($valeur !== null) {
                    $config->setValeur($valeur);
                }
            }
            $entityManager->flush();

            $this->addFlash('success', 'Configuration mise à jour avec succès !');
            return $this->redirectToRoute('app_admin_config_index');
        }

        return $this->render('admin/config/index.html.twig', [
            'configs' => $configs,
            'defaultConfigs' => $defaultConfigs,
        ]);
    }
}
