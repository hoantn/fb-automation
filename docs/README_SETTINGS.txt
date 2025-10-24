FB Automation – Settings Pack (normalized)
===============================================

Files included:
- database/migrations/2025_10_24_000000_rebuild_settings_table.php
- app/Models/Setting.php
- app/Http/Controllers/Admin/SettingController.php
- resources/views/admin/settings/index.blade.php

Routes to add into routes/web.php (inside your admin group):
------------------------------------------------------------
use App\Http\Controllers\Admin\SettingController;
Route::prefix('admin')->middleware(['web','auth'])->group(function () {
    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('admin.settings.update');
});

How to install:
---------------
1) Unzip contents to your Laravel project root (allow overwrite).
2) Run migrations:
   php artisan migrate
3) Clear caches (recommended):
   php artisan config:clear && php artisan route:clear && php artisan cache:clear && php artisan view:clear
4) Visit /admin/settings

This migration will rebuild the `settings` table if it finds a non-standard schema,
attempting to copy any existing data, and then replace the old table safely.
