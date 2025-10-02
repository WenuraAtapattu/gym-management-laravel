<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixDuplicatePersonalAccessTokens extends Migration
{
    public function up()
    {
        // Mark the migration as completed if the table exists
        if (Schema::hasTable('personal_access_tokens')) {
            DB::table('migrations')->insert([
                'migration' => '2019_12_14_000001_create_personal_access_tokens_table',
                'batch' => 1
            ]);
        }
    }

    public function down()
    {
        // Optional: Add code to reverse the migration if needed
    }
}