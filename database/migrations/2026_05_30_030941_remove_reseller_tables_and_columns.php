<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite has issues with dropping foreign keys easily in older versions, 
        // but Laravel handles dropping columns gracefully in recent versions.
        
        Schema::table('stores', function (Blueprint $table) {
            // Check if foreign key exists (if MySQL) or just dropColumn which might handle it
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['reseller_id']);
            }
            $table->dropColumn('reseller_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['reseller_id']);
            }
            $table->dropColumn('reseller_id');
        });

        Schema::table('import_logs', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['reseller_id']);
            }
            $table->dropColumn('reseller_id');
        });

        Schema::dropIfExists('resellers');
        Schema::dropIfExists('reseller_tiers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu direverse karena ini adalah destructive purge
    }
};
