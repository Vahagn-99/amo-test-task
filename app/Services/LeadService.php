<?php

namespace App\Services;

use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\EntitiesServices\Leads;
use AmoCRM\Exceptions\AmoCRMApiException;
use App\Services\Traits\AccessTokenManager;
use Illuminate\Support\Facades\Log;
use JsonException;


class LeadService extends AmoApiService
{
    use AccessTokenManager;

    /** @var  Leads $leads */
    private Leads $leadsService;

    /**
     * @return null|LeadsCollection
     */
    public function getAllLeads(): null|LeadsCollection
    {
        $leadsCollection = null;
        try {
            $leadsService = $this->authClient()->leads();
            $leadsCollection = $leadsService->get();
            $leadsCollection .= $leadsService->nextPage($leadsCollection);
        } catch (AmoCRMApiException|JsonException  $e) {
            Log::info($e->getMessage());
        } finally {
            return $leadsCollection;
        }
    }


}
