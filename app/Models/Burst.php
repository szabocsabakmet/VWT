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
