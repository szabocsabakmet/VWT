<?php

namespace App\Services;

class VWTAlgorithm
{
    public array $peakTimes = [];
    public array $costs = [];

//    protected array $positioningTimesL = [];

    private float $lambdaUnitDelay;
//    private int $numbersOfLastBurstsToConsider;
    private iterable $data;
    private array $bursts;

    /**
     * @param iterable $data
     * @param float $lambdaUnitDelay
     * @param int $numbersOfLastBurstsToConsider
     */
    public function __construct(
        iterable $data,
        float    $lambdaUnitDelay = 0.1,
        int      $numbersOfLastBurstsToConsider = 50,
    )
    {
        $this->lambdaUnitDelay = $lambdaUnitDelay;
//        $this->numbersOfLastBurstsToConsider = $numbersOfLastBurstsToConsider;
        $this->data = $data;
    }


//    /**
//     * Adding the next item to the array while keeping it on the maximum of L length
//     * @param int $time
//     * @return void
//     */
//    private function addNewPositioningTime($burstId, $time): void
//    {
////        if (count($this->positioningTimesL) === $this->numbersOfLastBurstsToConsider) {
////            array_shift($this->positioningTimesL);
////        }
//
//        $this->positioningTimesL[$burstId] = $time;
//    }

    private function getSumOfPositioningTimesForL()
    {
        return array_sum($this->peakTimes);
    }

//    /**
//     * The method should check if the 'block_id' is the same as the currently analyzed burst number
//     * @param $data
//     * @param $burst
//     * @return bool
//     */
//    private function dataIsElementOfActualBurst($row, $burstId)
//    {
//        return $burstId === $row['block_id'];
//    }

    private function dataIsElementOfNewBurst($burstId)
    {
        return !isset($this->bursts[$burstId]);
    }

    /**
     * This method should contain the logic of the optimal online function
     * not sure of this
     */
    private function cost($burst)
    {
        $lastItem = end($burst['arrival_times']);
        $multiplier = $this->lambdaUnitDelay * ($lastItem - $burst['firstElementArrivalTime']) + (1 - $this->lambdaUnitDelay);


        $x = 0.0;
        $y = 0.0;

        reset($burst['arrival_times']);

        foreach ($burst['arrival_times'] as $weight => $arrival_time)
        {
            $x += (1 / (1 + $lastItem)) * $weight + (1 / (1 + $arrival_time));
            $y += $weight;
        }

        end($burst['arrival_times']);
        $y -= key($burst['arrival_times']);

        return $multiplier * $x * $y;
    }

    public function costForP($burst)
    {
        $costs = [];

        $lastItem = key(end($burst));

        $arrivalTimesBeforeT = $burst['arrival_times'];
        $tK = array_pop($arrivalTimesBeforeT);

        foreach ($burst['arrival_times'] as $t)
        {
            $multiplier = $this->lambdaUnitDelay * ($t - $burst['firstElementArrivalTime']) + (1 - $this->lambdaUnitDelay);

            $x = 0.0;
            $y = 0.0;

            reset($burst['arrival_times']);

            foreach ($arrivalTimesBeforeT as $weight => $arrival_time)
            {
                $x += (1 / (1 + $arrival_time)) * $weight + (1 / (1 + $tK));
            }

            $y = $lastItem;

//            end($burst['arrival_times']);
//            $y -= key($burst['arrival_times']);


            $costs [$multiplier * $x * $y] = $t;
        }

        return $costs[min(array_keys($costs))];
    }

    public function getResults()
    {
//        $opt = [];
//
//        $burstId = 0;
//
//        $actualBurst = [];
//
//        $p = 0;

        $results = [];

        /**
         * Végigmegy az adatokon, minden elem egy burst
         */
        foreach ($this->data as $row) {
            /**
             * Ha az adat új burst-ből valő (azaz a burst-nek vége) akkor:
             * $burstId legyen az új burst azonosítója (block_id)
             * $firstElementArrivalTime legyen az új burst első elemének érkezési időpontja
             * az előző burst-höz tartozó $p -t (p csucsertek) hozzáadjuk a processzor L hosszű vizsgálati tömbjéhez
             * $actualBurst ürítésre kerül
             */
            $burstId = $row['block_id'];
            if ($this->dataIsElementOfNewBurst($burstId)) {
            $this->bursts[$burstId]['firstElementArrivalTime'] = $row['time'];
            }
            $this->bursts[$burstId]['arrival_times'][$row['weight']] = $row['time'];

            /**
             * Ha $data eleme $burst-nek akkor:
             * Az éppen vizsgált sort hozzáadjuk az optimális függvény burt bemenetéhez
             * kiszámoljuk adott burst-re az optimális online függvényt
             * $p értékének az optimális függvény eddigi kimeneteinek minimumát adjuk
             */
            $cost = $this->cost($this->bursts[$burstId]);
            $this->bursts[$burstId]['costs'][] = $cost;




            $this->costs[$burstId] = $cost;

            $this->peakTimes[$burstId] = $this->costForP($this->bursts[$burstId]) - $this->bursts[$burstId]['firstElementArrivalTime'];

//            $this->peakTimes[$burstId] = $row['time'] - $this->bursts[$burstId]['firstElementArrivalTime'];




//            if (min($this->bursts[$burstId]['costs']) >= $cost) {
//                $this->costs[$burstId] = $cost;
//                $this->peakTimes[$burstId] = $row['time'] - $this->bursts[$burstId]['firstElementArrivalTime'];
//            }

            /**
             * t = arrival_time
             * rk = a burst első adatának beérkezési ideje (a burst kezdete)
             * t - rk >= (p^)
             * l a viszonyított utóbbi pozícionálási idők darabszáma (az elmúlt hány burst számít bele a kalkulációba)
             * j valamilyen burst számláló
             *
             * Amennyiben érvényesülnek a feltételek, p-t speciális módon számítjuk
             */
            if($this->peakTimes[$burstId] !== 0 && $row['time'] - $this->bursts[$burstId]['firstElementArrivalTime'] >= ($this->getSumOfPositioningTimesForL() / $burstId)) {
                $this->peakTimes[$burstId] = $this->getSumOfPositioningTimesForL() / count($this->bursts);
            }

        }
        return $results;
    }
}
