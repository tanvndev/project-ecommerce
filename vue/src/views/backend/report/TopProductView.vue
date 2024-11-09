<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen">
        <div class="flex items-center justify-between py-5">
          <div class="flex items-center">
            <a-button class="mr-3" @click="router.push({ name: 'report.index' })">
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

        <a-table
          class="mt-4"
          :dataSource="dataProduct"
          :columns="columns"
          :loading="isLoading"
          :pagination="pagination"
          @change="handleTableChange"
        >
          <template #bodyCell="{ column, record }">
            <template v-if="column.dataIndex === 'order_date'">
              {{ dayjs(record.order_date).format('DD/MM/YYYY') }}
            </template>
            <template v-if="column.dataIndex === 'net_revenue'">
              {{ formatCurrency(record.net_revenue) }}
            </template>
            <template v-if="column.dataIndex === 'total_profit'">
              {{ formatCurrency(record.total_profit) }}
            </template>
            <template v-if="column.dataIndex === 'total_discount'">
              {{ formatCurrency(record.total_discount) }}
            </template>
            <template v-if="column.dataIndex === 'total_shipping_fee'">
              {{ formatCurrency(record.total_shipping_fee) }}
            </template>
          </template>
        </a-table>
      </div>
    </template>
  </MasterLayout>
</template>
<script setup>
import { MasterLayout, ToolboxFilter } from '@/components/backend';
import axios from '@/configs/axios';
import { formatCurrency } from '@/utils/format';
import { debounce } from '@/utils/helpers';
import dayjs from 'dayjs';
import { computed, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const router = useRouter();
const route = useRoute();
const chartFor = ref('day');
const date = computed(() => route.query.date || 'last_30_days');
const start_date = computed(() => route.query.start_date || '');
const end_date = computed(() => route.query.end_date || '');
const isLoading = ref(false);
const columns = [
  {
    title: 'Sản phẩm',
    dataIndex: 'product_variant_name',
    key: 'product_variant_name'
  },
  {
    title: 'Giảm giá',
    dataIndex: 'total_discount',
    key: 'total_discount'
  },
  {
    title: 'Số lượng đơn',
    key: 'total_quantity',
    dataIndex: 'total_quantity'
  },
  {
    title: 'Phí giao hàng',
    key: 'total_shipping_fee',
    dataIndex: 'total_shipping_fee'
  },
  {
    title: 'Doanh thu thuần',
    dataIndex: 'total_profit',
    key: 'total_profit'
  }
];
const pageSize = ref(20);
const currentPage = ref(1);
const totalPages = ref(0);

const pagination = computed(() => ({
  total: totalPages.value,
  current: currentPage.value,
  pageSize: pageSize.value
}));

const dataProduct = ref([]);

const fetchData = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get('/statistics/products?condition=product_sell_best', {
      params: {
        date: date.value,
        start_date: start_date.value,
        end_date: end_date.value,
        page: currentPage.value,
        pageSize: pageSize.value
      }
    });

    dataProduct.value = response.data?.data;
    totalPages.value = response.data?.total;
  } catch (error) {
    console.log(error);
  } finally {
    isLoading.value = false;
  }
};

const debounceGetData = debounce(fetchData, 500);

const handleTableChange = async ({ current, pageSize }) => {
  currentPage.value = current;
  pageSize.value = pageSize;

  debounceGetData();
};

const handleOnChangeDate = async ({ allDay }) => {
  debounceGetData();
};
</script>
