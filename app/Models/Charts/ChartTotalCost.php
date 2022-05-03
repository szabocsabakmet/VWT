<?php

namespace App\Models\Charts;

use Exception;
use JetBrains\PhpStorm\Internal\TentativeType;
use Traversable;

class ChartTotalCost extends ChartModel
{
    /**
     * @var array<ChartDataSet>
     */
    public array $datasets = [];

    public function __construct(array $datasets)
    {
        parent::__construct($datasets);
    }

//    public function getXAxisValues()
//    {
//        $lambdaValues = [];
//        foreach ($this as $dataset)
//        {
//            foreach ($dataset->getData() as $lambda => $item)
//            {
//                $lambdaValues [] = (float)$lambda / 10;
//            }
//        }
//
//        return $lambdaValues;
//    }

    public function getXAxisValues()
    {
        return range(0, $this->getMaxCount());
    }

    /**
     * @return int
     */
    private function getMaxCount(): int
    {
        $maxCount = 0;
        foreach ($this as $dataset) {
            $maxCount = count($dataset->data);
        }
        return $maxCount;
    }
}
