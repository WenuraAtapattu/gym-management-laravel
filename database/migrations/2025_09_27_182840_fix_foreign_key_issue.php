<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Drop the foreign key constraint if it exists
        if (Schema::hasTable('fitness_classes')) {
            try {
                $connection = DB::getDoctrineSchemaManager();
                $foreignKeys = $connection->listTableForeignKeys('fitness_classes');
                
                foreach ($foreignKeys as $key) {
                    if (in_array('instructor_id', $key->getLocalColumns())) {
                        Schema::table('fitness_classes', function ($table) use ($key) {
                            $table->dropForeign([$key->getLocalColumns()[0]]);
                        });
                        break;
                    }
                }
            } catch (\Exception $e) {
                // Ignore any errors
            }
        }
        
        // We're not dropping the instructors table anymore
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as we don't want to recreate the problematic constraint
    }
};
