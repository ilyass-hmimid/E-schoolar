<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilieresTable extends Migration
{
    public function up()
    {
        Schema::create('filieres', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->foreignId('niveau_id')->constrained()->onDelete('cascade');
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('filieres');
    }
}
