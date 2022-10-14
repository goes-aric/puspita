<?php
namespace App\Http\Controllers\Pengeluaran;

use Exception;
use Illuminate\Http\Request;
use App\Http\Services\Pengeluaran\PengeluaranService;
use App\Http\Controllers\BaseController;

class PengeluaranController extends BaseController
{
    private $pengeluaranServices;
    private $moduleName;

    public function __construct(PengeluaranService $pengeluaranServices)
    {
        $this->pengeluaranServices = $pengeluaranServices;
        $this->moduleName = 'Pengeluaran Parkir';
    }

    public function index(Request $request)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $pengeluaran = $this->pengeluaranServices->fetchLimit($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar pengeluaran parkir', $pengeluaran);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'bulan' => 'required',
                'tahun' => 'required|numeric',
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $pengeluaran = $this->pengeluaranServices->createPengeluaran($request);
            return $this->returnResponse('success', self::HTTP_CREATED, 'Pengeluaran parkir berhasil dibuat', $pengeluaran);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function show($id)
    {
        try {
            $pengeluaran = $this->pengeluaranServices->fetchById($id);
            return $this->returnResponse('success', self::HTTP_OK, 'Detail pengeluaran parkir', $pengeluaran);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'bulan' => 'required',
                'tahun' => 'required|numeric',
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $pengeluaran = $this->pengeluaranServices->updatePengeluaran($request, $id);
            return $this->returnResponse('success', self::HTTP_OK, 'Pengeluaran parkir berhasil diperbaharui', $pengeluaran);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function destroy($id)
    {
        try {
            $pengeluaran = $this->pengeluaranServices->destroyPengeluaran($id);
            return $this->returnResponse('success', self::HTTP_OK, 'Pengeluaran parkir berhasil dihapus!', $pengeluaran);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function destroyMultiple(Request $request)
    {
        try {
            $props = $request->data;
            $pengeluaran = $this->pengeluaranServices->destroyMultiplePengeluaran($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Pengeluaran parkir berhasil dihapus!', $pengeluaran);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
