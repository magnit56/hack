<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id(); // Создает поле id
            $table->string('title'); // Поле для названия работы
            $table->text('description'); // Поле для описания работы
            $table->string('company'); // Поле для названия компании
            $table->string('location')->nullable(); // Поле для местоположения (опционально)
            $table->timestamps(); // Создает поля created_at и updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
