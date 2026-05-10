<?php

namespace App\Http\Controllers;

use App\Application\Actions\Tenants\CreateTenant;
use App\Domain\DTOs\RegisterOnTheFlyDTO;
use App\Http\Requests\StoreTenantRequest;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function __construct(public readonly CreateTenant $createTenant) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTenantRequest $request)
    {
        $data = RegisterOnTheFlyDTO::fromValidatable($request);

        $tenant = $this->createTenant->execute($data);

        return response()->json($tenant);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
