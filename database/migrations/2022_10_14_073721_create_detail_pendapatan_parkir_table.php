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
        Schema::create('detail_pendapatan_parkir', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_pendapatan_parkir')->unsigned();
            $table->foreign('id_pendapatan_parkir')->references('id')->on('pendapatan_parkir')->onDelete('cascade');
            $table->bigInteger('id_kendaraan')->unsigned();
            $table->foreign('id_kendaraan')->references('id')->on('kendaraan')->onDelete('cascade');
            $table->string('jenis_kendaraan', 255);
            $table->integer('jumlah_kendaraan');
            $table->decimal('biaya_parkir', 12,2);
            $table->decimal('total', 12,2);
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
        Schema::dropIfExists('detail_pendapatan_parkir');
    }
};
