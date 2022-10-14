<?php
namespace App\Http\Resources\Pengeluaran;

use Illuminate\Http\Resources\Json\JsonResource;

class PengeluaranResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'bulan'         => $this->bulan,
            'tahun'         => $this->tahun,
            'grand_total'   => $this->grand_total,
            'details'       => $this->details,
            'created_user'  => $this->createdUser->nama_user,
        ];
    }
}
