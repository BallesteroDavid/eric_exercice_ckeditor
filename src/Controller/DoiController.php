<?php

namespace App\Controller;

use App\Service\CrossrefService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class DoiController extends AbstractController
{
    #[Route('/doi/fetch', name: 'app_doi_fetch', methods: ['POST'])]
    public function fetch(Request $request, CrossrefService $crossrefService): JsonResponse
    {
        $csrfToken = $request->headers->get('X-CSRF-TOKEN');

        if (!$this->isCsrfTokenValid('fetch_doi', (string) $csrfToken)) {
            return $this->json([
                'error' => 'Token CSRF invalide.',
            ], 403);
        }

        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return $this->json([
                'error' => 'Requête invalide.',
            ], 400);
        }

        $doi = $this->normalizeDoi((string) ($payload['doi'] ?? ''));

        if (!$this->isValidDoi($doi)) {
            return $this->json([
                'error' => 'Le format du DOI est invalide.',
            ], 422);
        }

        try {
            $reference = $crossrefService->fetchWorkByDoi($doi);

            return $this->json([
                'reference' => $reference,
            ]);
        } catch (\Throwable) {
            return $this->json([
                'error' => 'Impossible de récupérer les informations de ce DOI.',
            ], 404);
        }
    }

    private function normalizeDoi(string $doi): string
    {
        $doi = trim($doi);

        $doi = preg_replace('#^(https?://)?(dx\.)?doi\.org/#i', '', $doi);
        $doi = preg_replace('/^doi:\s*/i', '', $doi);

        return trim((string) $doi);
    }

    private function isValidDoi(string $doi): bool
    {
        return preg_match('/^10\.\d{4,9}\/[-._;()\/:A-Z0-9]+$/i', $doi) === 1;
    }
}