<?php

namespace App\Models;

class Burst
{
    private int $id;
    private float $peakPositioningTime = 0.0;
//    private array $positioningTimes = [];
    private float $cost = 0.0;

    /**
     * @param int $id
     * @param float $peakPositioningTime
     * @param array $positioningTimes
     * @param float $cost
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function addCost(float $cost)
    {
        $this->cost += $cost;
    }

}
