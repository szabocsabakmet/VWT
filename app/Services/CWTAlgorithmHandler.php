<?php

namespace App\Services;

use App\Models\Charts\ChartBurstPeakTimes;
use App\Models\Charts\ChartDataSet;
use App\Models\Charts\ChartTotalCost;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CWTAlgorithmHandler extends AlgorithmHandler
{
    /**
     * @var array<int>
     */
    private array $peakPositioningTimes = [];
    private array $totalCosts = [];
    private float $lambda;
    private float $constantWaitingTime;
    private array $data;
    /**
     * @var array<ChartDataSet> $VWTBurstPeakTimesDataSet
     */
    private array $VWTBurstPeakTimesDataSet = [];

    public function getChartBurstPeakTimes(): ChartBurstPeakTimes
    {
        return new ChartBurstPeakTimes($this->VWTBurstPeakTimesDataSet);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getPeakPositioningTimes(): array
    {
        return $this->peakPositioningTimes;
    }

    public function getChartTotalCosts(): ChartTotalCost
    {
        $color = '4680bb';
        $totalCostsDataSet = [new ChartDataSet('CWT cost '. $this->constantWaitingTime, $this->totalCosts, $color, $color)];
        return new ChartTotalCost($totalCostsDataSet);
    }

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->lambda = (float)$request->get('lambda') / 10000 ?? 0.001;
        $this->constantWaitingTime = $request->get('constantWaitingTime') ?? 15.0;
        $this->data = $this->getJsonDecodedData();
    }

    public function run(): void
    {
        $cwtAlgorithm = new CWTAlgorithm($this->data, $this->lambda, $this->constantWaitingTime);
        $peakPositioningTimes = $cwtAlgorithm->getPeakPositioningTimes();
        $this->totalCosts [$this->lambda] = $cwtAlgorithm->getTotalCost();
        $color = '4680bb';
        $this->VWTBurstPeakTimesDataSet [] =
            new ChartDataSet('CWT ' . $this->constantWaitingTime, $peakPositioningTimes, $color, $color);
        $this->peakPositioningTimes = $peakPositioningTimes;
    }

    public function validateRequest(): void
    {
        try {
            if (is_null($this->request->file('formFile'))) {
                throw new BadRequestHttpException('No file provided');
            }
            if ($this->constantWaitingTime === 0.0) {
                throw new BadRequestHttpException('The burst consideration number cannot be 0');
            }
            if (!isset($this->lambda)) {
                throw new BadRequestHttpException('At least one lambda value has to be provided');
            }
        } catch (\Exception $exception) {
            $this->errors [] = $exception->getMessage();
        }
    }
}
