<?php

namespace App\Services;

interface Algorithm
{
    public function getPeakPositioningTimes(): array;

    public function getTotalCost(): float;
}
