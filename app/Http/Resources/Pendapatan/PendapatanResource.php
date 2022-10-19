<?php
namespace App\Http\Resources\Pendapatan;

use Illuminate\Http\Resources\Json\JsonResource;

class PendapatanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'tanggal'           => $this->tanggal,
            'grand_total'       => $this->grand_total,
            'gambar'            => $this->gambar ? asset('/storage/images') . '/' . $this->gambar : null,
            'details'           => $this->details,
            'created_user'      => $this->createdUser->nama_user,
        ];
    }
}
