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
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->change();
            $table->decimal('price', 8, 2)->unsigned()->change();
            $table->decimal('cost', 8, 2)->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('quantity')->change();
            $table->decimal('price', 8, 2)->change();
            $table->decimal('cost', 8, 2)->nullable()->change();
        });
    }
};
