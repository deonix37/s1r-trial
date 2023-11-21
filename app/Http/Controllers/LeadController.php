<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Services\LeadService;

class LeadController extends Controller
{
    public function __construct(
        protected LeadService $leadService,
    ) {}

    public function create()
    {
        return view('lead-create');
    }

    public function store(StoreLeadRequest $request)
    {
        $validated = $request->validated();

        $result = $this->leadService->createLead($validated);

        $response = back()->with([
            'lead_success' => $result['success'],
            'lead_message' => $result['message'],
        ]);

        if (!$result['success']) {
            $response->withInput();
        }

        return $response;
    }
}
