<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen">
        <h1 class="py-5 text-[18px] uppercase">Tổng quan báo cáo</h1>

        <a-tabs v-model:activeKey="activeTab">
          <a-tab-pane key="dashboard" tab="Tổng quan"></a-tab-pane>
          <a-tab-pane key="sales" tab="Phân tích doanh thu"></a-tab-pane>
          <a-tab-pane key="products" tab="Phân tích sản phẩm"></a-tab-pane>
          <a-tab-pane key="customer" tab="Phân tích khách hàng"></a-tab-pane>
        </a-tabs>

        <!-- Toolbox Filter -->
        <ToolboxFilter />

        <!-- Overview -->
        <OverviewView />
      </div>
    </template>
  </MasterLayout>
</template>

<script setup>
import { MasterLayout, ToolboxFilter } from '@/components/backend';
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import OverviewView from './partials/OverviewView.vue';

const router = useRouter();
const activeTab = ref('dashboard');
const query = router.currentRoute.value.query;

watch(activeTab, (newValue) => {
  router.push({
    query: {
      ...query,
      acrive_tab: newValue
    }
  });
});
</script>
<style scoped></style>
