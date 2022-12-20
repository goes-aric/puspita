<?php
namespace App\Http\Controllers\ArusKas;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Services\ArusKas\ArusKasService;

class ArusKasController extends BaseController
{
    private $arusKasServices;
    private $moduleName;

    public function __construct(ArusKasService $arusKasServices)
    {
        $this->arusKasServices = $arusKasServices;
        $this->moduleName = 'Laporan Arus Kas';
    }

    public function listPendapatan(Request $request)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $pendapatan = $this->arusKasServices->fetchPendapatan($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar pendapatan parkir', $pendapatan);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function listPengeluaran(Request $request)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $pengeluaran = $this->arusKasServices->fetchPengeluaran($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar pengeluaran', $pengeluaran);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
