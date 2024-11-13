<?php

namespace App\Services\Interfaces\Statistic;

interface StatisticServiceInterface
{
    public function reportOverview();
    public function revenueByDate();
    public function seasonalSale();
    public function popularProducts();
    public function loyalCustomers();


    public function getProductReport();

    public function getSearchHistory();
}
