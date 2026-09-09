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
            $table->string('role', 20)->default('user')->after('password')->index();
            $table->string('specialty')->nullable()->after('role');
            $table->string('country', 120)->nullable()->after('specialty');
            $table->string('state', 120)->nullable()->after('country');
            $table->unsignedTinyInteger('years_experience')->nullable()->after('state');
            $table->string('studies', 500)->nullable()->after('years_experience');
            $table->string('diplomas', 500)->nullable()->after('studies');
            $table->string('address')->nullable()->after('diplomas');
            $table->decimal('latitude', 10, 7)->nullable()->after('address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->index(['role', 'country', 'state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'country', 'state']);
            $table->dropColumn([
                'role',
                'specialty',
                'country',
                'state',
                'years_experience',
                'studies',
                'diplomas',
                'address',
                'latitude',
                'longitude',
            ]);
        });
    }
};
