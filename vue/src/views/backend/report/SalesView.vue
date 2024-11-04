<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen">
        <div class="flex items-center justify-between py-5">
          <div class="flex items-center">
            <a-button class="mr-3" @click="router.back()">
              <i class="fas fa-arrow-left text-gray-500"></i>
            </a-button>
            <h2 class="text-[18px] uppercase leading-none">Doanh thu theo thời gian</h2>
          </div>
          <div>
            <a-button class="mr-3" type="text">
              <i class="fas fa-download mr-2 text-gray-500"></i>
              Xuất báo cáo
            </a-button>
          </div>
        </div>

        <div class="flex justify-between">
          <!-- Toolbox Filter -->
          <ToolboxFilter @onChangeDate="handleOnChangeDate" />
          <a-select style="width: 150px" size="large" placeholder="Dạng biểu đồ" v-model="chartFor">
            <a-select-option value="day">Theo ngày</a-select-option>
            <a-select-option value="month">Theo tháng</a-select-option>
            <a-select-option value="year">Theo năm</a-select-option>
          </a-select>
        </div>
        <a-card class="mt-3">
          <h3 class="mb-2 font-bold">Tổng doanh thu</h3>

          <div>
            <!-- <Bar
              :data="dataChart"
              ref="barChartRef"
              :options="chartOptions"
              style="height: 400px"
            /> -->
            <canvas id="bar-chart"></canvas>
          </div>
        </a-card>

        <a-divider></a-divider>

        <a-table :dataSource="dataSource" :columns="columns" />
      </div>
    </template>
  </MasterLayout>
</template>
<script setup>
import { MasterLayout, ToolboxFilter } from '@/components/backend';
import { useRoute, useRouter } from 'vue-router';

const router = useRouter();
const chartFor = ref('day');
const columns = [
  {
    title: 'Ngày',
    dataIndex: 'ordered_at',
    key: 'ordered_at'
  },
  {
    title: 'Số lượng đơn',
    dataIndex: 'total_orders',
    key: 'total_orders'
  },
  {
    title: 'Tiền hàng',
    dataIndex: 'total_amount',
    key: 'total_amount'
  },
  {
    title: 'Giảm giá',
    key: 'discount',
    dataIndex: 'discount'
  },
  {
    title: 'Doanh thu thuần',
    key: 'net_revenue',
    dataIndex: 'net_revenue'
  },
  {
    title: 'Phí giao hàng',
    key: 'shipping_price',
    dataIndex: 'shipping_price'
  },
  {
    title: 'Tổng doanh thu',
    key: 'total_revenue',
    dataIndex: 'total_revenue'
  },
  {
    title: 'Lợi nhuận gộp',
    key: 'total_revenue_detail',
    dataIndex: 'total_revenue_detail'
  }
];
const dataSource = [];

import { computed, nextTick, onMounted, ref, watch } from 'vue';

const dataChart = ref({
  labels: [],
  datasets: [
    {
      label: 'Doanh thu',
      backgroundColor: '#0088ff',
      data: []
    }
  ],
  options: {
    responsive: true,
    maintainAspectRatio: false,
    lineTension: 1
  }
});

let chartInstance = null;

const handleOnChangeDate = async ({ allDay }) => {
  dataChart.value.labels = allDay;

  await fetchRevenueData(allDay);
  console.log(dataChart.value);

  const ctx = document.getElementById('bar-chart');

  if (chartInstance) {
    chartInstance.data.labels = dataChart.value.labels;
    chartInstance.data.datasets = dataChart.value.datasets; // Make sure you update datasets as well
    chartInstance.update();
  } else {
    // eslint-disable-next-line no-undef
    chartInstance = new Chart(ctx, {
      type: 'bar',
      data: dataChart.value,
      options: {
        responsive: true
      }
    });
  }

  console.log(dataChart.value);
};

// Ensure chartInstance is defined outside the function scope
const fetchRevenueData = async (dates) => {
  dataChart.value.datasets[0].data = dates.map((date) => Math.floor(Math.random() * 100)); // Thay thế bằng dữ liệu thực tế
};
onMounted(() => {});
</script>
