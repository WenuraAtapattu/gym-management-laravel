<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('reports')) {
            Schema::table('reports', function (Blueprint $table) {
                if (DB::getDriverName() === 'mysql') {
                    // For MySQL
                    $table->dropForeign(['review_id']);
                } else if (DB::getDriverName() === 'pgsql') {
                    // For PostgreSQL
                    $table->dropForeign('reports_review_id_foreign');
                }
            });
        }
    }

    public function down()
    {
        // This is a destructive migration, so we won't implement down()
    }
};