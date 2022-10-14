<?php
namespace App\Http\Resources\Kendaraan;

use Illuminate\Http\Resources\Json\JsonResource;

class KendaraanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'jenis_kendaraan'   => $this->jenis_kendaraan,
            'biaya_parkir'      => $this->biaya_parkir,
            'created_user'      => $this->createdUser->nama_user,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at
        ];
    }
}
