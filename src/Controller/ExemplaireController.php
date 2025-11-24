<?php

namespace App\Controller;

use App\Entity\Exemplaire;
use App\Form\ExemplaireSearchType;
use App\Form\ExemplaireType;
use App\Repository\ExemplaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/exemplaire')]
final class ExemplaireController extends AbstractController
{
    #[Route(name: 'app_exemplaire_index', methods: ['GET'])]
    public function index(ExemplaireRepository $exemplaireRepository, Request $request): Response
    {
        $search = new Exemplaire();
        $form = $this->createForm(ExemplaireSearchType::class, $search);
        $form->handleRequest($request);

        $page = $request->query->getInt('page', 1);
        $limit = 100;

        return $this->render('exemplaire/index.html.twig', [
            'exemplaires' => $exemplaireRepository->findAllSearch($search, $page, $limit),
            'form' => $form->createView(),
            'currentPage' => $page,
            'totalPages' => $exemplaireRepository->countPages($search, $limit),
        ]);
    }

    #[Route('/new', name: 'app_exemplaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $exemplaire = new Exemplaire();
        $form = $this->createForm(ExemplaireType::class, $exemplaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($exemplaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_exemplaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exemplaire/new.html.twig', [
            'exemplaire' => $exemplaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_exemplaire_show', methods: ['GET'])]
    public function show(Exemplaire $exemplaire): Response
    {
        return $this->render('exemplaire/show.html.twig', [
            'exemplaire' => $exemplaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_exemplaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Exemplaire $exemplaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExemplaireType::class, $exemplaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_exemplaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exemplaire/edit.html.twig', [
            'exemplaire' => $exemplaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_exemplaire_delete', methods: ['POST'])]
    public function delete(Request $request, Exemplaire $exemplaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$exemplaire->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($exemplaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_exemplaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
