<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 h-screen">
        <BreadcrumbComponent :titlePage="state.pageTitle" :routeCreate="state.routeCreate" />

        <!-- Toolbox -->
        <ToolboxComponent
          :routeCreate="state.routeCreate"
          :modelName="state.modelName"
          :isShowToolbox="state.isShowToolbox"
          :modelIds="state.modelIds"
          @onFilter="onFilterOptions"
          @onChangeToolbox="onChangeToolbox"
        />
        <!-- End toolbox -->

        <!-- Table -->
        <a-table
          bordered
          class="mt-2"
          :columns="columns"
          :data-source="state.dataSource"
          :row-selection="rowSelection"
          :pagination="pagination"
          :loading="loading"
          @change="handleTableChange"
        >
          <template #bodyCell="{ column, record }">
            <template v-if="column.dataIndex === 'email'">
              <ul class="mb-0">
                <li class="capitalize">{{ record.customer_name }}</li>
                <li>
                  <a class="text-blue-500" href="mailto:{{ record.customer_email }}">{{
                    record.customer_email
                  }}</a>
                </li>
                <li>{{ record.customer_phone }}</li>
              </ul>
            </template>

            <template v-if="column.dataIndex === 'order_status'">
              <a-tag :color="record.order_status_color">{{ record.order_status }}</a-tag>
            </template>
            <template v-if="column.dataIndex === 'payment_status'">
              <a-tag :color="record.payment_status_color">{{ record.payment_status }}</a-tag>
            </template>
            <template v-if="column.dataIndex === 'total_price'">
              {{ formatCurrency(record.total_price) }}
            </template>
            <template v-if="column.dataIndex === 'shipping_fee'">
              {{ formatCurrency(record.shipping_fee) }}
            </template>
            <template v-if="column.dataIndex === 'discount'">
              {{ formatCurrency(record.discount) }}
            </template>
            <template v-if="column.dataIndex === 'final_price'">
              {{ formatCurrency(record.final_price) }}
            </template>
            <template v-if="column.dataIndex === 'action'">
              <RouterLink
                class="rounded-[6px] bg-primary-500 px-[8px] py-[7px] text-white hover:bg-primary-400 hover:text-white"
                :to="{ name: 'order.update', params: { code: record.code } }"
              >
                <i class="fas fa-edit"></i
              ></RouterLink>
            </template>
          </template>
        </a-table>
        <!-- End table -->
      </div>
    </template>
  </MasterLayout>
</template>

<script setup>
import { onMounted, reactive, watch } from 'vue';
import { BreadcrumbComponent, MasterLayout, ToolboxComponent } from '@/components/backend';
import { useCRUD, usePagination } from '@/composables';
import { formatCurrency } from '@/utils/format';

// STATE
const state = reactive({
  pageTitle: 'Danh sách đơn hàng',
  modelName: 'Order',
  routeCreate: 'order.store',
  routeUpdate: 'order.update',
  endpoint: 'orders',
  isShowToolbox: false,
  modelIds: [],
  filterOptions: {},
  dataSource: []
});

const columns = [
  {
    title: 'ID',
    dataIndex: 'id',
    key: 'id',
    sorter: (a, b) => a.id - b.id,
    width: '5%'
  },
  {
    title: 'Email',
    dataIndex: 'email',
    key: 'email'
  },
  {
    title: 'Tổng tiền',
    dataIndex: 'total_price',
    key: 'total_price',
    sorter: (a, b) => a.total_price.localeCompare(b.total_price)
  },
  {
    title: 'Phí vận chuyển',
    dataIndex: 'shipping_fee',
    key: 'shipping_fee',
    sorter: (a, b) => a.shipping_fee.localeCompare(b.shipping_fee)
  },
  {
    title: 'Giảm giá',
    dataIndex: 'discount',
    key: 'discount',
    sorter: (a, b) => a.discount.localeCompare(b.discount)
  },
  {
    title: 'Tổng cuối',
    dataIndex: 'final_price',
    key: 'final_price',
    sorter: (a, b) => a.final_price.localeCompare(b.final_price)
  },
  {
    title: 'Thanh toán',
    dataIndex: 'payment_method_name',
    key: 'payment_method_name'
  },
  {
    title: 'Trạng thái thanh toán',
    dataIndex: 'payment_status',
    key: 'payment_status'
  },
  {
    title: 'Trạng thái đơn',
    dataIndex: 'order_status',
    key: 'order_status'
  },
  {
    title: 'Ngày đặt',
    dataIndex: 'ordered_at',
    key: 'ordered_at'
  },
  {
    title: 'Thực thi',
    dataIndex: 'action',
    key: 'action',
    width: '6%'
  }
];

const { getAll, loading } = useCRUD();

// Pagination
const {
  pagination,
  rowSelection,
  handleTableChange,
  onChangePagination,
  selectedRowKeys,
  selectedRows
} = usePagination();

// Fetchdata
const fetchData = async () => {
  const payload = {
    page: pagination.current,
    pageSize: pagination.pageSize,
    ...state.filterOptions
  };
  const response = await getAll(state.endpoint, payload);
  state.dataSource = response.data;
  pagination.current = response.current_page;
  pagination.total = response.total;
  pagination.pageSize = response.per_page;
};

// Watchers
watch(onChangePagination, () => fetchData());
watch(selectedRows, () => {
  state.isShowToolbox = selectedRows.value.length > 0;
  state.modelIds = selectedRowKeys.value;
});

const onFilterOptions = (filterValue) => {
  state.filterOptions = filterValue;
  fetchData();
};

const onChangeToolbox = () => {
  fetchData();
};

// Lifecycle hook
onMounted(fetchData);
</script>
