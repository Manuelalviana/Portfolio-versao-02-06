<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('categoria_tecnologia', 'estagio_id')) {
            Schema::table('categoria_tecnologia', function (Blueprint $table) {
                $table->foreignId('estagio_id')->nullable()->constrained('estagios')->nullOnDelete();
            });
        }

        DB::table('categoria_tecnologia')
            ->join('tecnologias', 'categoria_tecnologia.tecnologia_id', '=', 'tecnologias.id')
            ->whereNotNull('tecnologias.estagio_id')
            ->update(['categoria_tecnologia.estagio_id' => DB::raw('tecnologias.estagio_id')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('categoria_tecnologia', 'estagio_id')) {
            Schema::table('categoria_tecnologia', function (Blueprint $table) {
                $table->dropConstrainedForeignId('estagio_id');
            });
        }
    }
};
