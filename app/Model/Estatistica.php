<?php

namespace App\Model;

class Estatistica
{
    private int $count;
    private float $sum;
    private float $avg;
    private float $sumTx;
    private float $avgTx;

    public function __construct (int $count, float $sum, float $avg, float $sumTx, float $avgTx)
    {
        $this->count = $count;
        $this->sum = $sum;
        $this->avg = $avg;
        $this->sumTx = $sumTx;
        $this->avgTx = $avgTx;
    }

    /**
     * @return int
     */
    public function getCount (): int
    {
        return $this->count;
    }

    /**
     * @return float
     */
    public function getSum (): float
    {
        return $this->sum;
    }

    /**
     * @return float
     */
    public function getAvg (): float
    {
        return $this->avg;
    }

    /**
     * @return float
     */
    public function getSumTx (): float
    {
        return $this->sumTx;
    }

    /**
     * @return float
     */
    public function getAvgTx (): float
    {
        return $this->avgTx;
    }
}