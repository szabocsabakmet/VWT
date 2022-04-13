<?php

namespace App\Services;

use App\Models\Burst;
use App\Models\DataElement;
use Exception;
use JetBrains\PhpStorm\Pure;
use Traversable;

class BurstContainer implements \IteratorAggregate
{
    public int $burstId;
    /**
     * @var $bursts array<Burst>
     */
    public array $bursts = [];

    public function __construct()
    {
        $this->burstId = 0;
    }

    public function dataElementIsElementOfNewBurst(DataElement $dataElement): bool
    {
        return !isset($this->bursts[$dataElement->burstId]);
    }

    public function getOrCreateBurstOfDataElement(DataElement $dataElement): Burst
    {
        if ($this->dataElementIsElementOfNewBurst($dataElement)) {
            $burst = new Burst($dataElement->burstId, $dataElement->arrivalTime);
            $this->bursts[$dataElement->burstId] = $burst;
        }

        return $this->bursts[$dataElement->burstId];
    }

//    public function getBurstById(int $burstId): ?Burst
//    {
//        return $this->bursts [$burstId] ?? null;
//    }
//

    public function getSumOfPositioningTimesForStartedBursts(): float
    {
        $sum = 0.0;
        foreach ($this->bursts as $burst)
        {
            $sum += $burst->getPeakPositioningTime();
        }

        return $sum;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->bursts);
    }
}
