<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Inertia\Inertia;

class DashboardController extends Controller {
    public function show() {
        $latestProjects = Project::latest()->take(3)->get();

        return Inertia::render('Dashboard', [
            'projects' => $latestProjects,
        ]);
    }
}
