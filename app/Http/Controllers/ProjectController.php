<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Inertia\Inertia;

class ProjectController extends Controller {
    public function index() {
        $projects = Project::all();

        return Inertia::render('Dashboard', [
            'projects' => $projects,
        ]);
    }

    public function latestProjects() {
        $latestProjects = Project::latest()->take(3)->get();

        return Inertia::render('Dashboard', [
            'projects' => $latestProjects,
        ]);
    }
}
