<?php

namespace App\Services;

use App\Models\Burst;
use App\Models\DataElement;

class VWTAlgorithm extends AbstractAlgorithm
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

            $this->calculateCostAtCurrentState($dataElement, $burst);

            if ($burst->getPeakPositioningTime() === null
                && ($dataElement->arrivalTime - $burst->arrivalTimeOfFirstDataElement
                    >= (($this->bursts->getAverageOfOptimalPositioningTimesForStartedLBursts($burst))))
            ) {
                $burst->setPeakPositioningTime(
                    $this->bursts->getAverageOfOptimalPositioningTimesForStartedLBursts($burst)
                );
            }

            if ($row['event'] === 'end' && $burst->getPeakPositioningTime() === null) {
                $burst->setPeakPositioningTime($this->bursts->getAverageOfOptimalPositioningTimesForStartedLBursts($burst));
            }

            $results [$burst->id] = $burst->getPeakPositioningTime();
        }
        return $results;
    }

    protected function calculateCostAtCurrentState(DataElement $justArrivedDataElement, Burst $burst): void
    {
        $weightOfNotYetArrivedElements = 40.0 * 50.0;
        $costOfJustArrivedElement = 1 / (1 + $justArrivedDataElement->arrivalTime);

        foreach ($burst as $dataElement)
        {
            $costOfArrivedElements = 0.0;

            foreach ($burst as $alreadyArrivedElement)
            {
                $costOfArrivedElements += (1 / (1 + $alreadyArrivedElement->arrivalTime)) * $alreadyArrivedElement->weight;
                $weightOfNotYetArrivedElements -= 50;
            }

            $dataElement->cost = ($this->lambdaUnitDelay
                    * ($dataElement->arrivalTime - $burst->arrivalTimeOfFirstDataElement))
                + ((1 - $this->lambdaUnitDelay)
                    * ($costOfArrivedElements
                        + ($costOfJustArrivedElement * $weightOfNotYetArrivedElements)));
        }

        $burst->setOptimalPeakPositioningTimeBasedOnCost();

    }
}
