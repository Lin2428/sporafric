<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Permissions des pages Rapport de location / Paramètres location, données aux rôles
     * qui ont déjà accès au rapport de location existant (+ super_admin).
     */
    public function up(): void
    {
        $report   = Permission::findOrCreate('page_RentalReportPage', 'web');
        $settings = Permission::findOrCreate('page_RentalSettingsPage', 'web');

        $roles = Role::query()
            ->where('name', 'super_admin')
            ->orWhereHas('permissions', fn ($q) => $q->where('name', 'page_ReportLocationPage'))
            ->get();

        foreach ($roles as $role) {
            $role->givePermissionTo($report);
        }
        Role::query()->where('name', 'super_admin')->first()?->givePermissionTo($settings);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Permission::query()->whereIn('name', ['page_RentalReportPage', 'page_RentalSettingsPage'])->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
