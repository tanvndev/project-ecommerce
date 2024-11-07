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
            <canvas id="bar-chart"></canvas>
          </div>
        </a-card>

        <!-- <div v-else class="mt-3">
          <a-skeleton active />
        </div> -->

        <a-divider></a-divider>

        <a-table :dataSource="dataSource" :columns="columns" v-if="!isLoading"></a-table>

        <a-skeleton active v-else />
      </div>
    </template>
  </MasterLayout>
</template>
<script setup>
import { MasterLayout, ToolboxFilter } from '@/components/backend';
import axios from '@/configs/axios';
import { computed, nextTick, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const router = useRouter();
const route = useRoute();
const chartFor = ref('day');
const date = computed(() => route.query.date);
const isLoading = ref(false);
const columns = [
  {
    title: 'Ngày',
    dataIndex: 'order_date',
    key: 'order_date'
  },
  {
    title: 'Số lượng đơn',
    dataIndex: 'total_orders',
    key: 'total_orders'
  },
  {
    title: 'Doanh thu thuần',
    dataIndex: 'net_revenue',
    key: 'net_revenue'
  },
  {
    title: 'Giảm giá',
    key: 'total_discount',
    dataIndex: 'total_discount'
  },
  {
    title: 'Doanh thu thuần',
    key: 'total_profit',
    dataIndex: 'total_profit'
  },
  {
    title: 'Phí giao hàng',
    key: 'total_shipping_fee',
    dataIndex: 'total_shipping_fee'
  },

  {
    title: 'Lợi nhuận gộp',
    key: 'total_profit',
    dataIndex: 'total_profit'
  }
];
const dataSource = ref([]);

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

  await fetchRevenueData();
  await nextTick();

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
};

const fetchRevenueData = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get('/statistics/revenue-by-date', {
      params: {
        date: date.value,
        chart: true
      }
    });

    dataChart.value.datasets[0].data = response.data?.chartData;
    dataSource.value = response.data?.data?.data;
  } catch (error) {
    console.log(error);
  } finally {
    isLoading.value = false;
  }
};
</script>
