<?php

namespace App\Http\Controllers;

use App\Models\Charts\ChartBurstPeakTimes;
use App\Models\Charts\ChartDataSet;
use App\Models\Charts\ChartTotalCost;
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
        $chartTotalCosts = null;
        $numberOfBurstsToConsider = (int)$request->get('numberOfBurstsToConsider');
        try {
            if (is_null($request->file('formFile'))) {
                throw new BadRequestHttpException('No file provided');
            }
            if (is_null($numberOfBurstsToConsider)) {
                throw new BadRequestHttpException('No burst consideration number provided');
            }
            if ($numberOfBurstsToConsider === 0) {
                throw new BadRequestHttpException('The burst consideration number cannot be 0');
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
                $vwtAlgorithm = new VWTAlgorithmWithObjects($data, $lambda, $numberOfBurstsToConsider);
                $peakPositioningTimes = $vwtAlgorithm->getPeakPositioningTimes();
                $totalCosts [$lambda * 10] = $vwtAlgorithm->getTotalCost();
                $color = array_pop($availableChartColors);
                $VWTDataSet [] = new ChartDataSet('VWT ' . $lambda, $peakPositioningTimes, $color, $color);
            }

            $color = '4680bb';
            $totalCostsDataSet = [new ChartDataSet('VWT cost', $totalCosts, $color,$color)];
            $chartTotalCosts = new ChartTotalCost($totalCostsDataSet);
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
            'chartTotalCosts' => $chartTotalCosts,
        ]);
    }
}
