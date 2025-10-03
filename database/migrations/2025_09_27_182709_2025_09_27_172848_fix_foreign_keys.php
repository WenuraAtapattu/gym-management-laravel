<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite' && Schema::hasTable('fitness_classes')) {
            Schema::table('fitness_classes', function (Blueprint $table) {
                if (DB::getDriverName() === 'mysql') {
                    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                    $table->dropForeign(['instructor_id']);
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                } else if (in_array(DB::getDriverName(), ['pgsql', 'mysql', 'sqlsrv'])) {
                    $table->dropForeign(['instructor_id']);
                }
            });
        }
    }

    public function down(): void
    {
        // This migration is not reversible
    }
};
