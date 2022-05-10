<?php

namespace App\Services;

use App\Models\Charts\ChartBurstPeakTimes;
use App\Models\Charts\ChartTotalCost;
use Illuminate\Http\Request;

abstract class AlgorithmHandler
{
    protected Request $request;
    /**
     * @var array<string>
     */
    protected array $errors = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    abstract public function validateRequest(): void;

    abstract public function hasErrors(): bool;

    abstract public function run(): void;

    abstract public function getChartTotalCosts(): ChartTotalCost;

    abstract public function getPeakPositioningTimes(): array;

    abstract public function getErrors(): array;

    abstract public function getChartBurstPeakTimes(): ChartBurstPeakTimes;

    protected function getJsonDecodedData(): array
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

    /**
     * @return string[]
     */
    protected function getAvailableChartColors(): array
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

}
