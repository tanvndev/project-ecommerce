<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen">
        <BreadcrumbComponent :titlePage="state.pageTitle" />

        <!-- Table -->
        <a-table
          bordered
          :columns="columns"
          :data-source="state.dataSource"
          :pagination="pagination"
          :loading="loading"
          @change="handleTableChange"
          class="components-table-demo-nested mt-2"
        >
          <template #bodyCell="{ column, record }">

            <template v-if="column.key === 'name'">
                <div class="flex items-center">
                  <div class="rounded border p-1 mr-2">
                    <img
                      class="h-[50px] w-[50px] object-cover"
                      :src="resizeImage(record.image, 100)"
                    />
                  </div>
                  {{ record.name }}
                </div>
              </template>
            <!-- <template v-if="column.dataIndex === 'status'">
             
            </template> -->
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
import { onMounted, reactive, watch } from 'vue';

import { resizeImage } from '@/utils/helpers';

import { useCRUD, usePagination } from '@/composables';
import { RouterLink } from 'vue-router';

const columns = [
  {
    title: 'Sản phẩm',
    dataIndex: 'name',
    key: 'name',
    sorter: (a, b) => a.name.localeCompare(b.name)
  },
  {
    title: 'Tồn kho',
    dataIndex: 'stock',
    key: 'stock'
  },
 
  {
    title: 'Lượt bán',
    dataIndex: 'total_sold',
    key: 'total_sold'
  },
  {
    title: 'Trạng thái',
    dataIndex: 'status',
    key: 'status'
  }
];

const state = reactive({
  pageTitle: 'Danh sách sản phẩm tồn kho',
  endpoint: 'statistics/low-and-out-of-stock-variants',
  isShowToolbox: false,
  modelIds: [],
  filterOptions: {},
  dataSource: []
});

// CRUD Operations
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
watch(onChangePagination, fetchData);
watch(selectedRows, () => {
  state.isShowToolbox = selectedRows.value.length > 0;
  state.modelIds = selectedRowKeys.value;
});

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
