<?php
namespace App\Http\Services\Pendapatan;

use Exception;
use App\Models\Pendapatan;
use Illuminate\Support\Str;
use App\Models\DetailPendapatan;
use App\Http\Services\BaseService;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Pendapatan\PendapatanResource;
use App\Http\Services\Pendapatan\DetailPendapatanService;

class PendapatanService extends BaseService
{
    /* PRIVATE VARIABLE */
    private $pendapatanModel;
    private $detailModel;
    private $detailService;
    private $carbon;

    public function __construct()
    {
        $this->pendapatanModel = new Pendapatan();
        $this->detailModel = new DetailPendapatan();
        $this->detailService = new DetailPendapatanService;
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
            $imageName = null;
            $imagePath = storage_path("app/public/images/");
            $imageBinary = $props->file('gambar');

            /* TRY TO UPLOAD IMAGE FIRST */
            /* DECLARE NEW IMAGE VARIABLE */
            $image = $props->file('gambar');
            $newName = 'pendapatan-'.Str::random(5).'.'. $image->getClientOriginalExtension();
            $uploadImage = $this->returnUploadFile($imagePath, $newName, $imageBinary);
            if ($uploadImage['status'] == 'success') {
                $imageName = $uploadImage['filename'];
            }

            $pendapatan = $this->pendapatanModel;
            $pendapatan->tanggal    = $props['tanggal'];
            $pendapatan->gambar     = $imageName;
            $pendapatan->id_user    = $this->returnAuthUser()->id;
            $pendapatan->save();

            /* DETAILS */
            foreach (json_decode($props['pendapatan']) as $item) {
                $detail = new $this->detailModel;
                $detail->id_pendapatan_parkir   = $pendapatan['id'];
                $detail->id_kendaraan           = $item->id;
                $detail->jenis_kendaraan        = $item->jenis_kendaraan;
                $detail->jumlah_kendaraan       = $item->jumlah_kendaraan;
                $detail->biaya_parkir           = $item->biaya_parkir;
                $detail->total                  = $item->jumlah_total;
                $detail->save();
            }

            /* UPDATE JUMLAH TOTAL ON PENDAPATAN PARKIR */
            $this->detailService->updateJumlahTotal($pendapatan['id']);

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
                $imageName = $pendapatan->gambar;
                $imagePath = storage_path("app/public/images/");
                $imageBinary = $props->file('gambar');

                /* TRY TO UPLOAD IMAGE */
                if (!empty($props->file('gambar'))) {
                    // IF CURRENT IMAGE IS NOT EMPTY, DELETE CURRENT IMAGE
                    if ($pendapatan->gambar != null) {
                        $this->returnDeleteFile($imagePath, $imageName);
                    }

                    /* DECLARE NEW IMAGE VARIABLE */
                    $image = $props->file('gambar');
                    $newName = 'pendapatan-'.Str::random(5).'.'. $image->getClientOriginalExtension();
                    $uploadImage = $this->returnUploadFile($imagePath, $newName, $imageBinary);
                    if ($uploadImage['status'] == 'success') {
                        $imageName = $uploadImage['filename'];
                    }
                }

                /* UPDATE PENDAPATAN */
                $pendapatan->tanggal    = $props['tanggal'];
                $pendapatan->gambar     = $imageName;
                $pendapatan->id_user    = $this->returnAuthUser()->id;
                $pendapatan->update();

                /* REMOVE PREV DETAILS */
                $this->detailModel::where('id_pendapatan_parkir', '=', $id)->delete();

                /* DETAILS */
                foreach (json_decode($props['pendapatan']) as $item) {
                    $detail = new $this->detailModel;
                    $detail->id_pendapatan_parkir   = $id;
                    $detail->id_kendaraan           = $item->id;
                    $detail->jenis_kendaraan        = $item->jenis_kendaraan;
                    $detail->jumlah_kendaraan       = $item->jumlah_kendaraan;
                    $detail->biaya_parkir           = $item->biaya_parkir;
                    $detail->total                  = $item->jumlah_total;
                    $detail->save();
                }

                /* UPDATE JUMLAH TOTAL ON PENDAPATAN PARKIR */
                $this->detailService->updateJumlahTotal($id);

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

    /* FETCH LAPORAN PENDAPATAN */
    public function fetchAll($props){
        $tanggalMulai = isset($props['start']) ? $props['start'] : $this->carbon::now();
        $tanggalAkhir = isset($props['end']) ? $props['end'] : $this->carbon::now();

        $datas = DB::table('detail_pendapatan_parkir')
                    ->join('pendapatan_parkir', 'detail_pendapatan_parkir.id_pendapatan_parkir', '=', 'pendapatan_parkir.id')
                    ->select(DB::raw('id_kendaraan, jenis_kendaraan, SUM(jumlah_kendaraan) AS jumlah, biaya_parkir, SUM(total) AS total'))
                    ->where('pendapatan_parkir.tanggal', '>=', $tanggalMulai)
                    ->where('pendapatan_parkir.tanggal', '<=', $tanggalAkhir)
                    ->groupBy('id_kendaraan', 'jenis_kendaraan', 'biaya_parkir')
                    ->get();
        return $datas;
    }

    /* CHARTS PENDAPATAN */
    public function charts(){
        try {
            $pendapatan = [];
            $year = $this->carbon::now()->format('Y');
            $data = [];
            for ($x=1; $x <= 12; $x++) {
                $data[] = $this->pendapatanModel::selectRaw("$x AS month, IFNULL(SUM(grand_total), 0) AS amount, 'Rupiah' AS unit")
                            ->whereYear('pendapatan_parkir.tanggal', '=', $year)
                            ->whereMonth('pendapatan_parkir.tanggal', '=', $x)
                            ->first();
            }

            $pendapatan[] = [
                'name'  => $year,
                'data'  => $data
            ];
            return $pendapatan;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
