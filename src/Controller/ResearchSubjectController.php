<?php

namespace App\Controller;

use App\Entity\ResearchSubject;
use App\Form\ResearchSubjectType;
use App\Repository\ResearchSubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/research/subject')]
final class ResearchSubjectController extends AbstractController
{
    #[Route(name: 'app_research_subject_index', methods: ['GET'])]
    public function index(ResearchSubjectRepository $researchSubjectRepository): Response
    {
        return $this->render('research_subject/index.html.twig', [
            'research_subjects' => $researchSubjectRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_research_subject_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response 
    {
        $researchSubject = new ResearchSubject();

        $form = $this->createForm(ResearchSubjectType::class, $researchSubject);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $slug = $slugger
                ->slug($researchSubject->getTitle() ?? '')
                ->lower()
                ->toString();

            $researchSubject->setSlug($slug);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($researchSubject);
            $entityManager->flush();

            return $this->redirectToRoute('app_research_subject_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('research_subject/new.html.twig', [
            'research_subject' => $researchSubject,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_research_subject_show', methods: ['GET'])]
    public function show(ResearchSubject $researchSubject): Response
    {
        return $this->render('research_subject/show.html.twig', [
            'research_subject' => $researchSubject,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_research_subject_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ResearchSubject $researchSubject, EntityManagerInterface $entityManager, SluggerInterface $slugger
    ): Response 
    {
        $form = $this->createForm(ResearchSubjectType::class, $researchSubject);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $slug = $slugger
                ->slug($researchSubject->getTitle() ?? '')
                ->lower()
                ->toString();

            $researchSubject->setSlug($slug);
            $researchSubject->setUpdatedAt(new \DateTimeImmutable());
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_research_subject_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('research_subject/edit.html.twig', [
            'research_subject' => $researchSubject,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_research_subject_delete', methods: ['POST'])]
    public function delete(Request $request, ResearchSubject $researchSubject, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$researchSubject->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($researchSubject);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_research_subject_index', [], Response::HTTP_SEE_OTHER);
    }
}
