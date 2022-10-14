<?php
namespace App\Http\Controllers\Kendaraan;

use Exception;
use Illuminate\Http\Request;
use App\Http\Services\Kendaraan\KendaraanService;
use App\Http\Controllers\BaseController;

class KendaraanController extends BaseController
{
    private $kendaraanServices;
    private $moduleName;

    public function __construct(KendaraanService $kendaraanServices)
    {
        $this->kendaraanServices = $kendaraanServices;
        $this->moduleName = 'Kendaraan';
    }

    public function index(Request $request)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $kendaraan = $this->kendaraanServices->fetchLimit($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar kendaraan', $kendaraan);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'jenis_kendaraan'   => 'required|string|unique:kendaraan',
                'biaya_parkir'      => 'required|numeric',
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $kendaraan = $this->kendaraanServices->createKendaraan($request);
            return $this->returnResponse('success', self::HTTP_CREATED, 'Kendaraan berhasil dibuat', $kendaraan);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function show($id)
    {
        try {
            $kendaraan = $this->kendaraanServices->fetchById($id);
            return $this->returnResponse('success', self::HTTP_OK, 'Detail kendaraan', $kendaraan);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'jenis_kendaraan'   => 'required|string|unique:kendaraan,jenis_kendaraan,'.$id.'',
                'biaya_parkir'      => 'required|numeric',
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $kendaraan = $this->kendaraanServices->updateKendaraan($request, $id);
            return $this->returnResponse('success', self::HTTP_OK, 'Data kendaraan berhasil diperbaharui', $kendaraan);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function destroy($id)
    {
        try {
            $kendaraan = $this->kendaraanServices->destroyKendaraan($id);
            return $this->returnResponse('success', self::HTTP_OK, 'Kendaraan berhasil dihapus!', $kendaraan);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function destroyMultiple(Request $request)
    {
        try {
            $props = $request->data;
            $kendaraan = $this->kendaraanServices->destroyMultipleKendaraan($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Kendaraan berhasil dihapus!', $kendaraan);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function fetchDataOptions(Request $request)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $kendaraan = $this->kendaraanServices->fetchDataOptions($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar kendaraan', $kendaraan);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
