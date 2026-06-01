<?php

namespace App\Controller;

use App\Entity\ResearchSubject;
use App\Repository\ResearchSubjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/research')]
final class PublicResearchController extends AbstractController
{
    #[Route(name: 'app_public_research_index', methods: ['GET'])]
    public function index(ResearchSubjectRepository $researchSubjectRepository): Response
    {
        return $this->render('public_research/index.html.twig', [
            'research_subjects' => $researchSubjectRepository->findBy(
                ['status' => ResearchSubject::STATUS_PUBLISHED],
                ['createdAt' => 'DESC']
            ),
        ]);
    }

    #[Route('/{slug}', name: 'app_public_research_show', methods: ['GET'])]
    public function show(string $slug, ResearchSubjectRepository $researchSubjectRepository): Response
    {
        $researchSubject = $researchSubjectRepository->findOneBy([
            'slug' => $slug,
            'status' => ResearchSubject::STATUS_PUBLISHED,
        ]);

        if (!$researchSubject) {
            throw $this->createNotFoundException('Sujet de recherche introuvable.');
        }

        return $this->render('public_research/show.html.twig', [
            'research_subject' => $researchSubject,
        ]);
    }
}