<?php

namespace App\Http\Controllers\Organization;

use App\Enums\OrganizationType;
use App\Http\Controllers\HybridController;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrganizationController extends HybridController
{
    /**
     * Display a listing of the resource.
     */
    public function index(?string $account = null)
    {
        $organizations = Auth::user()->organizations;

        return Inertia::render('organizations/Index', [
            'organizations' => $organizations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(?string $account = null)
    {
        return Inertia::render('organizations/Create', [
            'organizationTypeOptions' => OrganizationType::toSelectOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganizationRequest $request, ?string $account = null)
    {

        $validated = $request->validated();
        Auth::user()->organizations()->create($validated);

        if ($this->shouldRedirectBack()) {
            return back()->with('success', 'Organization created.');
        }

        return redirect()->route('organization.index')
            ->with('success', 'Organization created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(?string $account, Organization $organization)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(?string $account, Organization $organization)
    {
        return Inertia::render('organizations/Edit', [
            'organization' => $organization,
            'organizationTypeOptions' => OrganizationType::toSelectOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganizationRequest $request, ?string $account, Organization $organization)
    {
        $validated = $request->validated();
        $organization->update($validated);

        return $this->renderDetached('organizations/Index', ['organizations' => Auth::user()->organizations])->with('success', 'Organization updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(?string $account, Organization $organization)
    {
        try {
            $organization->delete();

            return redirect()->route('organization.index')->with('success', 'Organization deleted.');
        } catch (\Exception $e) {
            return redirect()->route('organization.index')->with('error', 'Organization cannot be deleted.');
        }
    }
}
