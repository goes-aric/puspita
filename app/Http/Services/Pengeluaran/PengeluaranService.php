<?php
namespace App\Http\Services\Pengeluaran;

use Exception;
use App\Models\Pengeluaran;
use App\Http\Services\BaseService;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Pengeluaran\PengeluaranResource;

class PengeluaranService extends BaseService
{
    /* PRIVATE VARIABLE */
    private $pengeluaranModel;
    private $carbon;

    public function __construct()
    {
        $this->pengeluaranModel = new Pengeluaran();
        $this->carbon = $this->returnCarbon();
    }

    /* FETCH ALL PENGELUARAN */
    public function fetchLimit($props){
        /* GET DATA FOR PAGINATION AS A MODEL */
        $getAllData = $this->dataFilterPagination($this->pengeluaranModel, [], null);
        $totalData = $getAllData->count();

        /* GET DATA WITH FILTER FOR PAGINATION AS A MODEL */
        $getFilterData = $this->dataFilterPagination($this->pengeluaranModel, $props, null);
        $totalFiltered = $getFilterData->count();

        /* GET DATA WITH FILTER AS A MODEL */
        $datas = $this->dataFilter($this->pengeluaranModel, $props, null);

        /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
        $datas = $datas->get();
        $datas = PengeluaranResource::collection($datas);
        $pengeluaran = [
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

        return $pengeluaran;
    }

    /* FETCH PENGELUARAN BY ID */
    public function fetchById($id){
        try {
            $pengeluaran = $this->pengeluaranModel::find($id);
            if ($pengeluaran) {
                $pengeluaran = PengeluaranResource::make($pengeluaran);
                return $pengeluaran;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW PENGELUARAN */
    public function createPengeluaran($props){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $pengeluaran = $this->pengeluaranModel;
            $pengeluaran->bulan     = $props['bulan'];
            $pengeluaran->tahun     = $props['tahun'];
            $pengeluaran->id_user   = $this->returnAuthUser()->id;
            $pengeluaran->save();

            /* COMMIT DB TRANSACTION */
            DB::commit();

            $pengeluaran = PengeluaranResource::make($pengeluaran);
            return $pengeluaran;
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            throw $ex;
        }
    }

    /* UPDATE PENGELUARAN */
    public function updatePengeluaran($props, $id){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $pengeluaran = $this->pengeluaranModel::find($id);
            if ($pengeluaran) {
                /* UPDATE PENGELUARAN */
                $pengeluaran->bulan     = $props['bulan'];
                $pengeluaran->tahun     = $props['tahun'];
                $pengeluaran->id_user   = $this->returnAuthUser()->id;
                $pengeluaran->update();

                /* COMMIT DB TRANSACTION */
                DB::commit();

                $pengeluaran = PengeluaranResource::make($pengeluaran);
                return $pengeluaran;
            } else {
                throw new Exception('Catatan tidak ditemukan!');
            }
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            throw $ex;
        }
    }

    /* DESTROY PENGELUARAN */
    public function destroyPengeluaran($id){
        try {
            $pengeluaran = $this->pengeluaranModel::find($id);
            if ($pengeluaran) {
                $pengeluaran->delete();

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* DESTROY SELECTED / MULTIPLE PENGELUARAN */
    public function destroyMultiplePengeluaran($props){
        try {
            $pengeluaran = $this->pengeluaranModel::whereIn('id', $props);

            if ($pengeluaran->count() > 0) {
                $pengeluaran->delete();

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
