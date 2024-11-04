<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen">
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
          :columns="columns"
          :data-source="state.dataSource"
          :row-selection="rowSelection"
          :pagination="pagination"
          :loading="loading"
          @change="handleTableChange"
          class="components-table-demo-nested mt-2"
        >
          <template #bodyCell="{ column, record }">
            <template v-if="column.dataIndex === 'name'">
              <RouterLink
                :to="{ name: 'flash-sale.update', params: { id: record.id } }"
                class="text-blue-500"
                >{{ record.name }}</RouterLink
              >
            </template>

            <template v-if="column.dataIndex === 'status'">
              <a-tag :color="record.status.color">{{ record.status.text }}</a-tag>
            </template>
            <template v-if="column.dataIndex === 'total_product'">
              {{ record.product_variants?.length }}
            </template>

            <template v-if="column.dataIndex === 'publish'">
              <StatusSwitchComponent
                :record="record"
                :modelName="state.modelName"
                :field="column.dataIndex"
              />
            </template>
          </template>

          <template #expandedRowRender="{ record }">
            <a-table
              :columns="innerColumns"
              :data-source="record.product_variants"
              :pagination="false"
            >
              <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'sale_price'">
                  <span class="font-bold">{{ formatCurrency(record.sale_price) }}</span>
                </template>
              </template>
            </a-table>
          </template>
        </a-table>

        <!-- End table -->
      </div>
    </template>
  </MasterLayout>
</template>

<script setup>
import {
  BreadcrumbComponent,
  MasterLayout,
  StatusSwitchComponent,
  ToolboxComponent
} from '@/components/backend';
import { useCRUD, usePagination } from '@/composables';
import { formatCurrency } from '@/utils/format';
import { debounce } from '@/utils/helpers';
import { onMounted, reactive, watch } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { columns, innerColumns } from './columns';

// STATE
const state = reactive({
  pageTitle: 'Danh sách Flash Sale',
  modelName: 'FlashSale',
  routeCreate: 'flash-sale.create',
  routeUpdate: 'fash-sale.update',
  endpoint: 'flash-sales',
  isShowToolbox: false,
  modelIds: [],
  filterOptions: {},
  dataSource: []
});

const route = useRoute();
const { getAll, loading } = useCRUD();

// Pagination
const {
  pagination,
  rowSelection,
  onChangePagination,
  selectedRowKeys,
  selectedRows,
  handleTableChange
} = usePagination();

// Fetch data
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

const deboucedFetchData = debounce(fetchData, 500);

// Watchers
watch(onChangePagination, fetchData);
watch(selectedRows, () => {
  state.isShowToolbox = selectedRows.value.length > 0;
  state.modelIds = selectedRowKeys.value;
});
watch(
  () => route.query.archive,
  (newValue) => {
    state.filterOptions.archive = newValue === 'true' ? true : false;
    state.pageTitle =
      newValue === 'true' ? 'Danh sách lưu trữ thương hiệu' : 'Danh sách thương hiệu';
    deboucedFetchData();
  }
);

// Event Handlers
const onFilterOptions = (filterValue) => {
  state.filterOptions = filterValue;
  fetchData();
};

const onChangeToolbox = () => {
  fetchData();
};

// Lifecycle Hook
onMounted(fetchData);
</script>
