<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateActivityLogTable extends Migration
{
    public function up(): void
    {
        /** @var string|null $connection */
        $connection = config('activitylog.database_connection');
        /** @var string $tableName */
        $tableName = config('activitylog.table_name');

        Schema::connection($connection)->create($tableName, function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject', 'subject');
            $table->nullableMorphs('causer', 'causer');
            $table->json('properties')->nullable();
            $table->timestamps();
            $table->index('log_name');
        });
    }

    public function down(): void
    {
        /** @var string|null $connection */
        $connection = config('activitylog.database_connection');
        /** @var string $tableName */
        $tableName = config('activitylog.table_name');
        Schema::connection($connection)->dropIfExists($tableName);
    }
}
