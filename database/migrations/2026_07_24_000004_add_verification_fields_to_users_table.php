<?php

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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('age')->nullable()->after('years_experience');
            $table->string('diploma_file_path')->nullable()->after('diplomas');
            $table->unsignedTinyInteger('technician_verification_score')->nullable()->after('longitude');
            $table->unsignedTinyInteger('technician_verification_attempts')->default(0)->after('technician_verification_score');
            $table->timestamp('technician_verified_at')->nullable()->after('technician_verification_attempts');
            $table->index(['role', 'technician_verified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'technician_verified_at']);
            $table->dropColumn([
                'age',
                'diploma_file_path',
                'technician_verification_score',
                'technician_verification_attempts',
                'technician_verified_at',
            ]);
        });
    }
};
