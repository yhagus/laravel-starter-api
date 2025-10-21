<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /** @var string|null $connection */
        $connection = config('activitylog.database_connection');
        /** @var string $tableName */
        $tableName = config('activitylog.table_name');

        Schema::connection($connection)->table($tableName, function (Blueprint $table): void {
            $table->string('event')->nullable()->after('subject_type');
        });
    }

    public function down(): void
    {
        /** @var string|null $connection */
        $connection = config('activitylog.database_connection');
        /** @var string $tableName */
        $tableName = config('activitylog.table_name');

        Schema::connection($connection)->table($tableName, function (Blueprint $table): void {
            $table->dropColumn('event');
        });
    }
};
