<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

class HuggingFaceSemanticService
{

    protected $apiUrl = 'https://api-inference.huggingface.co/models/';
    protected $modelName = 'sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2';
    protected $cacheTime = 10080; // Minutes (1 semaine)

    /**
     * Extrait les mots-clés d'une requête
     */
    public function extractKeywords(string $query): array
    {
        // Nettoyer et préparer la requête
        $processedQuery = $this->preprocessText($query);

        // Extraction des mots-clés via tokenization simple
        $tokens = explode(' ', $processedQuery);
        $tokens = array_filter($tokens, function($token) {
            return strlen($token) > 2;
        });

        // Identification des termes importants (vue, mer, montagne, etc.)
        $viewTerms = $this->identifyViewTerms($tokens);
        $keywords = [];

        if (!empty($viewTerms)) {
            $keywords['vue'] = $viewTerms[0]; // On prend le premier terme de vue identifié
            $viewType = $this->getCategoryForTerm($viewTerms[0]);
        } else {
            $viewType = null;
        }

        // Autres mots-clés (comme "hôtel")
        foreach ($tokens as $token) {
            if (in_array($token, ['hotel', 'hôtel', 'auberge', 'logement'])) {
                $keywords['type'] = $token;
            }
        }

        return [
            'keywords' => $keywords,
            'viewType' => $viewType,
            'viewTerms' => $viewTerms
        ];
    }

    /**
     * Prétraitement du texte: suppression des accents, passage en minuscules
     */
    private function preprocessText(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);

        // Supprimer les stopwords français
        $stopwords = ['le', 'la', 'les', 'un', 'une', 'des', 'et', 'avec', 'sur', 'pour', 'dans', 'je', 'tu', 'il', 'nous', 'vous', 'ils'];
        $words = explode(' ', $text);
        $filteredWords = array_filter($words, function($word) use ($stopwords) {
            return !in_array($word, $stopwords) && strlen($word) > 2;
        });

        return implode(' ', $filteredWords);
    }

    /**
     * Identifie les termes liés à une vue particulière
     */
    private function identifyViewTerms(array $tokens): array
    {
        $viewRelatedTerms = [];
        $potentialViewTerms = ['mer', 'ocean', 'montagne', 'lac', 'ville', 'plage', 'jardin', 'parc', 'rivière', 'fleuve', 'forêt'];

        foreach ($tokens as $token) {
            if (in_array($token, $potentialViewTerms)) {
                $viewRelatedTerms[] = $token;
            }
        }

        return $viewRelatedTerms;
    }

    /**
     * Détermine la catégorie sémantique d'un terme
     */
    private function getCategoryForTerm(string $term): string
    {
        // Vérifier si nous avons déjà classifié ce terme (mise en cache)
        $cachedCategory = Cache::get('term_category_' . $term);
        if ($cachedCategory) {
            return $cachedCategory;
        }

        // Catégoriser avec Hugging Face
        $category = $this->getSemanticCategoryFromHuggingFace($term);

        // Mettre en cache pour éviter des appels API répétés
        Cache::put('term_category_' . $term, $category, $this->cacheTime);

        return $category;
    }

    /**
     * Utilise Hugging Face pour déterminer la catégorie sémantique
     */
    private function getSemanticCategoryFromHuggingFace(string $term): string
    {
        try {
            // Utiliser le modèle de classification de texte
            $modelEndpoint = $this->apiUrl . 'facebook/bart-large-mnli';
            $categories = ['mer', 'lac', 'montagne', 'ville', 'jardin', 'campagne'];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post($modelEndpoint, [
                'inputs' => "Classifie le mot '{$term}' dans la catégorie la plus appropriée.",
                'parameters' => [
                    'candidate_labels' => implode(', ', $categories)
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return $result['labels'][0] ?? $this->fallbackCategorization($term);
            }

            return $this->fallbackCategorization($term);

        } catch (Exception $e) {
            // En cas d'erreur, utiliser la catégorisation de repli
            return $this->fallbackCategorization($term);
        }
    }

    /**
     * Méthode de repli basée sur des règles simples
     */
    private function fallbackCategorization(string $term): string
    {
        $categories = [
            'mer' => ['mer', 'ocean', 'plage', 'maritime', 'côte'],
            'lac' => ['lac', 'étang', 'lagune'],
            'montagne' => ['montagne', 'alpes', 'pyrénées', 'sommet'],
            'ville' => ['ville', 'urbain', 'centre', 'métropole'],
            'jardin' => ['jardin', 'parc', 'verdure', 'floral'],
            'campagne' => ['campagne', 'rural', 'ferme', 'champ'],
        ];

        foreach ($categories as $category => $relatedTerms) {
            if (in_array($term, $relatedTerms)) {
                return $category;
            }
        }

        // Catégorie par défaut si aucune correspondance
        return 'autre';
    }

    /**
     * Génère des suggestions basées sur le contexte sémantique
     */
    public function suggestAlternatives(array $analysisResult): array
    {
        $viewType = $analysisResult['viewType'];
        $suggestions = [];

        if (!$viewType || $viewType == 'autre') {
            // Si aucun type de vue spécifique n'est identifié, suggérer les catégories principales
            return [
                ['type' => 'mer', 'confidence' => 0.8],
                ['type' => 'montagne', 'confidence' => 0.7],
                ['type' => 'lac', 'confidence' => 0.6]
            ];
        }

        // Utiliser Hugging Face pour suggérer des catégories similaires
        $similarCategories = $this->getSimilarCategoriesFromHuggingFace($viewType);

        return $similarCategories;
    }

    /**
     * Utilise Hugging Face pour trouver des catégories sémantiquement proches
     */
    private function getSimilarCategoriesFromHuggingFace(string $category): array
    {
        // Vérifier si nous avons ces suggestions en cache
        $cachedSuggestions = Cache::get('similar_categories_' . $category);
        if ($cachedSuggestions) {
            return $cachedSuggestions;
        }

        try {
            // Créer des paires de termes pour trouver des similitudes sémantiques
            $categories = ['mer', 'lac', 'rivière', 'océan', 'montagne', 'colline', 'vallée', 'forêt', 'jardin', 'parc', 'ville', 'plage', 'campagne'];
            $pairs = [];

            foreach ($categories as $compareCategory) {
                if ($compareCategory != $category) {
                    $pairs[] = [
                        'text1' => $category,
                        'text2' => $compareCategory
                    ];
                }
            }

            // Utiliser un modèle de similarité sémantique
            $similarityEndpoint = $this->apiUrl . $this->modelName;
            $results = [];

            // Traiter par lots pour éviter les limitations de l'API
            $batchSize = 5;
            $batches = array_chunk($pairs, $batchSize);

            foreach ($batches as $batch) {
                $sentences1 = array_column($batch, 'text1');
                $sentences2 = array_column($batch, 'text2');

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_KEY'),
                    'Content-Type' => 'application/json',
                ])->post($similarityEndpoint, [
                    'inputs' => [
                        'source_sentence' => $sentences1,
                        'sentences' => $sentences2
                    ]
                ]);

                if ($response->successful()) {
                    $batchResults = $response->json();

                    // Formater les résultats
                    foreach ($batchResults as $index => $similarity) {
                        $results[] = [
                            'type' => $batch[$index]['text2'],
                            'confidence' => $similarity
                        ];
                    }
                }
            }

            // Trier par confiance et prendre les 3 premiers
            usort($results, function($a, $b) {
                return $b['confidence'] <=> $a['confidence'];
            });

            $topResults = array_slice($results, 0, 3);

            if (!empty($topResults)) {
                // Mettre en cache pour éviter des appels API répétés
                Cache::put('similar_categories_' . $category, $topResults, $this->cacheTime);
                return $topResults;
            }

        } catch (Exception $e) {
            // En cas d'erreur, utiliser des suggestions par défaut
        }

        // Suggestions par défaut
        $defaultSuggestions = [
            'mer' => ['lac', 'rivière', 'océan'],
            'lac' => ['mer', 'rivière', 'étang'],
            'montagne' => ['colline', 'vallée', 'forêt'],
            'ville' => ['centre-ville', 'quartier historique', 'métropole'],
            'jardin' => ['parc', 'forêt', 'nature'],
            'campagne' => ['vallée', 'champ', 'prairie']
        ];

        $suggestions = [];
        $confidence = 0.7;

        if (isset($defaultSuggestions[$category])) {
            foreach ($defaultSuggestions[$category] as $suggestion) {
                $suggestions[] = [
                    'type' => $suggestion,
                    'confidence' => $confidence
                ];
                $confidence -= 0.1;
            }
        }

        return $suggestions;
    }

    /**
     * Génère dynamiquement des synonymes pour un terme donné
     */
    public function findSynonyms(string $term): array
    {
        // Vérifier dans le cache
        $cacheKey = 'synonyms_' . $term;
        $cachedSynonyms = Cache::get($cacheKey);

        if ($cachedSynonyms) {
            return $cachedSynonyms;
        }

        try {
            // Utiliser un modèle de remplissage de masque (fill-mask)
            $fillMaskEndpoint = $this->apiUrl . 'camembert-base';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post($fillMaskEndpoint, [
                'inputs' => "Les synonymes du mot '{$term}' sont: <mask>"
            ]);

            if ($response->successful()) {
                $suggestions = $response->json();
                $synonyms = [];

                // Extraire les propositions pertinentes
                foreach ($suggestions as $item) {
                    if (isset($item['token_str'])) {
                        $candidate = strtolower(trim($item['token_str']));
                        if ($candidate != $term && strlen($candidate) > 2) {
                            $synonyms[] = $candidate;
                        }
                    }
                }

                // Limiter à 5 synonymes maximum
                $synonyms = array_slice($synonyms, 0, 5);

                // Mettre en cache
                Cache::put($cacheKey, $synonyms, $this->cacheTime);

                return $synonyms;
            }
        } catch (Exception $e) {
            // Silencieux en cas d'erreur
        }

        // Synonymes par défaut pour certains termes
        $defaultSynonyms = [
            'mer' => ['océan', 'plage', 'côte'],
            'montagne' => ['alpes', 'sommet', 'relief'],
            'ville' => ['cité', 'métropole', 'urbain'],
            'lac' => ['étang', 'plan d\'eau', 'bassin'],
            'jardin' => ['parc', 'espace vert', 'verdure']
        ];

        return $defaultSynonyms[$term] ?? [];
    }
}