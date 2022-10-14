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
        Schema::create('detail_pengeluaran_parkir', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_pengeluaran_parkir')->unsigned();
            $table->foreign('id_pengeluaran_parkir')->references('id')->on('pengeluaran_parkir')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('kode_akun', 150);
            $table->string('nama_akun', 255);
            $table->decimal('jumlah_pengeluaran', 12,2);
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
        Schema::dropIfExists('detail_pengeluaran_parkir');
    }
};
