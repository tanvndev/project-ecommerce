<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen">
        <h1 class="py-5 text-[18px] uppercase">Phân tích sản phẩm</h1>

        <a-tabs>
          <a-tab-pane key="product" tab="Phân tích sản phẩm"></a-tab-pane>
        </a-tabs>

        <!-- Toolbox Filter -->
        <ToolboxFilter />

        <a-row :gutter="[16,16]" class="report-block mt-3">
          
        <TopProductSell :date="date" :startDate="start_date" :endDate="end_date" />

        <AvgTotalPriceOrder :date="date" :startDate="start_date" :endDate="end_date" />
        
        <PopularProduct :date="date" :startDate="start_date" :endDate="end_date" />

        </a-row>
      </div>
    </template>
  </MasterLayout>
</template>

<script setup>
import { MasterLayout, ToolboxFilter } from '@/components/backend';
import TopProductSell from './partials/dashboard/TopProductSell.vue';

import PopularProduct from './partials/dashboard/PopularProduct.vue';

import AvgTotalPriceOrder from './partials/dashboard/AvgTotalPriceOrder.vue';

import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const date = computed(() => route.query.date || 'last_30_days');
const start_date = computed(() => route.query.start_date || '');
const end_date = computed(() => route.query.end_date || '');
</script>
<style scoped>

</style>
