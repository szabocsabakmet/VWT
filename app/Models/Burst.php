<?php

namespace App\Models;

class Burst implements \IteratorAggregate
{
    public int $id;
    public int $arrivalTimeOfFirstDataElement;

    private float $peakPositioningTime = 0.0;
    /**
     * @var $dataElements array<DataElement>
     */
    private array $dataElements = [];
    private float $peakCost = 0.0;

    /**
     * @param int $id
     */
    public function __construct(int $id, int $arrivalTimeOfFirstDataElement)
    {
        $this->id = $id;
        $this->arrivalTimeOfFirstDataElement = $arrivalTimeOfFirstDataElement;
    }

    public function getPeakCost(): float
    {
        return $this->peakCost;
    }

    public function addDataElement(DataElement $dataElement): void
    {
        $this->dataElements [] = $dataElement;
    }

    public function calculateCostAtCurrentState(float $lambdaUnitDelay, DataElement $justArrivedDataElement): void
    {
        $dataElements = $this->dataElements;
        //consider moving the weights to the loop, so it would have a new starting value for every DataElement
        $weightOfNotYetArrivedElements = 40.0 * 50.0;
        $costOfJustArrivedElement = 1 / (1 + $justArrivedDataElement->arrivalTime);

        foreach ($dataElements as $dataElement)
        {
            $costOfArrivedElements = 0.0;

            foreach ($dataElements as $alreadyArrivedElement)
            {
                $costOfArrivedElements += (1 / (1 + $alreadyArrivedElement->arrivalTime)) * $alreadyArrivedElement->weight;
                $weightOfNotYetArrivedElements -= $alreadyArrivedElement->weight;
//                if (!($alreadyArrivedElement === $justArrivedDataElement)) {
//                }
            }

            $dataElement->cost = ($lambdaUnitDelay
                * ($dataElement->arrivalTime - $this->arrivalTimeOfFirstDataElement))
                + ((1 - $lambdaUnitDelay)
                * ($costOfArrivedElements
                    + ($costOfJustArrivedElement * $weightOfNotYetArrivedElements)));
        }

        $this->setPeakPositioningTimeBasedOnCost();
    }

    public function setPeakPositioningTime(float $peakPositioningTime): void
    {
        $this->peakPositioningTime = $peakPositioningTime - $this->arrivalTimeOfFirstDataElement;
    }

    public function setPeakPositioningTimeBasedOnCost(): void
    {
        $minArrivalTime = reset($this->dataElements)->arrivalTime;
        $leastCost = reset($this->dataElements)->cost;

        /**
         * @var $dataElement DataElement
         */
        foreach ($this as $dataElement)
        {
            if ($dataElement->cost <= $leastCost) {
                $leastCost = $dataElement->cost;
                $minArrivalTime = $dataElement->arrivalTime;
            }
        }

        $this->peakPositioningTime = $minArrivalTime - $this->arrivalTimeOfFirstDataElement;
    }

    public function getPeakPositioningTime(): float
    {
        return $this->peakPositioningTime;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->dataElements);
    }
}
