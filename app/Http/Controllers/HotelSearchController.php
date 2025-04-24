<?php

namespace App\Http\Controllers;

use App\Http\Middleware\HuggingFaceSemanticService;
use App\Http\Middleware\SemanticEnrichmentService;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelSearchController extends Controller
{
    protected $semanticService;
    protected $enrichmentService;

    public function __construct(
        HuggingFaceSemanticService $semanticService,
        SemanticEnrichmentService $enrichmentService
    ) {
        $this->semanticService = $semanticService;
        $this->enrichmentService = $enrichmentService;
    }

    public function search(Request $request)
    {
        $query = $request->input('q');

        // Utiliser Hugging Face pour analyser la requête
        $analysis = $this->semanticService->extractKeywords($query);
        $keywords = $analysis['keywords'];
        $viewType = $analysis['viewType'];
        $viewTerms = $analysis['viewTerms'];

        // Recherche principale basée sur les mots-clés extraits
        $hotels = collect();

        if (!empty($viewTerms)) {
            // Construire une requête Eloquent enrichie
            $queryBuilder = Hotel::query();

            foreach ($viewTerms as $term) {
                $queryBuilder->where(function($q) use ($term) {
                    $q->where('description', 'like', '%' . $term . '%')
                        ->orWhereJsonContains('caractéristiques->vue_' . $term, true)
                        ->orWhereJsonContains('caractéristiques->semantic_' . $term, true);

                    // Chercher aussi dans les synonymes
                    $synonyms = $this->semanticService->findSynonyms($term);
                    foreach ($synonyms as $synonym) {
                        $q->orWhere('description', 'like', '%' . $synonym . '%')
                            ->orWhereJsonContains('caractéristiques->semantic_' . $synonym, true);
                    }
                });
            }

            $hotels = $queryBuilder->get();
        }

        // Obtenir des suggestions via Hugging Face
        $suggestionCategories = $this->semanticService->suggestAlternatives($analysis);
        $suggestedHotels = collect();

        // Rechercher des hôtels correspondant aux suggestions
        foreach ($suggestionCategories as $suggestion) {
            $term = $suggestion['type'];
            $results = Hotel::where(function($q) use ($term) {
                $q->where('description', 'like', '%' . $term . '%')
                    ->orWhereJsonContains('caractéristiques->vue_' . $term, true)
                    ->orWhereJsonContains('caractéristiques->semantic_' . $term, true);
            })
                ->take(2)
                ->get();

            if ($results->isNotEmpty()) {
                // Ajouter le terme de suggestion à chaque hôtel pour l'affichage
                $results->each(function($hotel) use ($term) {
                    $hotel->suggestion_term = $term;
                });

                $suggestedHotels = $suggestedHotels->merge($results);
            }
        }

        return view('hotels.search-results', [
            'query' => $query,
            'hotels' => $hotels,
            'suggestedHotels' => $suggestedHotels,
            'keywords' => $keywords,
            'suggestions' => $suggestionCategories,
            'viewType' => $viewType
        ]);
    }

    /**
     * Affiche les détails d'un hôtel spécifique
     */
    public function show(Hotel $hotel)
    {
        return view('hotels.show', compact('hotel'));
    }

    /**
     * Endpoint d'administration pour enrichir tous les hôtels
     */
    public function enrichAllHotels()
    {
        $count = $this->enrichmentService->enrichAllHotels();

        return back()->with('success', $count . ' hôtels ont été enrichis avec des données sémantiques');
    }
}

