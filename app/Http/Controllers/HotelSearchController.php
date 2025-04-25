<?php

namespace App\Http\Controllers;

use App\Http\Services\HuggingFaceSemanticService;
use App\Http\Services\SemanticEnrichmentService;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

        // Analyse sémantique
        $analysis = $this->semanticService->extractKeywords($query);
        $keywords = $analysis['keywords'];
        $viewType = $analysis['viewType'];
        $viewTerms = $analysis['viewTerms'];

        $hotels = collect();

        if (!empty($viewTerms)) {
            $queryBuilder = Hotel::query();

            foreach ($viewTerms as $term) {
                $queryBuilder->where(function($q) use ($term) {
                    $q->where('description', 'ilike', '%' . $term . '%')
                        ->orWhereRaw("(description->>'vue_{$term}')::boolean = true")
                        ->orWhereRaw("(description->>'semantic_{$term}')::boolean = true");

                    $synonyms = app(HuggingFaceSemanticService::class)->findSynonyms($term);
                    foreach ($synonyms as $synonym) {
                        $q->orWhere('description', 'ilike', '%' . $synonym . '%')
                            ->orWhereRaw("(description->>'semantic_{$synonym}')::boolean = true");
                    }
                });
            }

            $hotels = $queryBuilder->get();
        }

        // Suggestions
        $suggestionCategories = $this->semanticService->suggestAlternatives($analysis);
        $suggestedHotels = collect();

        foreach ($suggestionCategories as $suggestion) {
            $term = $suggestion['type'];

            $results = Hotel::where(function($q) use ($term) {
                $q->where('description', 'ilike', '%' . $term . '%')
                    ->orWhereRaw("(description->>'vue_{$term}')::boolean = true")
                    ->orWhereRaw("(description->>'semantic_{$term}')::boolean = true");
            })->take(2)->get();

            if ($results->isNotEmpty()) {
                $results->each(function($hotel) use ($term) {
                    $hotel->suggestion_term = $term;
                });

                $suggestedHotels = $suggestedHotels->merge($results);
            }
        }

        return Inertia::render('MapMatch', [
            'query' => $query,
            'hotels' => $hotels,
            'suggestedHotels' => $suggestedHotels,
            'keywords' => $keywords,
            'suggestions' => $suggestionCategories,
            'viewType' => $viewType
        ]);
    }

    public function show(Hotel $hotel)
    {
        return view('hotels.show', compact('hotel'));
    }

    public function enrichAllHotels()
    {
        $count = $this->enrichmentService->enrichAllHotels();
        return back()->with('success', $count . ' hôtels ont été enrichis.');
    }
}
