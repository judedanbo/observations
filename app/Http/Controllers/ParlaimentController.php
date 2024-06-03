<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParliamentRequest;
use App\Http\Requests\UpdateParliamentRequest;
use App\Models\Parliament;

class ParliamentController extends Controller
{
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
    public function store(StoreParliamentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Parliament $parliament)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Parliament $parliament)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParliamentRequest $request, Parliament $parliament)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Parliament $parliament)
    {
        //
    }
}
