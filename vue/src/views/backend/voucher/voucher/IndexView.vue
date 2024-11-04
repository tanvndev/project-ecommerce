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
                :to="{ name: 'voucher.update', params: { id: record.id } }"
                class="text-blue-500"
                >{{ record.name }}
              </RouterLink>
              <p class="mb-0 mt-1">
                {{ record.text_description }}
              </p>
            </template>

            <template v-if="column.dataIndex === 'publish'">
              <StatusSwitchComponent
                :record="record"
                :modelName="state.modelName"
                :field="column.dataIndex"
              />
            </template>

            <template v-if="column.dataIndex === 'status'">
              <a-tag :color="record?.status?.color">{{ record?.status?.text }}</a-tag>
            </template>

            <template v-if="column.dataIndex === 'voucher_time'">
              <div class="flex flex-col gap-2">
                <span
                  >Từ:
                  <span class="font-medium">
                    {{ record.voucher_time[0] }}
                  </span>
                </span>
                <span
                  >Đến:
                  <span class="font-medium">
                    {{ record.voucher_time[1] }}
                  </span>
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
import { useCRUD, usePagination } from '@/composables';
import { debounce, resizeImage } from '@/utils/helpers';
import { onMounted, reactive, watch } from 'vue';
import { useRoute } from 'vue-router';

// STATE
const state = reactive({
  pageTitle: 'Danh sách mã giảm giá',
  modelName: 'Voucher',
  routeCreate: 'voucher.store',
  routeUpdate: 'voucher.update',
  endpoint: 'vouchers',
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
    title: 'Ảnh',
    dataIndex: 'image',
    key: 'image',
    width: '7%'
  },
  {
    title: 'Khuyến mại',
    dataIndex: 'name',
    key: 'name',
    width: '30%'
  },
  {
    title: 'Trạng thái',
    dataIndex: 'status',
    key: 'status'
  },
  {
    title: 'Mã',
    dataIndex: 'code',
    key: 'code',
    sorter: (a, b) => a.code.localeCompare(b.code)
  },
  {
    title: 'Số lượng',
    dataIndex: 'quantity',
    key: 'quantity',
    sorter: (a, b) => a.quantity.localeCompare(b.quantity)
  },

  {
    title: 'Thời gian',
    dataIndex: 'voucher_time',
    key: 'voucher_time'
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
      newValue === 'true' ? 'Danh sách lưu trữ mã giảm giá' : 'Danh sách mã giảm giá';
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
