<?php

namespace App\Http\Controllers;

use App\Models\Charts\ChartBurstPeakTimes;
use App\Models\Charts\ChartDataSet;
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
        $peakPositioningTimes = [];
        $chartBurstPeakTimes = null;
        try {
            if (is_null($request->file('formFile'))) {
                throw new BadRequestHttpException('No file provided');
            }
            $data = json_decode($request->file('formFile')?->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR);

            $availableChartColors = [
                'ff6384ff',
                '4680bb',
                'bdd85b',
                'ca16c1',
                'a3023a',
                'cd87ac',
                '9fadbe',
                '662d33',
                '21dfdf',
            ];

            foreach ($request->get('flexCheckDefault') as $lambda)
            {
                $vwtAlgorithm = new VWTAlgorithmWithObjects($data, $lambda);
                $peakPositioningTimes = $vwtAlgorithm->getPeakPositioningTimes();
                $color = array_pop($availableChartColors);
                $VWTDataSet [] = new ChartDataSet('VWT ' . $lambda, $peakPositioningTimes, $color, $color);
            }

            $chartBurstPeakTimes = new ChartBurstPeakTimes($VWTDataSet);

        } catch (\JsonException) {
            $errors [] = 'Something happened while decoding json, please check if it is valid';
        } catch (\Exception $exception) {
            $errors [] = $exception->getMessage();
        }

        $request->flash();

        return view('home', [
            'peakPositioningTimes' => $peakPositioningTimes,
            'errors' => $errors,
            'chartBurstPeakTimes' => $chartBurstPeakTimes,
        ]);
    }
}
