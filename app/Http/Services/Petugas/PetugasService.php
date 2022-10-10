<?php
namespace App\Http\Services\Petugas;

use Exception;
use App\Models\Akun;
use App\Http\Services\BaseService;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Akun\AkunResource;

class PetugasService extends BaseService
{
    /* PRIVATE VARIABLE */
    private $petugasModel;

    public function __construct()
    {
        $this->petugasModel = new Petugas();
    }

    /* FETCH ALL PETUGAS */
    public function fetchLimit($props){
        /* GET DATA FOR PAGINATION AS A MODEL */
        $getAllData = $this->dataFilterPagination($this->petugasModel, [], null);
        $totalData = $getAllData->count();

        /* GET DATA WITH FILTER FOR PAGINATION AS A MODEL */
        $getFilterData = $this->dataFilterPagination($this->petugasModel, $props, null);
        $totalFiltered = $getFilterData->count();

        /* GET DATA WITH FILTER AS A MODEL */
        $datas = $this->dataFilter($this->petugasModel, $props, null);

        /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
        $datas = $datas->get();
        $datas = AkunResource::collection($datas);
        $petugas = [
            "total" => $totalData,
            "total_filter" => $totalFiltered,
            "per_page" => $props['take'],
            "current_page" => $props['skip'] == 0 ? 1 : ($props['skip'] + 1),
            "last_page" => ceil($totalFiltered / $props['take']),
            "from" => $totalFiltered === 0 ? 0 : ($props['skip'] != 0 ? ($props['skip'] * $props['take']) + 1 : 1),
            "to" => $totalFiltered === 0 ? 0 : ($props['skip'] * $props['take']) + $datas->count(),
            "show" => [
                ["number" => 25, "name" => "25"], ["number" => 50, "name" => "50"], ["number" => 100, "name" => "100"]
            ],
            "data" => $datas
        ];

        return $petugas;
    }

    /* FETCH PETUGAS BY ID */
    public function fetchById($id){
        try {
            $petugas = $this->petugasModel::find($id);
            if ($petugas) {
                $petugas = AkunResource::make($petugas);
                return $petugas;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW PETUGAS */
    public function createPetugas($props){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $petugas = $this->petugasModel;
            $petugas->kode_akun    = $props['kode_akun'];
            $petugas->nama_akun    = $props['nama_akun'];
            $petugas->akun_utama   = $props['akun_utama'];
            $petugas->tipe_akun    = $props['tipe_akun'];
            $petugas->created_id   = $this->returnAuthUser()->id;
            $petugas->save();

            /* COMMIT DB TRANSACTION */
            DB::commit();

            $petugas = AkunResource::make($petugas);
            return $petugas;
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            throw $ex;
        }
    }

    /* UPDATE PETUGAS */
    public function updatePetugas($props, $id){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $petugas = $this->petugasModel::find($id);
            if ($petugas) {
                /* UPDATE PETUGAS */
                $petugas->kode_akun    = $props['kode_akun'];
                $petugas->nama_akun    = $props['nama_akun'];
                $petugas->akun_utama   = $props['akun_utama'];
                $petugas->tipe_akun    = $props['tipe_akun'];
                $petugas->updated_id   = $this->returnAuthUser()->id;
                $petugas->update();

                /* COMMIT DB TRANSACTION */
                DB::commit();

                $petugas = AkunResource::make($petugas);
                return $petugas;
            } else {
                throw new Exception('Catatan tidak ditemukan!');
            }
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            throw $ex;
        }
    }

    /* DESTROY PETUGAS */
    public function destroyPetugas($id){
        try {
            $petugas = $this->petugasModel::find($id);
            if ($petugas) {
                $petugas->delete();

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* DESTROY SELECTED / MULTIPLE PETUGAS */
    public function destroyMultiplePetugas($props){
        try {
            $petugas = $this->petugasModel::whereIn('id', $props);

            if ($petugas->count() > 0) {
                $petugas->delete();

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL PETUGAS FOR OPTIONS */
    public function fetchDataOptions($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->petugasModel, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $petugas = $datas->select('id', 'kode_akun', 'nama_akun')->get();

            return $petugas;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
