<?php

namespace App\Models\Charts;

class ChartBurstPeakTimes extends ChartModel
{
    public function __construct(array $datasets)
    {
        parent::__construct($datasets);
    }

    public function getXAxisValues(): array
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
