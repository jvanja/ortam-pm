<?php

namespace App\Http\Controllers;

use App\Models\PCAReport;
use Inertia\Inertia;
use Illuminate\Http\Request;

class PCAReportController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {

        $pca_reports = PCAReport::all();

        return Inertia::render('pca_reports/Index', [
            'pca_reports' => $pca_reports,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PCAReport $pCAReport) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PCAReport $pCAReport) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PCAReport $pCAReport) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PCAReport $pCAReport) {
        //
    }
}
