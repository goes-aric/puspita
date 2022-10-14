<?php
namespace App\Http\Resources\Pendapatan;

use Illuminate\Http\Resources\Json\JsonResource;

class DetailPendapatanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'id_pendapatan_parkir'  => $this->id_pendapatan_parkir,
            'id_kendaraan'          => $this->id_kendaraan,
            'jenis_kendaraan'       => $this->jenis_kendaraan,
            'jumlah_kendaraan'      => $this->jumlah_kendaraan,
            'biaya_parkir'          => $this->biaya_parkir,
            'total'                 => $this->total
        ];
    }
}
