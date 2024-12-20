<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen">
        <BreadcrumbComponent :titlePage="state.pageTitle" :routeCreate="state.routeCreate" />

        <!-- Toolbox -->
        <ToolboxComponent
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
            <template v-if="column.dataIndex === 'product_name'">
              <RouterLink
                :to="{ name: 'product.update', params: { id: record.product_id } }"
                class="text-blue-500"
              >
                {{ record.product_name }}
              </RouterLink>
            </template>

            <template v-if="column.dataIndex === 'comment'">
              <RouterLink
                :to="{ name: 'evaluate.replies', params: { id: record.id } }"
                class="text-blue-500 truncate max-w-[200px] block"
              >
                {{ record.comment }}
              </RouterLink>
            </template>

            <template v-if="column.dataIndex === 'status'">
              <RouterLink
                :to="{ name: 'evaluate.replies', params: { id: record.id } }"
                class="text-blue-500"
              >
                <a-tag :color="record.status.color">{{ record.status.text }}</a-tag>
              </RouterLink>
            </template>

            <template v-if="column.dataIndex === 'image'">
              <div v-if="record.images && record.images.length" class="flex gap-2">
                <img
                  v-for="(img, index) in record.images"
                  :key="index"
                  :src="resizeImage(img, 100)"
                  class="w-14 rounded border bg-[#f7f8fb] object-contain p-[2px] shadow-sm"
                  alt="Product Image"
                />
              </div>
            </template>

            <template v-if="column.dataIndex === 'publish'">
              <StatusSwitchComponent
                :record="record"
                :modelName="state.modelName"
                :field="column.dataIndex"
              />
            </template>

            <template v-if="column.dataIndex === 'rating'">
              <div class="flex">
                <span
                  v-for="n in 5"
                  :key="n"
                  :class="n <= record.rating ? 'text-yellow-500' : 'text-gray-300'"
                  class="text-lg"
                >
                  ★
                </span>
              </div>
            </template>
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
import { columns } from './columns';

import { resizeImage } from '@/utils/helpers';

import { useCRUD, usePagination } from '@/composables';
import { RouterLink } from 'vue-router';

const state = reactive({
  pageTitle: 'Danh sách đánh giá',
  modelName: 'ProductReview',
  routeUpdate: 'evaluate.replies',
  endpoint: 'product-reviews',
  routeCreate: '',
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
