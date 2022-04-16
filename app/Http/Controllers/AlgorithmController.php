<?php

namespace App\Http\Controllers;

use App\Models\ChartBurstPeakTimes;
use App\Services\VWTAlgorithm;
use App\Services\VWTAlgorithmWithObjects;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AlgorithmController extends Controller
{
    public function index(Request $request)
    {
        return view('home');
    }

    public function processData(Request $request)
    {
        $errors = [];
        $result = [];
        try {
            if (is_null($request->file('formFile'))) {
                throw new BadRequestHttpException('No file provided');
            }
            $data = json_decode($request->file('formFile')?->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR);

            $vwtAlgorithm = new VWTAlgorithmWithObjects($data);
            $result = $vwtAlgorithm->getResults();
        } catch (\JsonException) {
            $errors [] = 'Something happened while decoding json, please check if it is valid';
        } catch (\Exception $exception) {
            $errors [] = $exception->getMessage();
        }

//        $chartBurstPeakTimes = new ChartBurstPeakTimes($vwtAlgorithm->peakTimes);

//        dd($result);

        return view('home', [
            'result' => $result,
            'errors' => $errors,
//            'chartBurstPeakTimes' => $chartBurstPeakTimes,
        ]);
    }
}
