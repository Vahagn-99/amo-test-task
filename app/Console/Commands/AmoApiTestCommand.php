<?php

namespace App\Console\Commands;

use App\Services\AmoApiService;
use App\Services\LeadService;
use Illuminate\Console\Command;

class AmoApiTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amo:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(LeadService $amo): void
    {
        $amo->authClient();
        $leads = $amo->getAllLeads();

        dd($leads);
    }
}
