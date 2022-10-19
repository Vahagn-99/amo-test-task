<?php

namespace App\Http\Controllers;

use AmoCRM\Exceptions\BadTypeException;
use App\Services\LeadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AmoController extends Controller
{

    public function __construct(private readonly LeadService $amoService)
    {

    }

    /**
     * @throws BadTypeException
     */
    public function amoButton(): void
    {
        $this->amoService->showButton();
    }

    public function getToken(): RedirectResponse
    {

        $this->amoService->setCode(\request('code'));

        return redirect()->route('welcome');
    }


}
