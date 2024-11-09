<template>
  <a-col :span="12">
    <a-card class="p-0">
      <div class="">
        <div class="flex items-center justify-between">
          <div class="mb-3">
            <span class="border-b border-dashed">Top sản phẩm bán chạy</span>
            <TooltipComponent title="Top sản phẩm có Doanh thu cao nhất" />
          </div>
          <RouterLink :to="{ name: 'report.top.product' }" class="report-block-link">
            <i class="far fa-arrow-circle-right"></i>
          </RouterLink>
        </div>
      </div>

      <div v-if="dataProduct.length > 0">
        <div
          class="flex items-center justify-between border-b py-[14.5px] last:border-none"
          v-for="(item, index) in dataProduct"
          :key="index"
        >
          <div>
            <p class="truncate w-[450px]">{{ item.product_variant_name }}</p>
          </div>
          <div class="flex items-center">
            <div>
              <p class="font-bold ">{{ formatCurrency(item.net_revenue) }} </p>
              <span class="text-gray-400"> {{ item.total_quantity }} sản phẩm </span>
            </div>

            <div class="ml-24">
              <i class="fas fa-minus text-gray-400"></i>
            </div>
          </div>
        </div>
      </div>
      <a-skeleton active v-else />
    </a-card>
  </a-col>
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

const dataProduct = ref([]);

const getData = async () => {
  try {
    const response = await axios.get('/statistics/products?condition=product_sell_best', {
      params: {
        date: props.date,
        start_date: props.startDate,
        end_date: props.endDate,
        pageSize: 5
      }
    });
    dataProduct.value = response.data?.data;
  } catch (error) {
    console.log(error);
  }
};

const debounceGetData = debounce(getData, 500);

watch(
  () => [props.date, props.startDate, props.endDate],
  () => {
    debounceGetData();
  },
  { immediate: true }
);
</script>
