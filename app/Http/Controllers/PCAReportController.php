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
  // public function show(PCAReport $PCAReport) {
  public function show($id) {
    $report = PCAReport::findOrFail($id); // Or handle not found

    return Inertia::render('pca_reports/Show', [
      'pca_report' => $report,
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(PCAReport $PCAReport) {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, int $id) {
    $validatedData = $request->validate([
      'name' => 'required',
      'occupation_of_the_property' => 'required',
      'total_site_area' => ['required', 'numeric'],
      'surface_area_of_the_building' => ['required', 'numeric'],
      'occupation_of_the_building' => ['required', 'numeric'],
      'year_of_construction' => ['required', 'digits:4'],
      'structure' => 'required',
      'fondation' => 'required',
      'building' => 'nullable',
      'numbers_of_floors' => ['nullable', 'numberic'],
      'basement' => 'boolean',
      'residential_units' => ['nullable', 'numeric'],
      'non_residential_units' => ['nullable', 'numeric'],
    ]);

    $pcaReport = PCAReport::findOrFail($id);
    $pcaReport->fill($validatedData)->save();
    return redirect()->back()->with('success', 'PCA Report updated successfully!');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(PCAReport $PCAReport) {
    //
  }
}
