<?php

namespace App\Models\Charts;

interface ChartModelInterface extends \IteratorAggregate
{
    public function __construct(array $datasets);

    public function getXAxisValues();

}
