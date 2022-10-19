<?php

namespace App\Http\Controllers;

use App\Services\LeadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AmoController extends Controller
{
    public function __construct(private LeadService $amoService)
    {

    }

    public function amoButton(): void
    {
        $this->amoService->showButton();
    }

    public function getToken(): RedirectResponse
    {
        $this->amoService->setCode(\request('code', null));

        return redirect()->route('welcome');
    }


}
