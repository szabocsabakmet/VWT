<?php

namespace App\Models\Charts;

use Traversable;

class ChartBurstPeakTimes implements ChartModelInterface
{
    /**
     * @var array<ChartDataSet>
     */
    public array $datasets = [];

    public function __construct(array $datasets)
    {
        $this->datasets = $datasets;
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

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->datasets);
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
