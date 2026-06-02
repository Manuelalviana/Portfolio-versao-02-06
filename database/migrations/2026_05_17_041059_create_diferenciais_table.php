<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTecnologiaDiferencialTable extends Migration
{
    public function up()
    {
        Schema::create('tecnologia_diferencial', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tecnologia_id');
            $table->unsignedBigInteger('diferencial_id');
            $table->unsignedBigInteger('icone_id')->nullable();
            $table->string('descricao_personalizada')->nullable();
            $table->integer('ordem')->default(0);
            $table->timestamps();
            
            $table->foreign('tecnologia_id')
                  ->references('id')
                  ->on('tecnologias')
                  ->onDelete('cascade');
                  
            $table->foreign('diferencial_id')
                  ->references('id')
                  ->on('diferenciais')
                  ->onDelete('cascade');
                  
            $table->foreign('icone_id')
                  ->references('id')
                  ->on('icones')
                  ->onDelete('set null');
                  
            $table->unique(['tecnologia_id', 'diferencial_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('tecnologia_diferencial');
    }
}
