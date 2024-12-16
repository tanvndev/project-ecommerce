<template>
  <a-col class="top-product" :span="16">
    <a-card class="p-0">
      <div>
        <div class="flex items-center justify-between">
          <div class="mb-3">
            <span class="border-b border-dashed">Top khách hàng thân thiết</span>
            <TooltipComponent title="Top khách hàng có nhiều đơn hàng" />
          </div>
          <RouterLink :to="{ name: 'report.top.loyal.customer' }" class="report-block-link">
            <i class="far fa-arrow-circle-right"></i>
          </RouterLink>
        </div>
      </div>

      <!-- Header row -->
      <div v-if="LoyalCustomer?.length">
        <div class="flex items-center justify-between border-b bg-gray-100 py-2 font-bold">
          <div>
            <p>Khách hàng</p>
          </div>
          <div>
            <p>Số đơn hàng</p>
          </div>
          <div>
            <p>Tổng chi tiêu</p>
          </div>
          <div>
            <p>Chi tiêu trung bình</p>
          </div>
        </div>

        <!-- Data rows -->
        <div
          class="flex items-center justify-between border-b py-[14.5px] last:border-none"
          v-for="(item, index) in LoyalCustomer"
          :key="index"
        >
          <div>
            <p class="truncate">{{ item.customer_name }}</p>
          </div>
          <div>
            <p class="truncate">{{ item.total_orders }} đơn hàng</p>
          </div>
          <div>
            <p class="font-bold">{{ formatCurrency(item.total_spent) }}</p>
          </div>
          <div>
            <p class="truncate">{{ formatCurrency(item.average_spent) }}</p>
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

const LoyalCustomer = ref([]);

const getData = async () => {
  try {
    const response = await axios.get('/statistics/loyal-customers', {
      params: {
        date: props.date,
        start_date: props.startDate,
        end_date: props.endDate,
        pageSize: 5
      }
    });

    LoyalCustomer.value = response.data?.data;
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
