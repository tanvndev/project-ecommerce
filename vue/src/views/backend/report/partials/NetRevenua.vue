<template>
  <a-col :span="12">
    <a-card class="p-0">
      <div class="">
        <div class="flex items-center justify-between">
          <div class="mb-3">
            <span class="border-b border-dashed">Doanh thu thuần</span>
            <TooltipComponent title="Doanh thu thuần" />
          </div>
          <RouterLink to="#" class="report-block-link">
            <i class="far fa-arrow-circle-right"></i>
          </RouterLink>
        </div>
      </div>

      <div>
        <canvas id="line-chart-net-revenua" style="height: 400px"></canvas>
      </div>
    </a-card>
  </a-col>
</template>

<script setup>
import { TooltipComponent } from '@/components/backend';
import axios from '@/configs/axios';
import { debounce } from '@/utils/helpers';
import { computed, nextTick, ref, watch } from 'vue';
import { useStore } from 'vuex';

const props = defineProps({
  date: {
    type: String,
    default: 'last_30_days'
  },
  startDate: {
    type: String,
    default: ''
  },
  endDate: {
    type: String,
    default: ''
  }
});

const store = useStore();
const allDay = computed(() => store.getters['reportStore/getAllDayFormat']);

const dataChart = ref({
  labels: [],
  datasets: [
    {
      label: 'Doanh thu',
      backgroundColor: '#0088ff',
      data: [],
      fill: 1,
      tension: 0.5
    }
  ],
  options: {
    responsive: true,
    maintainAspectRatio: false,
    lineTension: 1,
    interaction: {
      intersect: true,
      mode: 'index'
    }
  }
});

const fetchData = async () => {
  try {
    const response = await axios.get('statistics/revenue-by-date', {
      params: {
        date: props.date,
        start_date: props.startDate,
        end_date: props.endDate,
        chart: true,
        pageSize: 10,
        chartColumn: 'net_revenue'
      }
    });
    dataChart.value.datasets[0].data = response.data?.chartData;
  } catch (error) {
    console.log(error);
  }
};

let chartInstance = null;
const getDataChart = async () => {
  dataChart.value.labels = allDay.value;

  await fetchData();
  await nextTick();

  const ctx = document.getElementById('line-chart-net-revenua');

  if (chartInstance) {
    chartInstance.data.labels = dataChart.value.labels;
    chartInstance.data.datasets = dataChart.value.datasets; // Make sure you update datasets as well
    chartInstance.update();
  } else {
    // eslint-disable-next-line no-undef
    chartInstance = new Chart(ctx, {
      type: 'line',
      data: dataChart.value,
      options: {
        responsive: true
      }
    });
  }
};

const debounceGetDataChart = debounce(getDataChart, 500);

watch(
  () => [props.date, props.startDate, props.endDate],
  () => {
    debounceGetDataChart();
  },
  { immediate: true }
);
</script>
