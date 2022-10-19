<?php

namespace App\Services;

use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\EntitiesServices\Leads;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use App\Services\Traits\AccessTokenManager;
use Illuminate\Support\Facades\Log;

class LeadService extends AmoApiService
{
    use AccessTokenManager;

    private Leads $leadsService;


    /**
     * @throws AmoCRMMissedTokenException
     */
    public function __construct()
    {
        parent::__construct();

        $this->leadsService = $this->apiClient->leads();
    }

    /**
     * @return null|LeadsCollection
     * @throws AmoCRMApiException
     */
    public function getAllLeads(): null|LeadsCollection
    {
        $leadsCollection = null;
        try {
            $leadsCollection = $this->leadsService->get();
            $leadsCollection .= $this->leadsService->nextPage($leadsCollection);
        } catch (AmoCRMApiException $e) {
            Log::info($e->getMessage());
        } finally {
            return $leadsCollection;
        }
    }


}
