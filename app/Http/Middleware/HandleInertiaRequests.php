<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Organization;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware {
  /**
   * The root template that's loaded on the first page visit.
   *
   * @see https://inertiajs.com/server-side-setup#root-template
   *
   * @var string
   */
  protected $rootView = 'app';

  /**
   * Determines the current asset version.
   *
   * @see https://inertiajs.com/asset-versioning
   */
  public function version(Request $request): ?string {
    return parent::version($request);
  }

  /**
   * Define the props that are shared by default.
   *
   * @see https://inertiajs.com/shared-data
   *
   * @return array<string, mixed>
   */
  public function share(Request $request): array {
    if ($request->user()) {
      $organization_id =  $request->user()->organization_id;
      if ($organization_id) {
        $organization = Organization::find($organization_id)->name;
      } else {
        $organization = false;
      }
    } else {
      $organization = env('APP_NAME');
    }
    return [
      ...parent::share($request),
      'name' => config('app.name'),
      'user' => env('USERNAME'),
      'password' => env('PASSWORD'),
      'organization' => $organization,
      'auth' => [
        'user' => $request->user(),
        'roles' => $request->user() ? $request->user()->roles->pluck('name') : [],
        'permissions' => $request->user() ? $request->user()->getPermissionsViaRoles()->pluck('name') : [],
      ],
      'ziggy' => function () use ($request) {
        return array_merge((new Ziggy)->toArray(), [
          'location' => $request->url(),
          'query' => $request->query()
        ]);
      },
    ];
  }
}
