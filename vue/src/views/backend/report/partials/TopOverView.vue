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
  <a-row :gutter="[16, 16]" class="report-block my-3" v-else>
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
</template>
<script setup>
import { TooltipComponent } from '@/components/backend';
import axios from '@/configs/axios';
import { formatCurrency } from '@/utils/format';
import { debounce } from '@/utils/helpers';
import { ref, watch } from 'vue';

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

const overviewData = ref(null);

const getOverviewData = async () => {
  try {
    const response = await axios.get('statistics/report-overview', {
      params: {
        date: props.date,
        start_date: props.startDate,
        end_date: props.endDate
      }
    });
    overviewData.value = response.data;
  } catch (error) {
    console.log(error);
  }
};

const debounceGetOverviewData = debounce(getOverviewData, 500);

watch(
  () => [props.date, props.startDate, props.endDate],
  () => {
    debounceGetOverviewData();
  },
  { immediate: true }
);
</script>
