<?php
namespace App\Http\Services\Pendapatan;

use Exception;
use App\Models\Pendapatan;
use App\Http\Services\BaseService;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Pendapatan\PendapatanResource;

class PendapatanService extends BaseService
{
    /* PRIVATE VARIABLE */
    private $pendapatanModel;
    private $carbon;

    public function __construct()
    {
        $this->pendapatanModel = new Pendapatan();
        $this->carbon = $this->returnCarbon();
    }

    /* FETCH ALL PENDAPATAN */
    public function fetchLimit($props){
        /* GET DATA FOR PAGINATION AS A MODEL */
        $getAllData = $this->dataFilterPagination($this->pendapatanModel, [], null);
        $totalData = $getAllData->count();

        /* GET DATA WITH FILTER FOR PAGINATION AS A MODEL */
        $getFilterData = $this->dataFilterPagination($this->pendapatanModel, $props, null);
        $totalFiltered = $getFilterData->count();

        /* GET DATA WITH FILTER AS A MODEL */
        $datas = $this->dataFilter($this->pendapatanModel, $props, null);

        /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
        $datas = $datas->get();
        $datas = PendapatanResource::collection($datas);
        $pendapatan = [
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

        return $pendapatan;
    }

    /* FETCH PENDAPATAN BY ID */
    public function fetchById($id){
        try {
            $pendapatan = $this->pendapatanModel::find($id);
            if ($pendapatan) {
                $pendapatan = PendapatanResource::make($pendapatan);
                return $pendapatan;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW PENDAPATAN */
    public function createPendapatan($props){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $pendapatan = $this->pendapatanModel;
            $pendapatan->tanggal    = $props['tanggal'];
            $pendapatan->gambar     = $props['gambar'];
            $pendapatan->id_user    = $this->returnAuthUser()->id;
            $pendapatan->save();

            /* COMMIT DB TRANSACTION */
            DB::commit();

            $pendapatan = PendapatanResource::make($pendapatan);
            return $pendapatan;
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            throw $ex;
        }
    }

    /* UPDATE PENDAPATAN */
    public function updatePendapatan($props, $id){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $pendapatan = $this->pendapatanModel::find($id);
            if ($pendapatan) {
                /* UPDATE PENDAPATAN */
                $pendapatan->tanggal    = $props['tanggal'];
                $pendapatan->gambar     = $props['gambar'];
                $pendapatan->id_user    = $this->returnAuthUser()->id;
                $pendapatan->update();

                /* COMMIT DB TRANSACTION */
                DB::commit();

                $pendapatan = PendapatanResource::make($pendapatan);
                return $pendapatan;
            } else {
                throw new Exception('Catatan tidak ditemukan!');
            }
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            throw $ex;
        }
    }

    /* DESTROY PENDAPATAN */
    public function destroyPendapatan($id){
        try {
            $pendapatan = $this->pendapatanModel::find($id);
            if ($pendapatan) {
                $pendapatan->delete();

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* DESTROY SELECTED / MULTIPLE PENDAPATAN */
    public function destroyMultiplePendapatan($props){
        try {
            $pendapatan = $this->pendapatanModel::whereIn('id', $props);

            if ($pendapatan->count() > 0) {
                $pendapatan->delete();

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
