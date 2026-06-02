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
        Schema::table('diferenciais', function (Blueprint $table) {
            if (! Schema::hasColumn('diferenciais', 'id_idioma')) {
                $table->foreignId('id_idioma')->nullable()->constrained('idiomas', 'id')->nullOnDelete()->after('descricao');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diferenciais', function (Blueprint $table) {
            if (Schema::hasColumn('diferenciais', 'id_idioma')) {
                $table->dropConstrainedForeignId('id_idioma');
            }
        });
    }
};
