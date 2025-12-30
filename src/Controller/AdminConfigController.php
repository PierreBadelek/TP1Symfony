<?php

namespace App\Controller;

use App\Service\ConfigurationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/config')]
#[IsGranted('ROLE_ADMIN')]
class AdminConfigController extends AbstractController
{
    public function __construct(
        private ConfigurationService $configurationService
    ) {}

    #[Route('/', name: 'app_admin_config_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $configs = $this->configurationService->getAllConfigurations();
        $defaultConfigs = $this->configurationService->getDefaultConfigurations();

        if ($request->isMethod('POST')) {
            $this->configurationService->updateConfigurations($request->request->all());
            $this->addFlash('success', 'Configuration mise à jour avec succès !');

            return $this->redirectToRoute('app_admin_config_index');
        }

        return $this->render('admin/config/index.html.twig', [
            'configs' => $configs,
            'defaultConfigs' => $defaultConfigs,
        ]);
    }
}
