<?php

namespace App\Services;

use App\Models\Charts\ChartBurstPeakTimes;
use App\Models\Charts\ChartDataSet;
use App\Models\Charts\ChartTotalCost;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VWTAlgorithmHandler extends AlgorithmHandler
{
    private const COLOR = 'ff6384ff';

    /**
     * @var array<int>
     */
    private array $peakPositioningTimes = [];
    private array $peakCosts = [];
    private float $lambda;
    private int $numberOfBurstsToConsider;
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
        $totalCostsDataSet = [new ChartDataSet('VWT cost '. $this->lambda, $this->peakCosts, self::COLOR,self::COLOR)];
        return new ChartTotalCost($totalCostsDataSet);
    }

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->lambda = (float)$request->get('lambda') / 10000 ?? 0.001;
        $this->numberOfBurstsToConsider = (int)$this->request->get('numberOfBurstsToConsider');
        $this->data = $this->getJsonDecodedData();
    }

    public function run(): void
    {
        $vwtAlgorithm = new VWTAlgorithm($this->data, $this->lambda, $this->numberOfBurstsToConsider);
        $peakPositioningTimes = $vwtAlgorithm->getPeakPositioningTimes();
        $this->peakCosts = $vwtAlgorithm->getPeakCosts();
        $this->VWTBurstPeakTimesDataSet [] =
            new ChartDataSet('VWT ' . $this->lambda, $peakPositioningTimes, self::COLOR, self::COLOR);
        $this->peakPositioningTimes = $peakPositioningTimes;
    }

    public function validateRequest(): void
    {
        try {
            if (is_null($this->request->file('formFile'))) {
                throw new BadRequestHttpException('No file provided');
            }
            if ($this->numberOfBurstsToConsider === 0) {
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
