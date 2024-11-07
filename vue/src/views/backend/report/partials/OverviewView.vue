<template>
  <a-row :gutter="[16, 16]" class="report-block mt-3" v-if="overviewData">
    <a-col :span="6">
      <a-card class="p-0">
        <div class="">
          <div class="flex items-center justify-between">
            <div>
              <span class="border-b border-dashed">Doanh thu thuần</span>
              <TooltipComponent title="Doanh thu thuần" />
            </div>
            <RouterLink :to="{ name: 'report.sales' }" class="report-block-link">
              <i class="far fa-arrow-circle-right"></i>
            </RouterLink>
          </div>
          <h3 class="mb-0 text-xl font-bold">{{ formatCurrency(overviewData.net_revenue) }}</h3>
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
          <h3 class="mb-0 text-xl font-bold">{{ formatCurrency(overviewData.total_profit) }}</h3>
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
          <h3 class="mb-0 text-xl font-bold">{{ overviewData.total_orders }}</h3>
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
          <h3 class="mb-0 text-xl font-bold">
            {{ formatCurrency(overviewData.total_value_of_stock) }}
          </h3>
        </div>
      </a-card>
    </a-col>
  </a-row>
  <a-row :gutter="[16, 16]" class="report-block mt-3" v-else>
    <a-col :span="6">
      <a-skeleton active />
    </a-col>
    <a-col :span="6">
      <a-skeleton active />
    </a-col>
    <a-col :span="6">
      <a-skeleton active />
    </a-col>
    <a-col :span="6">
      <a-skeleton active />
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
          <canvas id="my-chart-id-1" style="height: 400px"></canvas>
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
          <canvas id="my-chart-id-2" style="height: 400px"></canvas>
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
          <canvas id="my-chart-id-3" style="height: 400px"></canvas>
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
          <canvas id="my-chart-id-4" style="height: 400px"></canvas>
        </div>
      </a-card>
    </a-col>
  </a-row>
</template>
<script setup>
import TooltipComponent from '@/components/backend/includes/TooltipComponent.vue';
import { useCRUD } from '@/composables';
import { formatCurrency } from '@/utils/format';
import { onMounted, ref } from 'vue';

const { getAll, data, messages } = useCRUD();
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false
};
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
      backgroundColor: '#0088ff',
      data: [40, 20, 12, 39, 10, 40, 39, 80, 40, 20, 12, 11]
    }
  ]
});
const overviewData = ref(null);

const getOverviewData = async () => {
  const response = await getAll('statistics/report-overview');

  if (response) {
    overviewData.value = response;
  }
};
onMounted(async () => {
  getOverviewData();

  const ctx = document.getElementById('my-chart-id-1').getContext('2d');
  const ctx2 = document.getElementById('my-chart-id-2').getContext('2d');
  const ctx3 = document.getElementById('my-chart-id-3').getContext('2d');
  const ctx4 = document.getElementById('my-chart-id-4').getContext('2d');

  // eslint-disable-next-line no-undef
  const myChart = new Chart(ctx, {
    type: 'line',
    data: chartData.value,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        title: {
          display: true,
          text: 'Tổng số đơn hàng'
        }
      }
    }
  });

  // eslint-disable-next-line no-undef
  const myChart2 = new Chart(ctx2, {
    type: 'line',
    data: chartData.value,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        title: {
          display: true,
          text: 'Tổng số đơn hàng'
        }
      }
    }
  });

  // eslint-disable-next-line no-undef
  const myChart3 = new Chart(ctx3, {
    type: 'line',
    data: chartData.value,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        title: {
          display: true,
          text: 'Tổng số đơn hàng'
        }
      }
    }
  });

  // eslint-disable-next-line no-undef
  const myChart4 = new Chart(ctx4, {
    type: 'line',
    data: chartData.value,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        title: {
          display: true,
          text: 'Tổng số đơn hàng'
        }
      }
    }
  });
});
</script>
