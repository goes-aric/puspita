<?php
namespace App\Http\Resources\Pengeluaran;

use Illuminate\Http\Resources\Json\JsonResource;

class DetailPengeluaranResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'id_pengeluaran_parkir' => $this->id_pengeluaran_parkir,
            'tanggal'               => $this->tanggal,
            'kode_akun'             => $this->kode_akun,
            'nama_akun'             => $this->nama_akun,
            'jumlah_pengeluaran'    => $this->jumlah_pengeluaran
        ];
    }
}
