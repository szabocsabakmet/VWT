<?php

namespace App\Services;

use App\Models\Charts\ChartBurstPeakTimes;
use App\Models\Charts\ChartDataSet;
use App\Models\Charts\ChartTotalCost;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VWTAlgorithmHandler extends AlgorithmHandler
{
    /**
     * @var array<int>
     */
    private array $peakPositioningTimes = [];
    private array $totalCosts = [];
    private array $lambdaValues;
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
        $colors = $this->getAvailableChartColors();
        $color = array_pop($colors);
        $totalCostsDataSet = [new ChartDataSet('VWT cost', $this->totalCosts, $color,$color)];
        return new ChartTotalCost($totalCostsDataSet);
    }

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->lambdaValues = $request->get('flexCheckDefault') ?? [];
        $this->numberOfBurstsToConsider = (int)$this->request->get('numberOfBurstsToConsider');
        $this->data = $this->getJsonDecodedData();
        $request->replace(['flexCheckDefault' => $this->lambdaValues,
            'numberOfBurstsToConsider' => $this->numberOfBurstsToConsider
        ]);
    }

    public function run(): void
    {
        $availableChartColors = $this->getAvailableChartColors();

        foreach ($this->lambdaValues as $lambda) {
            $lambdaKey = $lambda * 10;
            $vwtAlgorithm = new VWTAlgorithm($this->data, $lambda, $this->numberOfBurstsToConsider);
            $peakPositioningTimes = $vwtAlgorithm->getPeakPositioningTimes();
            $this->totalCosts [$lambdaKey] = $vwtAlgorithm->getTotalCost();
            $color = array_pop($availableChartColors);
            $this->VWTBurstPeakTimesDataSet [] =
                new ChartDataSet('VWT ' . $lambda, $peakPositioningTimes, $color, $color);
            $this->peakPositioningTimes [$lambdaKey] = $peakPositioningTimes;
        }
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
            if (empty($this->lambdaValues)) {
                throw new BadRequestHttpException('At least one lambda value has to be provided');
            }
        } catch (\Exception $exception) {
            $this->errors [] = $exception->getMessage();
        }
    }
}
