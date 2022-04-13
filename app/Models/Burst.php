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
//        dump($this->dataElements);
        $dataElements = $this->dataElements;
//        $lastDataElement = end($dataElements);

//        dump($this->dataElements);
        $costOfArrivedElements = 0.0;
        $weightOfNotYetArrivedElements = 20.0 * 50.0;

//        $lossOfLatency = $lambdaUnitDelay
//            * ($lastDataElement->arrivalTime - $this->arrivalTimeOfFirstDataElement)
//            + (1 - $lambdaUnitDelay);
        $costOfJustArrivedElement = 1 / (1 + $justArrivedDataElement->arrivalTime);

//        foreach ($dataElements as $dataElement)
//        {
//            $weightOfNotYetArrivedElements -= $dataElement->weight;
//        }

        foreach ($dataElements as $dataElement)
        {
            if (!($dataElement === $justArrivedDataElement)) {
                $costOfArrivedElements += 1 / (1 + $dataElement->arrivalTime) * $dataElement->weight;
            }
            $weightOfNotYetArrivedElements -= $dataElement->weight;

//            $cost = $lambdaUnitDelay
//                * ($dataElement->arrivalTime - $this->arrivalTimeOfFirstDataElement)
//                + (1 - $lambdaUnitDelay) * ($costOfArrivedElements + $costOfJustArrivedElement * $weightOfNotYetArrivedElements);
//
//            $dataElement->costOfBurstAtArrival = $cost;
        }

        $justArrivedDataElement->costOfBurstAtArrival = $lambdaUnitDelay
            * ($justArrivedDataElement->arrivalTime - $this->arrivalTimeOfFirstDataElement)
            + (1 - $lambdaUnitDelay)
            * ($costOfArrivedElements + $costOfJustArrivedElement * $weightOfNotYetArrivedElements);

//        $justArrivedDataElement->costOfBurstAtArrival = $cost;

        $this->setPeakPositioningTimeBasedOnCost();
    }

    public function setPeakPositioningTime(float $peakPositioningTime): void
    {
        $this->peakPositioningTime = $peakPositioningTime - $this->arrivalTimeOfFirstDataElement;
    }

    public function setPeakPositioningTimeBasedOnCost(): void
    {
        $minArrivalTime = end($this->dataElements)->arrivalTime;
        $leastCost = end($this->dataElements)->costOfBurstAtArrival;

        foreach ($this as $dataElement)
        {
            if ($dataElement->costOfBurstAtArrival < $leastCost) {
                $leastCost = $dataElement->costOfBurstAtArrival;
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
