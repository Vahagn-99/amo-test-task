<?php

namespace App\Console\Commands;

use AmoCRM\Exceptions\AmoCRMApiException;
use App\Models\Lead;
use App\Services\LeadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetAmoLeadsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @param LeadService $leadService
     * @return void
     * @throws AmoCRMApiException
     */
    public function handle(LeadService $leadService): void
    {
        $leads = (array)$leadService->getAllLeads();
        foreach (array_chunk($leads, 200) as $data) {
            foreach ($data as $item) {
                DB::table('leads')->updateOrInsert([], []);
            }
        }
    }
}
