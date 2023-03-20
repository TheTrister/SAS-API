<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->string('NIS');
            $table->string('NAMA');
            $table->string('JENIS_KELAMIN');
            $table->date('TANGGAL_LAHIR');
            $table->integer('ID_JURUSAN');
            $table->integer('ID_KELAS');
            $table->string('NO_HP');
            $table->string('IMEI');
            $table->string('PASSWORD');
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
        Schema::dropIfExists('siswas');
    }
};
