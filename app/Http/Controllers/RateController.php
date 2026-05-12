<?php

namespace App\Http\Controllers;

use App\Enums\RateFrequency;
use App\Enums\RateTypeScope;
use App\Models\Rate;
use App\Models\RateType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(?string $account = null)
    {
        $rates = Rate::with(['rateType', 'organization', 'project', 'user'])
            ->active()
            ->get();

        return Inertia::render('Rate/Index', [
            'rates' => $rates,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(?string $account = null)
    {
        return Inertia::render('Rate/Create', [
            'rateTypes' => RateType::all(),
            'frequenciesOptions' => RateFrequency::toSelectOptions(),
            'scopesOptions' => RateTypeScope::toSelectOptions(),
            'organizations' => Auth::user()->organizations,
            'projects' => Auth::user()->projects,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ?string $account = null)
    {
        $validated = $request->validate([
            'rate_type_id' => ['nullable', 'exists:tenant.rate_types,id'],
            'rate_frequency' => 'required|string',
            'organization_id' => 'nullable|exists:tenant.organizations,id',
            'project_id' => 'nullable|exists:tenant.projects,id',
            'user_id' => 'nullable|exists:tenant.users,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'overtime_multiplier' => 'numeric|min:1',
            'holiday_multiplier' => 'numeric|min:1',
            'special_multiplier' => 'numeric|min:1',
            'custom_multiplier_rate' => 'nullable|numeric|min:1',
            'custom_multiplier_label' => 'nullable|string',
            'is_default' => 'boolean',
            'effective_from' => 'nullable|date',
            'effective_until' => 'nullable|date|after:effective_from',
        ]);

        Rate::create($validated);

        return redirect()
            ->route('rate.index')
            ->with('success', 'Rate created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(?string $account, Rate $rate)
    {
        $rate->load(['rateType', 'organization', 'project', 'user']);

        return Inertia::render('Rate/Show', [
            'rate' => $rate,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(?string $account, Rate $rate)
    {
        $rate->load(['rateType', 'organization', 'project', 'user']);

        return Inertia::render('Rate/Edit', [
            'rate' => $rate,
            'frequencies' => RateFrequency::toSelectOptions(),
            'scopes' => RateTypeScope::toSelectOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ?string $account, Rate $rate)
    {
        $validated = $request->validate([
            'rate_type_id' => 'required|exists:rate_types,id',
            'rate_frequency' => 'required|string',
            'organization_id' => 'nullable|exists:organizations,id',
            'project_id' => 'nullable|exists:projects,id',
            'user_id' => 'nullable|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'overtime_multiplier' => 'numeric|min:1',
            'holiday_multiplier' => 'numeric|min:1',
            'special_multiplier' => 'numeric|min:1',
            'custom_multiplier_rate' => 'nullable|numeric|min:1',
            'custom_multiplier_label' => 'nullable|string',
            'is_default' => 'boolean',
            'effective_from' => 'nullable|date',
            'effective_until' => 'nullable|date|after:effective_from',
        ]);

        $rate->update($validated);

        return redirect()
            ->route('rate.index')
            ->with('success', 'Rate updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(?string $account, Rate $rate)
    {
        try {
            $rate->delete();

            return redirect()
                ->route('rate.index')
                ->with('success', 'Rate deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('rate.index')
                ->with('error', 'Unable to delete rate.');
        }
    }
}
