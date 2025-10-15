<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->enum('date_type', ['single', 'range', 'multiple'])->default('single')->after('application_body');
            $table->json('specific_dates')->nullable()->after('date_type');
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['subject', 'application_body', 'date_type', 'specific_dates']);
        });
    }
};
