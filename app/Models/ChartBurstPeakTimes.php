<?php

namespace App\Models;

class ChartBurstPeakTimes
{
    public array $peakTimes;
    public array $yAxisValues;
    public array $xAxisValues;

    /**
     * @param array $peakTimes
     */
    public function __construct(array $peakTimes)
    {
        $this->peakTimes = $peakTimes;
        $this->xAxisValues = $this->getXAxisValues();
        $this->yAxisValues = $this->getYAxisValues();
    }

    private function getYAxisValues()
    {
        $rounded = $this->getRoundedAverageYValue();
        return range(min($this->peakTimes) - $rounded,
            max($this->peakTimes) + $rounded,
            $this->getYStep($rounded));
    }

    private function getRoundedAverageYValue()
    {
        return round(array_sum($this->peakTimes) / count($this->peakTimes));
    }

    private function getYStep($roundedValue = null)
    {
        if (is_null($roundedValue)) {
            $roundedValue = $this->getRoundedAverageYValue();
        }

        return round($roundedValue * 0.5);
    }

    private function getXAxisValues()
    {
        $count = count($this->peakTimes);
        return range(1, $count, $count * 0.1);
    }

}
