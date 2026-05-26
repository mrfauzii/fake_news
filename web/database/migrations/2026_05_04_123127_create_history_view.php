<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW history_view AS
            SELECT 
                (SELECT ui.id FROM user_interactions ui WHERE ui.request_id = r.id ORDER BY ui.created_at ASC LIMIT 1) AS interaction_id,
                (SELECT ui.user_id FROM user_interactions ui WHERE ui.request_id = r.id ORDER BY ui.created_at ASC LIMIT 1) AS user_id,
                r.id AS request_id, 
                (
                    SELECT u.name 
                    FROM user_interactions ui 
                    JOIN users u ON ui.user_id = u.id 
                    WHERE ui.request_id = r.id 
                    ORDER BY ui.created_at ASC 
                    LIMIT 1
                ) AS username,
                r.input_text,
                r.created_at,
                r.deleted_at,
                r.final_label,
                r.final_confidence,
                r.status
            FROM 
                requests r
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS history_view");
    }
};