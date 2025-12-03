<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table): void {
            $table->dropMorphs('subject', 'subject');
            $table->dropMorphs('causer', 'causer');

            $table->nullableUlidMorphs('subject', 'subject');
            $table->nullableUlidMorphs('causer', 'causer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table): void {
            $table->dropMorphs('subject');
            $table->dropMorphs('causer');

            $table->nullableMorphs('subject', 'subject');
            $table->nullableMorphs('causer', 'causer');
        });
    }
};
