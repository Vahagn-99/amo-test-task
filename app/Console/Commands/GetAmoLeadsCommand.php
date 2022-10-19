<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Lead;
use App\Services\LeadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;

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
     */
    public function handle(LeadService $leadService): void
    {
        $leads = $leadService->getAllLeads();
        $chunks = array_chunk((array)$leads, 200);

        foreach ($chunks as $data) {
            foreach ($data[0] as $item) {
                $this->saveToDbByTransaction($item);
            }
        }
    }

    private function saveToDbByTransaction($item): void
    {
        DB::transaction(static function () use ($item) {
            try {
                /** @var Lead $lead */
                $lead = Lead::updateOrCreate(['lead_id' => $item->id], [
                    'name' => $item->name,
                    'price' => $item->price ?? null,
                    'is_deleted' => $item->isDeleted,
                ]);

                if (!is_null($item->contacts)) {
                    foreach ($item->contacts as $contact) {
                        $lead->contacts()->updateOrCreate(['contact_id' => $contact->id], [
                            'name' => $contact->name ?? '',
                            'email' => $contact->email ?? '',
                            'phone' => $contact->phone ?? '',
                        ]);
                    }
                }

                if (!is_null($item->company)) {
                    $company = Company::updateOrCreate(['company_id' => $item->company->id], [
                        'name' => $item->company->name ?? '',
                    ]);
                    $lead->update(['company_id' => $company->id]);
                }

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                Log::info($e);
            }
        });
    }
}
