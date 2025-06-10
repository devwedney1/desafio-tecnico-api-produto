<?php
namespace App\Models;

class Estatistica
{
    private int $count;
    private float $sum;
    private float $avg;
    private float $sumTx;
    private float $avgTx;

    public function __construct(int $count, float $sum, float $avg, float $sumTx, float $avgTx)
    {
        $this->count = $count;
        $this->sum = $sum;
        $this->avg = $avg;
        $this->sumTx = $sumTx;
        $this->avgTx = $avgTx;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getSum(): float
    {
        return $this->sum;
    }

    public function getAvg(): float
    {
        return $this->avg;
    }

    public function getSumTx(): float
    {
        return $this->sumTx;
    }

    public function getAvgTx(): float
    {
        return $this->avgTx;
    }
}