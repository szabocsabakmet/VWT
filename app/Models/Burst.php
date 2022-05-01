<?php

namespace App\Models;

class Burst implements \IteratorAggregate
{
    public int $id;
    public int $arrivalTimeOfFirstDataElement;

    private ?float $peakPositioningTime = null;
    private float $optimalPositioningTime = 0.0;
    /**
     * @var $dataElements array<DataElement>
     */
    private array $dataElements = [];
    public float $peakCost = 0.0;

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
                $weightOfNotYetArrivedElements -= 50;

//                if (isset($this->peakPositioningTime) && $alreadyArrivedElement->arrivalTime > $this->peakPositioningTime + $this->arrivalTimeOfFirstDataElement) {
//                    $weightOfNotYetArrivedElements += $alreadyArrivedElement->weight;
//                } else {
//                    $costOfArrivedElements += (1 / (1 + $alreadyArrivedElement->arrivalTime)) * $alreadyArrivedElement->weight;
//                }
            }

            $dataElement->cost = ($lambdaUnitDelay
                * ($dataElement->arrivalTime - $this->arrivalTimeOfFirstDataElement))
                + ((1 - $lambdaUnitDelay)
                * ($costOfArrivedElements
                    + ($costOfJustArrivedElement * $weightOfNotYetArrivedElements)));
        }

        $this->setOptimalPeakPositioningTimeBasedOnCost();
    }

    public function setPeakPositioningTime(float $peakPositioningTime): void
    {
        $this->peakPositioningTime = $peakPositioningTime;
    }

    public function setOptimalPeakPositioningTimeBasedOnCost(): void
    {
        $minArgDataElement = reset($this->dataElements);

        /**
         * @var $dataElement DataElement
         */
        foreach ($this as $dataElement)
        {
            if ($dataElement->cost <= $minArgDataElement->cost) {
                $minArgDataElement = $dataElement;
            }
        }

        $this->optimalPositioningTime = $minArgDataElement->arrivalTime - $this->arrivalTimeOfFirstDataElement;
        $this->peakCost = $minArgDataElement->cost;
    }

    public function getOptimalPositioningTime(): float
    {
        return $this->optimalPositioningTime;
    }

    public function getPeakPositioningTime(): ?float
    {
        return $this->peakPositioningTime;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->dataElements);
    }
}
