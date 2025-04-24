<?php

namespace App\Http\Middleware;

use App\Models\RoomType;


class SemanticEnrichmentService
{

    protected $semanticService;

    public function __construct(HuggingFaceSemanticService $semanticService)
    {
        $this->semanticService = $semanticService;
    }

    /**
     * Enrichit automatiquement les caractéristiques des hôtels avec des termes sémantiques
     */
    public function enrichHotelSemantics(RoomType $hotel)
    {
        // Extraire le texte important de l'hôtel
        $textToAnalyze = $hotel->description . ' ' . $hotel->nom;

        // Tokeniser le texte
        $words = explode(' ', strtolower($textToAnalyze));
        $words = array_map(function($word) {
            return preg_replace('/[^\p{L}\p{N}]/u', '', $word);
        }, $words);
        $words = array_filter($words, function($word) {
            return strlen($word) > 3;
        });

        // Trouver les termes significatifs
        $significantTerms = $this->findSignificantTerms($words);

        // Caractéristiques actuelles
        $features = $hotel->caractéristiques ?? [];

        // Ajouter des caractéristiques basées sur la sémantique
        foreach ($significantTerms as $term) {
            // Générer des synonymes pour chaque terme significatif
            $synonyms = $this->semanticService->findSynonyms($term);

            // Ajouter le terme original et ses synonymes comme caractéristiques
            $features['semantic_' . $term] = true;

            // Associer les vues aux caractéristiques spécifiques
            if (in_array($term, ['mer', 'ocean', 'plage'])) {
                $features['vue_mer'] = true;
            } elseif (in_array($term, ['lac', 'étang'])) {
                $features['vue_lac'] = true;
            } elseif (in_array($term, ['montagne', 'alpes', 'sommet'])) {
                $features['vue_montagne'] = true;
            } elseif (in_array($term, ['jardin', 'parc', 'verdure'])) {
                $features['vue_jardin'] = true;
            }

            // Ajouter les synonymes comme termes associés
            if (!empty($synonyms)) {
                $features['synonyms_' . $term] = $synonyms;
            }
        }

        // Mettre à jour l'hôtel
        $hotel->caractéristiques = $features;
        $hotel->save();

        return $hotel;
    }

    /**
     * Trouve les termes significatifs dans un ensemble de mots
     */
    private function findSignificantTerms(array $words): array
    {
        $potentialTerms = ['mer', 'ocean', 'plage', 'lac', 'étang', 'montagne', 'alpes', 'sommet',
            'jardin', 'parc', 'ville', 'centre', 'forêt', 'rivière', 'panorama', 'vue'];

        $foundTerms = [];

        foreach ($words as $word) {
            if (in_array($word, $potentialTerms)) {
                $foundTerms[] = $word;
            }
        }

        return array_unique($foundTerms);
    }

    /**
     * Enrichit tous les hôtels de la base de données
     */
    public function enrichAllHotels()
    {
        $hotels = RoomType::all();

        foreach ($hotels as $hotel) {
            $this->enrichHotelSemantics($hotel);
        }

        return $hotels->count();
    }

}