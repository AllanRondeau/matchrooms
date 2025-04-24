<?php

namespace App\Http\Services;

use App\Http\Services\SemanticEnrichmentService;
use Illuminate\Console\Command;

class EnrichHotelsCommand extends Command
{
    protected $signature = 'hotels:enrich';
    protected $description = 'Enrichit les hôtels avec des données sémantiques via Hugging Face';

    protected $enrichmentService;

    public function __construct(SemanticEnrichmentService $enrichmentService)
    {
        parent::__construct();
        $this->enrichmentService = $enrichmentService;
    }

    public function handle()
    {
        $this->info('Début de l\'enrichissement sémantique des hôtels...');

        $count = $this->enrichmentService->enrichAllHotels();

        $this->info($count . ' hôtels ont été enrichis avec succès.');

        return Command::SUCCESS;
    }
}