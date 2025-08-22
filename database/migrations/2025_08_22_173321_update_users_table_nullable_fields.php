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
            $table->string('logo')->nullable()->change();
            $table->string('username')->nullable()->change();
            $table->enum('role', ['admin', 'store'])->default('store')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('logo')->nullable(false)->change();
            $table->string('username')->nullable(false)->change();
            $table->enum('role', ['admin', 'store'])->nullable(false)->change();
        });
    }
};
