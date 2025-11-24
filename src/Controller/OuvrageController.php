<?php

namespace App\Controller;

use App\Entity\Ouvrage;
use App\Entity\Ouvrage1Type;
use App\Form\OuvrageSearchType;
use App\Repository\OuvrageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ouvrage')]
final class OuvrageController extends AbstractController
{
    #[Route(name: 'app_ouvrage_index', methods: ['GET'])]
    public function index(OuvrageRepository $ouvrageRepository, Request $request): Response
    {
        $search = new Ouvrage();
        $form = $this->createForm(OuvrageSearchType::class, $search);
        $form->handleRequest($request);

        $page = $request->query->getInt('page', 1);
        $limit = 100;

        return $this->render('ouvrage/index.html.twig', [
            'lesOuvrages' => $ouvrageRepository->findAllSearch($search, $page, $limit),
            'form' => $form->createView(),
            'currentPage' => $page,
            'totalPages' => $ouvrageRepository->countPages($search, $limit),
        ]);
    }

    #[Route('/new', name: 'app_ouvrage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ouvrage = new Ouvrage();
        $form = $this->createForm(Ouvrage1Type::class, $ouvrage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ouvrage);
            $entityManager->flush();

            return $this->redirectToRoute('app_ouvrage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ouvrage/new.html.twig', [
            'ouvrage' => $ouvrage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ouvrage_show', methods: ['GET'])]
    public function show(Ouvrage $ouvrage): Response
    {
        return $this->render('ouvrage/show.html.twig', [
            'ouvrage' => $ouvrage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ouvrage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ouvrage $ouvrage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Ouvrage1Type::class, $ouvrage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ouvrage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ouvrage/edit.html.twig', [
            'ouvrage' => $ouvrage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ouvrage_delete', methods: ['POST'])]
    public function delete(Request $request, Ouvrage $ouvrage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ouvrage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ouvrage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ouvrage_index', [], Response::HTTP_SEE_OTHER);
    }
}
