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
            <template v-if="column.dataIndex === 'catalogue_name'">
              <a-tag color="blue">{{ record.catalogue_name }}</a-tag>
            </template>

            <!-- <template v-if="column.dataIndex === 'publish'">
              <StatusSwitchComponent
                :record="record"
                :modelName="state.modelName"
                :field="column.dataIndex"
              />
            </template>

            <template v-if="column.dataIndex === 'action'">
              <ActionComponent
                @onDelete="onDelete"
                :id="record.id"
                :routeUpdate="state.routeUpdate"
                :endpoint="state.endpoint"
              />
            </template> -->
          </template>

          <template #expandedRowRender="{ record }">
            <h2>Địa chỉ giao hàng</h2>
            <ul class="mb-0 ml-10 mt-3 list-disc" v-if="record?.addresses?.length">
              <li class="mb-2" v-for="address in record.addresses" :key="address.id">
                <span class="capitalize"> {{ address.fullname }} - </span>
                <span class="text-primary-500"> {{ address.phone }} - </span>
                <span>
                  {{ address.shipping_address }}
                </span>
              </li>
            </ul>
            <div class="ml-10 text-red-500" v-else>Chưa có sẵn địa chỉ.</div>
          </template>
        </a-table>
        <!-- End table -->
      </div>
    </template>
  </MasterLayout>
</template>

<script setup>
import {
  ActionComponent,
  BreadcrumbComponent,
  MasterLayout,
  StatusSwitchComponent,
  ToolboxComponent
} from '@/components/backend';
import { useCRUD, usePagination } from '@/composables';
import { debounce } from '@/utils/helpers';
import { onMounted, reactive, watch } from 'vue';
import { useRoute } from 'vue-router';

// STATE
const state = reactive({
  pageTitle: 'Danh sách thành viên',
  modelName: 'User',
  routeCreate: 'user.store',
  routeUpdate: 'user.update',
  endpoint: 'users',
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
    title: 'Tên thành viên',
    dataIndex: 'fullname',
    key: 'fullname',
    sorter: (a, b) => a.fullname.localeCompare(b.fullname)
  },
  {
    title: 'Nhóm thành viên',
    dataIndex: 'catalogue_name',
    key: 'catalogue_name'
  },
  {
    title: 'Địa chỉ email',
    dataIndex: 'email',
    key: 'email',
    sorter: (a, b) => a.email.localeCompare(b.email)
  },
  {
    title: 'Số điện thoại',
    dataIndex: 'phone',
    key: 'phone',
    sorter: (a, b) => a.phone.localeCompare(b.phone)
  },
//   {
//     title: 'Tình trạng',
//     dataIndex: 'publish',
//     key: 'publish',
//     width: '7%'
//   },
//   {
//     title: 'Thực thi',
//     dataIndex: 'action',
//     key: 'action',
//     width: '6%'
//   }
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
  const response = await getAll(state.endpoint + '/admin/index', payload);
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

const onDelete = (key) => {
  state.dataSource = state.dataSource.filter((item) => item.key !== key);
};

// Lifecycle hook
onMounted(fetchData);
</script>
