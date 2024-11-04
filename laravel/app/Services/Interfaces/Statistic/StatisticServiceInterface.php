<?php

namespace App\Services\Interfaces\Statistic;

interface StatisticServiceInterface
{
    public function revenueByDate();
    public function seasonalSale();
    public function popularProducts();
    public function loyalCustomers();


}
