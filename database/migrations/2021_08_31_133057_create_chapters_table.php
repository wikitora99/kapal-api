<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->integer('number')->unique();
            $table->integer('sequence')->unique();
            $table->string('short_name');
            $table->string('long_name');
            $table->string('transliteration');
            $table->string('translation');
            $table->enum('revelation', ['Makkiyyah', 'Madaniyyah']);
            $table->text('tafsir');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chapters');
    }
}
