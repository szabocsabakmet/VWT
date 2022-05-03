<?php

namespace App\Services;

use App\Models\Burst;
use App\Models\DataElement;

class CWTAlgorithm extends AbstractAlgorithm
{
    public BurstContainer $bursts;
    private float $lambdaUnitDelay;
    private iterable $data;
    private int $cwtWaitingTime;

    public function __construct(
        iterable $data,
        float    $lambdaUnitDelay = 0.1,
        int      $cwtWaitingTime = 15,
        int      $l = 50
    )
    {
        $this->lambdaUnitDelay = $lambdaUnitDelay;
        $this->data = $data;
        $this->bursts = new BurstContainer($l);
        $this->cwtWaitingTime = $cwtWaitingTime;
    }

    public function getPeakPositioningTimes(): array
    {
        $results = [];

        foreach ($this->data as $row) {

            $dataElement = new DataElement($row['block_id'], $row['time'], $row['weight']);
            $burst = $this->bursts->getOrCreateBurstOfDataElement($dataElement);
            $burst->addDataElement($dataElement);


            if ($dataElement->arrivalTime - $burst->arrivalTimeOfFirstDataElement >= $this->cwtWaitingTime) {
                $burst->setPeakPositioningTime((float)$this->cwtWaitingTime);
                $this->calculateCostAtCurrentState($dataElement, $burst);
            }

            $results [$burst->id] = $burst->getPeakPositioningTime();

        }
        return $results;

    }

    protected function calculateCostAtCurrentState(DataElement $justArrivedDataElement, Burst $burst): void
    {
        $costOfLateArrivedDataElements = 0.0;

        /**
         * @var $lateArrivedDataElement DataElement
         */
        foreach ($burst as $lateArrivedDataElement) {
            if ($lateArrivedDataElement->arrivalTime - $burst->arrivalTimeOfFirstDataElement > $this->cwtWaitingTime) {
                $costOfLateArrivedDataElements
                    += ($lateArrivedDataElement->weight / (1 + $lateArrivedDataElement->arrivalTime));
            }
        }

        $burst->peakCost =
            $this->lambdaUnitDelay *
            $justArrivedDataElement->arrivalTime + (1 - $this->lambdaUnitDelay) * $costOfLateArrivedDataElements;
    }
}
