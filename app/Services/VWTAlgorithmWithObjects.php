<?php

namespace App\Services;

use App\Models\DataElement;

class VWTAlgorithmWithObjects
{
    public BurstContainer $bursts;

    public array $costs = [];
    private float $lambdaUnitDelay;
    private iterable $data;
    private int $l = 50;

    public function __construct(
        iterable $data,
        float    $lambdaUnitDelay = 0.1,
        int      $l = 50
    )
    {
        $this->lambdaUnitDelay = $lambdaUnitDelay;
        $this->data = $data;
        $this->bursts = new BurstContainer($l);
    }


    public function getResults()
    {
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
            $dataElement = new DataElement($row['block_id'], $row['time'], $row['weight']);
            $burst = $this->bursts->getOrCreateBurstOfDataElement($dataElement);
            $burst->addDataElement($dataElement);

            /**
             * Ha $data eleme $burst-nek akkor:
             * Az éppen vizsgált sort hozzáadjuk az optimális függvény burt bemenetéhez
             * kiszámoljuk adott burst-re az optimális online függvényt
             * $p értékének az optimális függvény eddigi kimeneteinek minimumát adjuk
             */
            $burst->calculateCostAtCurrentState($this->lambdaUnitDelay, $dataElement);
            $peakTime = $burst->getPeakPositioningTime();

            if ($peakTime !== 0.0
                && ($dataElement->arrivalTime - $burst->arrivalTimeOfFirstDataElement
                    >= (($this->bursts->getSumOfPositioningTimesForStartedLBursts($burst) - $peakTime) / $burst->id))
            ) {
                $burst->setPeakPositioningTime(
                    $burst->arrivalTimeOfFirstDataElement
                    + $this->bursts->getAverageOfPositioningTimesForStartedLBursts($burst)
                );
            }

            /**
             * t = arrival_time
             * rk = a burst első adatának beérkezési ideje (a burst kezdete)
             * t - rk >= (p^)
             * l a viszonyított utóbbi pozícionálási idők darabszáma (az elmúlt hány burst számít bele a kalkulációba)
             * j valamilyen burst számláló
             *
             * Amennyiben érvényesülnek a feltételek, p-t speciális módon számítjuk
             */

            $results [$burst->id] = $burst->getPeakPositioningTime();

//            if (isset($results[950])) {
//                $dataElement = $dataElement;
//            }

        }
        return $results;
    }
}
