<?php

namespace App\Services;

use App\Models\Burst;
use App\Models\DataElement;

class BurstContainer implements \IteratorAggregate
{
    public int $burstId;
    /**
     * @var $bursts array<Burst>
     */
    public array $bursts = [];
    private int $l;

    public function __construct(int $l)
    {
        $this->burstId = 0;
        $this->l = $l;
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

    public function getSumOfOptimalPositioningTimesForStartedLBursts(Burst $burst, array $burstsToConsider = []): float
    {
        $burstsToConsider = empty($burstsToConsider) ? $this->getBurstsToConsiderForBurst($burst) : $burstsToConsider;
        $sum = 0.0;
        /**
         * @var $consideredBurst Burst
         */
        foreach ($burstsToConsider as $consideredBurst)
        {
            $sum += $consideredBurst->getOptimalPositioningTime();
        }

        return $sum;
    }

    public function getAverageOfOptimalPositioningTimesForStartedLBursts(Burst $burst): float
    {
        $burstsToConsider = $this->getBurstsToConsiderForBurst($burst);

        $divider = min(count($burstsToConsider), $this->l) === 0 ? 1 : min(count($burstsToConsider), $this->l);

        return $this->getSumOfOptimalPositioningTimesForStartedLBursts($burst) / $divider;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->bursts);
    }

    private function getBurstsToConsiderForBurst(Burst $burst): array
    {
        $burstsToConsider = $this->bursts;

        unset($burstsToConsider[$burst->id]);

        if (isset($burstsToConsider[$burst->id + 1])) {
            unset($burstsToConsider[$burst->id + 1]);
        }

        return array_slice($burstsToConsider, -$this->l);
    }
}
