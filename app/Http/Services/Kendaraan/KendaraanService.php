<?php
namespace App\Http\Services\Kendaraan;

use Exception;
use App\Models\Kendaraan;
use App\Http\Services\BaseService;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Kendaraan\KendaraanResource;

class KendaraanService extends BaseService
{
    /* PRIVATE VARIABLE */
    private $kendaraanModel;

    public function __construct()
    {
        $this->kendaraanModel = new Kendaraan();
    }

    /* FETCH ALL KENDARAAN */
    public function fetchLimit($props){
        /* GET DATA FOR PAGINATION AS A MODEL */
        $getAllData = $this->dataFilterPagination($this->kendaraanModel, [], null);
        $totalData = $getAllData->count();

        /* GET DATA WITH FILTER FOR PAGINATION AS A MODEL */
        $getFilterData = $this->dataFilterPagination($this->kendaraanModel, $props, null);
        $totalFiltered = $getFilterData->count();

        /* GET DATA WITH FILTER AS A MODEL */
        $datas = $this->dataFilter($this->kendaraanModel, $props, null);

        /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
        $datas = $datas->get();
        $datas = KendaraanResource::collection($datas);
        $kendaraan = [
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

        return $kendaraan;
    }

    /* FETCH KENDARAAN BY ID */
    public function fetchById($id){
        try {
            $kendaraan = $this->kendaraanModel::find($id);
            if ($kendaraan) {
                $kendaraan = KendaraanResource::make($kendaraan);
                return $kendaraan;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW KENDARAAN */
    public function createKendaraan($props){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $kendaraan = $this->kendaraanModel;
            $kendaraan->jenis_kendaraan = $props['jenis_kendaraan'];
            $kendaraan->biaya_parkir    = $props['biaya_parkir'];
            $kendaraan->id_user         = $this->returnAuthUser()->id;
            $kendaraan->save();

            /* COMMIT DB TRANSACTION */
            DB::commit();

            $kendaraan = KendaraanResource::make($kendaraan);
            return $kendaraan;
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            throw $ex;
        }
    }

    /* UPDATE KENDARAAN */
    public function updateKendaraan($props, $id){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $kendaraan = $this->kendaraanModel::find($id);
            if ($kendaraan) {
                /* UPDATE KENDARAAN */
                $kendaraan->jenis_kendaraan = $props['jenis_kendaraan'];
                $kendaraan->biaya_parkir    = $props['biaya_parkir'];
                $kendaraan->id_user         = $this->returnAuthUser()->id;
                $kendaraan->update();

                /* COMMIT DB TRANSACTION */
                DB::commit();

                $kendaraan = KendaraanResource::make($kendaraan);
                return $kendaraan;
            } else {
                throw new Exception('Catatan tidak ditemukan!');
            }
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            throw $ex;
        }
    }

    /* DESTROY KENDARAAN */
    public function destroyKendaraan($id){
        try {
            $kendaraan = $this->kendaraanModel::find($id);
            if ($kendaraan) {
                $kendaraan->delete();

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* DESTROY SELECTED / MULTIPLE KENDARAAN */
    public function destroyMultipleKendaraan($props){
        try {
            $kendaraan = $this->kendaraanModel::whereIn('id', $props);

            if ($kendaraan->count() > 0) {
                $kendaraan->delete();

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL KENDARAAN FOR OPTIONS */
    public function fetchDataOptions($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->kendaraanModel, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $kendaraan = $datas->select('id', 'jenis_kendaraan', 'biaya_parkir')->get();

            return $kendaraan;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
