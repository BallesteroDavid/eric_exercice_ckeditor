<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CrossrefService
{
    public function __construct(private readonly HttpClientInterface $httpClient){}

    public function fetchWorkByDoi(string $doi): array
    {
        $response = $this->httpClient->request('GET', 'https://api.crossref.org/works/' . rawurlencode($doi), [
            'query' => [
                'mailto' => 'david.ballestero@laplateforme.io',
            ],
            'headers' => [
                'User-Agent' => 'EricWebsiteExercise/1.0 (mailto:david.ballestero@laplateforme.io)',
            ],
            'timeout' => 8,
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Aucune référence trouvée pour ce DOI.');
        }

        $data = $response->toArray(false);
        $message = $data['message'] ?? [];

        return [
            'doi' => $message['DOI'] ?? $doi,
            'title' => $message['title'][0] ?? 'Titre non renseigné',
            'authors' => $this->formatAuthors($message['author'] ?? []),
            'year' => $message['issued']['date-parts'][0][0] ?? null,
            'journal' => $message['container-title'][0] ?? null,
            'publisher' => $message['publisher'] ?? null,
            'url' => $message['URL'] ?? 'https://doi.org/' . $doi,
        ];
    }
    
    private function formatAuthors(array $authors): string
    {
        if ($authors === []) {
            return 'Auteur non renseigné';
        }

        $formattedAuthors = [];

        foreach ($authors as $author) {
            $given = $author['given'] ?? '';
            $family = $author['family'] ?? '';

            $fullName = trim($given . ' ' . $family);

            if ($fullName !== '') {
                $formattedAuthors[] = $fullName;
            }
        }

        if ($formattedAuthors === []) {
            return 'Auteur non renseigné';
        }

        return implode(', ', $formattedAuthors);
    }
}