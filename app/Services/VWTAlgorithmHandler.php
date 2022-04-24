<?php

namespace App\Services;

use App\Models\Charts\ChartBurstPeakTimes;
use App\Models\Charts\ChartDataSet;
use App\Models\Charts\ChartTotalCost;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VWTAlgorithmHandler
{
    private Request $request;
    /**
     * @var array<string>
     */
    private array $errors = [];
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
        $this->request = $request;
        $this->lambdaValues = $request->get('flexCheckDefault') ?? [];
        $this->numberOfBurstsToConsider = (int)$this->request->get('numberOfBurstsToConsider');
        $this->data = $this->getJsonDecodedData();
        $this->validateRequest();
        $request->replace(['flexCheckDefault' => $this->lambdaValues,
            'numberOfBurstsToConsider' => $this->numberOfBurstsToConsider
        ]);
    }

    /**
     * @return string[]
     */
    private function getAvailableChartColors(): array
    {
        return [
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

    private function validateRequest(): void
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

    private function getJsonDecodedData(): array
    {
        try {
            return json_decode($this->request->file('formFile')?->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            $this->errors [] = 'Something happened while decoding json, please check if it is valid';
        }

        return [];
    }
}
