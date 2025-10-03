<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Disable foreign key checks for MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        // Drop the problematic foreign key if it exists
        if (Schema::hasTable('reports') && Schema::hasColumn('reports', 'review_id')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->dropForeign(['review_id']);
            });
        }

        // Re-enable foreign key checks for MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    public function down()
    {
        // This is a one-way migration
    }
};