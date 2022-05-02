<?php

namespace App\Http\Controllers;

use App\Models\Charts\ChartBurstPeakTimes;
use App\Models\Charts\ChartTotalCost;
use App\Services\CWTAlgorithmHandler;
use App\Services\VWTAlgorithmHandler;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlgorithmController extends Controller
{
    public function index(): View
    {
        return view('home');
    }

    public function processData(Request $request): View
    {
        $VWTAlgorithmHandler = new VWTAlgorithmHandler($request);
        $VWTAlgorithmHandler->validateRequest();
        if (!$VWTAlgorithmHandler->hasErrors()){
            $VWTAlgorithmHandler->run();
        }

        $errors = $VWTAlgorithmHandler->getErrors();

        $VWTResults = [
            'peakPositioningTimes' => $VWTAlgorithmHandler->getPeakPositioningTimes(),
            'chartBurstPeakTimes' => $VWTAlgorithmHandler->getChartBurstPeakTimes(),
            'chartTotalCosts' => $VWTAlgorithmHandler->getChartTotalCosts()
        ];

        $CWTAlgorithmHandler = new CWTAlgorithmHandler($request);
        $CWTAlgorithmHandler->validateRequest();
        if (!$CWTAlgorithmHandler->hasErrors()){
            $CWTAlgorithmHandler->run();
        }

        $errors = array_merge($errors, $CWTAlgorithmHandler->getErrors());

        $CWTResults = [
            'chartBurstPeakTimes' => $CWTAlgorithmHandler->getChartBurstPeakTimes(),
            'chartTotalCosts' => $CWTAlgorithmHandler->getChartTotalCosts()
        ];


        $chartBurstPeakTimes = new ChartBurstPeakTimes(array_merge($VWTResults['chartBurstPeakTimes']->datasets, $CWTResults['chartBurstPeakTimes']->datasets));
        $chartTotalCosts = new ChartTotalCost(array_merge($VWTResults['chartTotalCosts']->datasets, $CWTResults['chartTotalCosts']->datasets));


        $request->flash();

        return view('home', [
            'peakPositioningTimes' => $VWTResults['peakPositioningTimes'],
            'errors' => $errors,
            'chartBurstPeakTimes' => $chartBurstPeakTimes,
            'chartTotalCosts' => $chartTotalCosts,
        ]);
    }
}
