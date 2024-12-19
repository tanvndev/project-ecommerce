<template>
  <TopOverView :date="date" :startDate="start_date" :endDate="end_date" />

  <a-row :gutter="[16, 16]" class="report-block mt-3">
    <!-- NetRevenua -->
    <NetRevenua :date="date" :startDate="start_date" :endDate="end_date" />
    <!-- ProfitView -->
    <ProfitView :date="date" :startDate="start_date" :endDate="end_date" />
    <!-- TopProductSell -->
    <TopProductSell :date="date" :startDate="start_date" :endDate="end_date" />
    <!-- AvgTotalPriceOrder -->
    <AvgTotalPriceOrder :date="date" :startDate="start_date" :endDate="end_date" />
    <!-- TopSearchHistory -->
    <!-- <TopSearchHistory :date="date" :startDate="start_date" :endDate="end_date" /> -->
  </a-row>
</template>
<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import AvgTotalPriceOrder from './AvgTotalPriceOrder.vue';
import NetRevenua from './NetRevenua.vue';
import ProfitView from './ProfitView.vue';
import TopOverView from './TopOverView.vue';
import TopProductSell from './TopProductSell.vue';
import TopSearchHistory from './TopSearchHistory.vue';

const route = useRoute();
const date = computed(() => route.query.date || 'last_30_days');
const start_date = computed(() => route.query.start_date || '');
const end_date = computed(() => route.query.end_date || '');
</script>
