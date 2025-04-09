<?php

namespace App\Http\Controllers;

use App\Models\PCAReport;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class PCAReportController extends Controller {
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request) {

    $this->authorize('report.view', PCAReport::class);

    $searchQuery = $request->input('search');
    $pca_reports = PCAReport::query()
      ->when($searchQuery, function (Builder $query, string $search) {
        $query->where('name', 'like', "%{$search}%");
      })
      ->latest()
      ->paginate(10);

    return Inertia::render('pca_reports/Index', [
      'pca_reports' => [
        'data' => $pca_reports->items(),
        'meta' => [
          'current_page' => $pca_reports->currentPage(),
          'first_page_url' => $pca_reports->url(1),
          'from' => $pca_reports->firstItem(),
          'last_page' => $pca_reports->lastPage(),
          'last_page_url' => $pca_reports->url($pca_reports->lastPage()),
          'links' => $pca_reports->linkCollection(),
          'next_page_url' => $pca_reports->nextPageUrl(),
          'path' => $pca_reports->path(),
          'per_page' => $pca_reports->perPage(),
          'prev_page_url' => $pca_reports->previousPageUrl(),
          'to' => $pca_reports->lastItem(),
          'total' => $pca_reports->total(),
        ]
      ],
      'filters' => ['search' => $searchQuery]
    ]);

    return Inertia::render('pca_reports/Index', [
      'pca_reports' => $pca_reports,
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request) {
    //
    $this->authorize('report.create', PCAReport::class);
  }

  /**
   * Display the specified resource.
   */
  public function show($id) {
    $this->authorize('report.view', PCAReport::class);
    $report = PCAReport::with('project')->findOrFail($id); // Or handle not found

    return Inertia::render('pca_reports/Show', [
      'pca_report' => $report,
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(PCAReport $PCAReport) {
    $this->authorize('report.edit', PCAReport::class);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, int $id) {
    $this->authorize('report.edit', PCAReport::class);

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
      'numbers_of_floors' => ['nullable', 'numeric'],
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
    $this->authorize('report.delete', PCAReport::class);
  }
}
