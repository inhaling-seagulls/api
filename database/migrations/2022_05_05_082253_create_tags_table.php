<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        DB::table('tags')->insert(
            array(
                ['name' => 'react'],
                ['name' => 'vue'],
                ['name' => 'angular'],
                ['name' => 'svelte'],
                ['name' => 'symfony'],
                ['name' => 'laravel'],
                ['name' => 'express'],
                ['name' => 'mongo'],
                ['name' => 'mysql'],
                ['name' => 'postgresql'],
                ['name' => 'electron'],
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
}
