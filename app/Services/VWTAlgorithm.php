<?php

namespace App\Services;

use App\Models\DataElement;

class VWTAlgorithm
{
    public BurstContainer $bursts;
    private float $lambdaUnitDelay;
    private iterable $data;

    public function __construct(
        iterable $data,
        float    $lambdaUnitDelay = 0.1,
        int      $l = 50
    )
    {
        $this->lambdaUnitDelay = $lambdaUnitDelay;
        $this->data = $data;
        $this->bursts = new BurstContainer($l);
    }


    public function getPeakPositioningTimes(): array
    {
        $results = [];

        foreach ($this->data as $row) {

            $dataElement = new DataElement($row['block_id'], $row['time'], $row['weight']);
            $burst = $this->bursts->getOrCreateBurstOfDataElement($dataElement);
            $burst->addDataElement($dataElement);

            $burst->calculateCostAtCurrentState($this->lambdaUnitDelay, $dataElement);

            if ($burst->getPeakPositioningTime() === null
                && ($dataElement->arrivalTime - $burst->arrivalTimeOfFirstDataElement
                    >= (($this->bursts->getAverageOfOptimalPositioningTimesForStartedLBursts($burst))))
            ) {
                $burst->setPeakPositioningTime(
                    $this->bursts->getAverageOfOptimalPositioningTimesForStartedLBursts($burst)
                );
            }

            if ($row['event'] === 'end' && $burst->getPeakPositioningTime() === null) {
                $burst->setPeakPositioningTime($dataElement->arrivalTime - $burst->arrivalTimeOfFirstDataElement);
            }

            $results [$burst->id] = $burst->getPeakPositioningTime();

//            if (isset($results[260])) {
//                $dataElement = $dataElement;
//            }

        }
        return $results;
    }

    public function getTotalCost()
    {
        $totalCost = 0.0;
        foreach ($this->bursts as $burst)
        {
            $totalCost += $burst->peakCost;
        }

        return $totalCost;
    }
}
