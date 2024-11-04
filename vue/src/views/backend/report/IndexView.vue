<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen">
        <h1 class="py-4 text-xl font-bold">Tổng quan báo cáo</h1>

        <a-tabs v-model:activeKey="activeTab">
          <a-tab-pane key="dashboard" tab="Tổng quan"></a-tab-pane>
          <a-tab-pane key="sales" tab="Phân tích doanh thu"></a-tab-pane>
          <a-tab-pane key="customer" tab="Phân tích khách hàng"></a-tab-pane>
        </a-tabs>

        <ToolboxFilter />

        <a-row :gutter="[16, 16]" class="report-block mt-3">
          <a-col :span="6">
            <a-card class="p-0">
              <div class="">
                <div class="flex items-center justify-between">
                  <div>
                    <span class="border-b border-dashed">Doanh thu thuần</span>
                    <TooltipComponent title="Doanh thu thuần" />
                  </div>
                  <RouterLink to="#" class="report-block-link">
                    <i class="far fa-arrow-circle-right"></i>
                  </RouterLink>
                </div>
                <h3 class="mb-0 text-xl font-bold">750,000₫</h3>
              </div>
            </a-card>
          </a-col>
          <a-col :span="6">
            <a-card class="p-0">
              <div class="">
                <div class="flex items-center justify-between">
                  <div>
                    <span class="border-b border-dashed">Lợi nhuận gộp</span>
                    <TooltipComponent title="Lợi nhuận gộp" />
                  </div>
                  <RouterLink to="#" class="report-block-link">
                    <i class="far fa-arrow-circle-right"></i>
                  </RouterLink>
                </div>
                <h3 class="mb-0 text-xl font-bold">750,000₫</h3>
              </div>
            </a-card>
          </a-col>
          <a-col :span="6">
            <a-card class="p-0">
              <div class="">
                <div class="flex items-center justify-between">
                  <div>
                    <span class="border-b border-dashed">Đơn hàng</span>
                    <TooltipComponent
                      title="Giá trị tồn kho
"
                    />
                  </div>
                  <RouterLink to="#" class="report-block-link">
                    <i class="far fa-arrow-circle-right"></i>
                  </RouterLink>
                </div>
                <h3 class="mb-0 text-xl font-bold">750,000₫</h3>
              </div>
            </a-card>
          </a-col>
          <a-col :span="6">
            <a-card class="p-0">
              <div class="">
                <div class="flex items-center justify-between">
                  <div>
                    <span class="border-b border-dashed">Giá trị tồn kho </span>
                    <TooltipComponent
                      title="Giá trị tồn kho
"
                    />
                  </div>
                  <RouterLink to="#" class="report-block-link">
                    <i class="far fa-arrow-circle-right"></i>
                  </RouterLink>
                </div>
                <h3 class="mb-0 text-xl font-bold">750,000₫</h3>
              </div>
            </a-card>
          </a-col>
        </a-row>

        <a-row :gutter="[16, 16]" class="report-block mt-3">
          <a-col :span="12">
            <a-card class="p-0">
              <div class="">
                <div class="flex items-center justify-between">
                  <div>
                    <span class="border-b border-dashed">Doanh thu thuần</span>
                    <TooltipComponent title="Doanh thu thuần" />
                  </div>
                  <RouterLink to="#" class="report-block-link">
                    <i class="far fa-arrow-circle-right"></i>
                  </RouterLink>
                </div>
                <h3 class="mb-0 text-xl font-bold">750,000₫</h3>
              </div>

              <div>
                <Line
                  id="my-chart-id"
                  :options="chartOptions"
                  :data="chartData"
                  style="height: 400px"
                />
              </div>
            </a-card>
          </a-col>
          <a-col :span="12">
            <a-card class="p-0">
              <div class="">
                <div class="flex items-center justify-between">
                  <div>
                    <span class="border-b border-dashed">Lợi nhuận gộp</span>
                    <TooltipComponent title="Lợi nhuận gộp" />
                  </div>
                  <RouterLink to="#" class="report-block-link">
                    <i class="far fa-arrow-circle-right"></i>
                  </RouterLink>
                </div>
                <h3 class="mb-0 text-xl font-bold">750,000₫</h3>
              </div>
              <div>
                <Line :options="chartOptions" :data="chartData" style="height: 400px" />
              </div>
            </a-card>
          </a-col>
          <a-col :span="12">
            <a-card class="p-0">
              <div class="">
                <div class="flex items-center justify-between">
                  <div>
                    <span class="border-b border-dashed">Lợi nhuận gộp</span>
                    <TooltipComponent title="Lợi nhuận gộp" />
                  </div>
                  <RouterLink to="#" class="report-block-link">
                    <i class="far fa-arrow-circle-right"></i>
                  </RouterLink>
                </div>
                <h3 class="mb-0 text-xl font-bold">750,000₫</h3>
              </div>
              <div>
                <Line :options="chartOptions" :data="chartData" style="height: 400px" />
              </div>
            </a-card>
          </a-col>
          <a-col :span="12">
            <a-card class="p-0">
              <div class="">
                <div class="flex items-center justify-between">
                  <div>
                    <span class="border-b border-dashed">Lợi nhuận gộp</span>
                    <TooltipComponent title="Lợi nhuận gộp" />
                  </div>
                  <RouterLink to="#" class="report-block-link">
                    <i class="far fa-arrow-circle-right"></i>
                  </RouterLink>
                </div>
                <h3 class="mb-0 text-xl font-bold">750,000₫</h3>
              </div>
              <div>
                <Line
                  id="my-chart-id"
                  :options="chartOptions"
                  :data="chartData"
                  style="height: 400px"
                />
              </div>
            </a-card>
          </a-col>
        </a-row>
      </div>
    </template>
  </MasterLayout>
</template>

<script setup>
import { MasterLayout, ToolboxFilter } from '@/components/backend';
import TooltipComponent from '@/components/backend/includes/TooltipComponent.vue';
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';
// import { useRoute } from 'vue-router';
import {
  CategoryScale,
  Chart as ChartJS,
  Legend,
  LinearScale,
  LineElement,
  PointElement,
  Title,
  Tooltip
} from 'chart.js';
import { Line } from 'vue-chartjs';
ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend);

const router = useRouter();
const activeTab = ref('dashboard');
const query = router.currentRoute.value.query;
const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false
});
const chartData = ref({
  labels: [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December'
  ],
  datasets: [
    {
      label: 'Data One',
      backgroundColor: '#f87979',
      data: [40, 20, 12, 39, 10, 40, 39, 80, 40, 20, 12, 11]
    }
  ]
});

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
