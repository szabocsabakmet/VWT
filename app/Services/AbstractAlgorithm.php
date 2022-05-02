<?php

namespace App\Services;

use App\Models\Burst;
use App\Models\DataElement;

abstract class AbstractAlgorithm implements Algorithm
{
    public BurstContainer $bursts;

    public function getTotalCost(): float
    {
        $totalCost = 0.0;
        foreach ($this->bursts as $burst)
        {
            $totalCost += $burst->peakCost;
        }

        return $totalCost * -1;
    }

    abstract protected function calculateCostAtCurrentState(DataElement $justArrivedDataElement, Burst $burst): void;
}
