<?php

namespace App\Http\Middleware;

use App\Models\Invoice;
use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictToOrganization {
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response {
    $user = auth()->user();
    // Check for project or invoice route parameters
    $projectId = $request->route('project');
    $invoiceId = $request->route('invoice');

    // Resolve the model instance based on the parameter
    if ($projectId) {
      $model = Project::find($projectId);
      if (!$model) {
        abort(404, 'Project not found.');
      }
    } elseif ($invoiceId) {
      $model = Invoice::find($invoiceId);
      if (!$model) {
        abort(404, 'Invoice not found.');
      }
    } else {
      // No relevant model in the route, proceed
      return $next($request);
    }

    // Check if the model belongs to the user's organization
    if (!$user->hasRole('superadmin') && $user->organization_id !== $model->organization_id) {
      abort(403, 'You do not have access to this resource.');
    }

    return $next($request);
  }
}
