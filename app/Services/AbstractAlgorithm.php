<?php

namespace App\Services;

use App\Models\Burst;
use App\Models\DataElement;

abstract class AbstractAlgorithm
{
    public BurstContainer $bursts;

    abstract protected function calculateCostAtCurrentState(DataElement $justArrivedDataElement, Burst $burst): void;

    abstract public function getPeakPositioningTimes(): array;

    public function getPeakCosts(): array
    {
        $costs = [];
        $costSoFar = 0.0;

        /**
         * @var Burst $burst
         */
        foreach ($this->bursts as $burst)
        {
            if ($burst->peakCost < 0.0) {
                $burst->peakCost *= -1;
            }
            $costSoFar += $burst->peakCost;
            $costs [] = $costSoFar;
        }

        return $costs;
    }
}
