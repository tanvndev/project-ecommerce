<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen">
        <h1 class="py-5 text-[18px] uppercase">Phân tích khách hàng</h1>

        <a-tabs v-model:activeKey="activeTab">
          <a-tab-pane key="user" tab="Phân tích khách hàng"></a-tab-pane>
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
import OverviewView from './partials/dashboard/OverviewView.vue';
</script>
<style scoped></style>
