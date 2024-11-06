<script setup>
import {
  BreadcrumbComponent,
  EditOrderComponent,
  MasterLayout,
  StatusSwitchComponent,
  ToolboxComponent
} from '@/components/backend';
import { useCRUD, usePagination } from '@/composables';
import { WIDGET_MODEL, WIDGET_TYPE } from '@/static/constants';
import { debounce, resizeImage } from '@/utils/helpers';
import { onMounted, reactive, watch } from 'vue';
import { RouterLink, useRoute } from 'vue-router';

// STATE
const state = reactive({
  pageTitle: 'Danh sách widget',
  modelName: 'Widget',
  routeCreate: 'widget.store',
  routeUpdate: 'widget.update',
  endpoint: 'widgets',
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
    title: 'Tên widget',
    dataIndex: 'name',
    key: 'name'
  },
  {
    title: 'Loại widget',
    dataIndex: 'type',
    key: 'type'
  },
  {
    title: 'Tổng sản phẩm',
    dataIndex: 'productCount',
    key: 'productCount',
    sorter: (a, b) => a.productCount - b.productCount
  },
  {
    title: 'Vị trí',
    dataIndex: 'order',
    key: 'order',
    width: '8%'
  },
  {
    title: 'Tình trạng',
    dataIndex: 'publish',
    key: 'publish',
    width: '7%'
  }
];

const { getAll, loading } = useCRUD();
const route = useRoute();
const typeName = (type) => WIDGET_TYPE.find((item) => item.value === type)?.label || '-';
const modelName = (model) => WIDGET_MODEL.find((item) => item.value === model)?.label || '-';

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

const deboucedFetchData = debounce(fetchData, 500);

// Watchers
watch(onChangePagination, () => fetchData());
watch(selectedRows, () => {
  state.isShowToolbox = selectedRows.value.length > 0;
  state.modelIds = selectedRowKeys.value;
});
watch(
  () => route.query.archive,
  (newValue) => {
    state.filterOptions.archive = newValue === 'true' ? true : false;
    state.pageTitle = newValue === 'true' ? 'Danh sách lưu trữ widget' : 'Danh sách widget';
    deboucedFetchData();
  }
);

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
          class="mt-2"
          :columns="columns"
          :data-source="state.dataSource"
          :row-selection="rowSelection"
          :pagination="pagination"
          :loading="loading"
          @change="handleTableChange"
        >
          <template #bodyCell="{ column, record }">
            <template v-if="column.dataIndex === 'image'">
              <img
                class="w-20 object-contain"
                :src="resizeImage(record.image, 100)"
                :alt="record.name"
              />
            </template>

            <template v-if="column.dataIndex === 'publish'">
              <StatusSwitchComponent
                :record="record"
                :modelName="state.modelName"
                :field="column.dataIndex"
              />
            </template>

            <template v-if="column.dataIndex === 'name'">
              <RouterLink
                :to="{ name: 'widget.update', params: { id: record.id } }"
                class="text-blue-500"
                >{{ record.name }}</RouterLink
              >
            </template>
            <template v-if="column.dataIndex === 'type'">
              {{ typeName(record.type) }}
            </template>

            <template v-if="column.dataIndex === 'model'">
              {{ modelName(record.model) }}
            </template>

            <template v-if="column.dataIndex === 'order'">
              <EditOrderComponent
                :record="record"
                :dataSource="state.dataSource"
                :endpoint="state.endpoint"
              />
            </template>
          </template>
        </a-table>
        <!-- End table -->
      </div>
    </template>
  </MasterLayout>
</template>
