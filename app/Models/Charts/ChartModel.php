<?php

namespace App\Models\Charts;

use Traversable;

abstract class ChartModel implements \IteratorAggregate
{
    /**
     * @var array<ChartDataSet>
     */
    public array $datasets = [];

    public function __construct(array $datasets)
    {
        $this->datasets = $datasets;
    }

    abstract public function getXAxisValues(): array;

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->datasets);
    }

}
