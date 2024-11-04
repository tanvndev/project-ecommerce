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
            <template v-if="column.dataIndex === 'name'">
              <RouterLink
                class="text-primary-500"
                :to="{ name: 'attribute.update', params: { id: record.id } }"
              >
                {{ record.name }}
              </RouterLink>
            </template>
          </template>

          <template #expandedRowRender="{ record }">
            <h2>Giá trị thuộc tính</h2>
            <ul
              class="mb-0 ml-10 mt-3 flex list-disc flex-wrap gap-x-7 gap-y-3"
              v-if="record.values.length"
            >
              <li class="text-primary-500" v-for="value in record.values" :key="value.id">
                <RouterLink :to="{ name: 'attribute.value.update', params: { id: value.id } }">
                  {{ value.name }}
                </RouterLink>
              </li>
            </ul>
            <div class="ml-10 text-red-500" v-else>Chưa có giá trị thuộc tính</div>
          </template>
        </a-table>
        <!-- End table -->
      </div>
    </template>
  </MasterLayout>
</template>

<script setup>
import { BreadcrumbComponent, MasterLayout, ToolboxComponent } from '@/components/backend';
import { useCRUD, usePagination } from '@/composables';
import { onMounted, reactive, watch } from 'vue';

// STATE
const state = reactive({
  pageTitle: 'Danh sách thuộc tính',
  modelName: 'Attribute',
  routeCreate: 'attribute.store',
  routeUpdate: 'attribute.update',
  endpoint: 'attributes',
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
    title: 'Tên thuộc tính',
    dataIndex: 'name',
    key: 'name',
    sorter: (a, b) => a.name.localeCompare(b.name)
  },
  {
    title: 'Mã thuộc tính',
    dataIndex: 'code',
    key: 'code',
    sorter: (a, b) => a.code.localeCompare(b.code)
  },
  {
    title: 'Mô tả thuộc tính',
    dataIndex: 'description',
    key: 'description',
    sorter: (a, b) => a.description.localeCompare(b.description)
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
