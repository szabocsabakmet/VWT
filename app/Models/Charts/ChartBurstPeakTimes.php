<?php

namespace App\Models\Charts;

use Traversable;

class ChartBurstPeakTimes extends ChartModel
{
    public function __construct(array $datasets)
    {
        parent::__construct($datasets);
    }

//    private function getYAxisValues()
//    {
//        $rounded = $this->getRoundedAverageYValue();
//        return range(min($this->peakTimes) - $rounded,
//            max($this->peakTimes) + $rounded,
//            $this->getYStep($rounded));
//    }

//    private function getRoundedAverageYValue()
//    {
//        return round(array_sum($this->peakTimes) / count($this->peakTimes));
//    }
//
//    private function getYStep($roundedValue = null)
//    {
//        if (is_null($roundedValue)) {
//            $roundedValue = $this->getRoundedAverageYValue();
//        }
//
//        return round($roundedValue * 0.5);
//    }

    public function getXAxisValues()
    {
        return range(0, $this->getMaxCount());
    }

    /**
     * @return int
     */
    private function getMaxCount(): int
    {
        $maxCount = 0;
        foreach ($this as $dataset) {
            $maxCount = count($dataset->data);
        }
        return $maxCount;
    }
}
