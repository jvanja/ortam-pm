<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Organization;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictToOrganization {
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response {
    $user = Auth::user();
    // Check for project or invoice route parameters
    $projectId = $request->route('project');
    $invoice = $request->route('invoice');

    // Resolve the model instance based on the parameter
    if ($projectId) {
      $model = Project::find($projectId);
      if (!$model) {
        abort(404, 'Project not found.');
      }
    } elseif ($invoice) {
      $model = Project::find($invoice->project_id);
      if (!$model) {
        abort(404, 'Invoice not found.');
      }
    } else {
      // No relevant model in the route, proceed
      return $next($request);
    }
    //  TODO:
    // Perhaps restrict only certain routes with this dd($request->route()->getName());
    if (isset($model->organization_id)) {
        $organization = Organization::findOrFail($model->organization_id);
        $request->attributes->add(['organization' => $organization]); // Attach to the request

        // Check if the model belongs to the user's organization
      if (!$user->hasRole('superadmin') && $user->organization_id !== $model->organization_id) {
        abort(403, 'You do not have access to this resource.');
      }
    }

    return $next($request);
  }
}
