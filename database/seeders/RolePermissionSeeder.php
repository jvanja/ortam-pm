<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder {
  public function run() {
    // Create roles
    $roleSuperAdmin = Role::findOrCreate('superadmin');
    $adminRole = Role::findOrCreate('admin');
    $employeeRole = Role::findOrCreate('employee');
    $receptionistRole = Role::findOrCreate('receptionist');
    $accountantRole = Role::findOrCreate('accountant');
    $clientRole = Role::findOrCreate('client');

    // Create permissions
    $permissions = [
      [
        'group_name' => 'dashboard',
        'permissions' => [
          'dashboard.view',
          'dashboard.edit',
        ]
      ],
      [
        'group_name' => 'admin',
        'permissions' => [
          'admin.create',
          'admin.view',
          'admin.edit',
          'admin.delete',
          'admin.approve',
        ]
      ],
      [
        'group_name' => 'role',
        'permissions' => [
          'role.create',
          'role.view',
          'role.edit',
          'role.delete',
          'role.approve',
        ]
      ],
      [
        'group_name' => 'profile',
        'permissions' => [
          'profile.view',
          'profile.edit',
          'profile.delete',
          'profile.update',
        ]
      ],
      [
        'group_name' => 'quote',
        'permissions' => [
          'quote.create',
          'quote.view',
          'quote.edit',
          'quote.delete',
          'quote.approve',
        ]
      ],
      [
        'group_name' => 'project',
        'permissions' => [
          'project.create',
          'project.view',
          'project.edit',
          'project.delete',
          'project.approve',
        ]
      ],
      [
        'group_name' => 'timesheet',
        'permissions' => [
          'timesheet.create',
          'timesheet.view',
          'timesheet.edit',
          'timesheet.delete',
          'timesheet.approve',
        ]
      ],
      [
        'group_name' => 'client',
        'permissions' => [
          'client.create',
          'client.view',
          'client.edit',
          'client.delete',
          'client.approve',
        ]
      ],
    ];

    // - Admin : can access and edit everything and can approve time sheets
    // - Receptionist : can access and edit everything but cannot approve time sheets
    // - Employee : can only access Projects, Clients, Quotes and can edit timesheet and space reserved to clients (they will upload files there)
    // - Accountant : can only access Projects, Clients, Quotes, space reserved to clients and can edit timesheet
    // - Client : can only access their space reserved and download files

    // Create and Assign Permissions
    for ($i = 0; $i < count($permissions); $i++) {
      $permissionGroup = $permissions[$i]['group_name'];
      for ($j = 0; $j < count($permissions[$i]['permissions']); $j++) {
        $permissionExist = Permission::where('name', $permissions[$i]['permissions'][$j])->first();
        if (is_null($permissionExist)) {
          $permission = Permission::create(
            [
              'name' => $permissions[$i]['permissions'][$j],
              // 'group_name' => $permissionGroup,
              // 'guard_name' => 'admin'
            ]
          );
          $roleSuperAdmin->givePermissionTo($permission);
        }
      }
    }
    // Assign permissions to roles
    // $adminRole->givePermissionTo($editPermission, $viewPermission);
    // $employeeRole->givePermissionTo($viewPermission);

    // Assign role to user
    $user = User::find(1); // Example user with ID 1
    $user->assignRole('superadmin');
  }
}
