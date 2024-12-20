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
              <div class="inline-block rounded border p-1">
                <img
                  class="h-[50px] w-[50px] object-cover"
                  :src="resizeImage(record.image, 100)"
                  :alt="record.name"
                />
              </div>
            </template>

            <template v-if="column.dataIndex === 'name'">
              <RouterLink
                :to="{ name: 'post.update', params: { id: record.id } }"
                class="text-blue-500"
                >{{ record.name }}
              </RouterLink>
            </template>

            <template v-if="column.dataIndex === 'publish'">
              <StatusSwitchComponent
                :record="record"
                :modelName="state.modelName"
                :field="column.dataIndex"
              />
            </template>

            <template v-if="column.dataIndex === 'is_featured'">
              <StatusSwitchComponent
                :record="record"
                :modelName="state.modelName"
                :field="column.dataIndex"
              />
            </template>
          </template>

          <template v-if="column.dataIndex === 'publish'">
            <StatusSwitchComponent
              :record="record"
              :modelName="state.modelName"
              :field="column.dataIndex"
            />
          </template>

          <template v-if="column.dataIndex === 'is_featured'">
            <StatusSwitchComponent
              :record="record"
              :modelName="state.modelName"
              :field="column.dataIndex"
            />
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
import { debounce, resizeImage } from '@/utils/helpers';
import { onMounted, reactive, watch } from 'vue';
import { useRoute } from 'vue-router';

// STATE
const state = reactive({
  pageTitle: 'Danh sách bài viết',
  modelName: 'Post',
  routeCreate: 'post.store',
  routeUpdate: 'post.update',
  endpoint: 'posts',
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
    title: 'Bài viết',
    dataIndex: 'name',
    key: 'name'
  },
  {
    title: 'Đường dẫn',
    dataIndex: 'canonical',
    key: 'canonical',
    sorter: (a, b) => a.canonical.localeCompare(b.canonical)
  },
  {
    title: 'Người viết',
    dataIndex: 'user_name',
    key: 'user_name',
    sorter: (a, b) => a.user_name.localeCompare(b.user_name)
  },
  {
    title: 'Ngày tạo',
    dataIndex: 'created_at',
    key: 'created_at'
  },
  {
    title: 'Nổi bật',
    dataIndex: 'is_featured',
    key: 'is_featured',
    width: '5%'
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
    state.pageTitle =
      newValue === 'true' ? 'Danh sách lưu trữ thương hiệu' : 'Danh sách thương hiệu';
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
