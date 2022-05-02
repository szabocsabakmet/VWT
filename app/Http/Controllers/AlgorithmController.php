<?php

namespace App\Http\Controllers;

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
        $algorithmHandler = new VWTAlgorithmHandler($request);
        $algorithmHandler->validateRequest();
        if (!$algorithmHandler->hasErrors()){
            $algorithmHandler->run();
        }

        [$peakPositioningTimes, $errors, $chartBurstPeakTimes, $chartTotalCosts] = [
            $algorithmHandler->getPeakPositioningTimes(),
            $algorithmHandler->getErrors(),
            $algorithmHandler->getChartBurstPeakTimes(),
            $algorithmHandler->getChartTotalCosts()
        ];

        $request->flash();

        return view('home', [
            'peakPositioningTimes' => $peakPositioningTimes,
            'errors' => $errors,
            'chartBurstPeakTimes' => $chartBurstPeakTimes,
            'chartTotalCosts' => $chartTotalCosts,
        ]);
    }
}
