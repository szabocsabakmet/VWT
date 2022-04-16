<?php

namespace App\Models;

class DataElement
{
    public int $burstId;
    public int $arrivalTime;
    public float $weight;
    public float $cost;

    /**
     * @param int $burstId
     * @param int $arrivalTime
     * @param float $weight
     */
    public function __construct(int $burstId, int $arrivalTime, float $weight)
    {
        $this->burstId = $burstId;
        $this->arrivalTime = $arrivalTime;
        $this->weight = $weight;
    }


}
