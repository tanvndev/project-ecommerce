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
                  :to="{ name: 'product.update', params: { id: record.id } }"
                  class="text-blue-500"
                  >{{ record.name }}</RouterLink
                >
              </template>
              
              <template v-if="column.dataIndex === 'publish'">
                <StatusSwitchComponent
                  :record="record"
                  :modelName="state.modelName"
                  :field="column.dataIndex"
                />
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
  import { columns } from './columns';
  import {
    BreadcrumbComponent,
    MasterLayout,
    StatusSwitchComponent,
    ToolboxComponent
  } from '@/components/backend';
  import { useCRUD, usePagination } from '@/composables';
  import { RouterLink } from 'vue-router';

  
  // STATE
  const state = reactive({
    pageTitle: 'Danh sách Flash Sale',
    modelName: 'flash-sales',
    routeCreate: 'flash-sale.create',
    routeUpdate: 'fash-sale.update',
    endpoint: 'flash-sales',
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
  