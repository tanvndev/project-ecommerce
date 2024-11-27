<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen">
        <BreadcrumbComponent :titlePage="state.pageTitle" />

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
            <template v-if="column.dataIndex === 'code'">
              <RouterLink
                :to="{ name: 'order.update', params: { code: record.code } }"
                class="text-blue-500"
                >{{ record.code }}</RouterLink
              >
            </template>

            <template v-if="column.dataIndex === 'requested_by'">
              <p>{{ record.requested_by.name }}</p>
              <p>{{ record.requested_by.email }}</p>
            </template>

            <template v-if="column.dataIndex === 'status'">
              <span :style="{ color: record.status.color }">
                {{ record.status.text }}
              </span>
            </template>

            <template v-if="column.dataIndex === 'action'">
              <a-button
                type="primary"
                :disabled="record.status.text !== 'Chờ xét duyệt'"
                @click="record.status.text === 'Chờ xét duyệt' ? showModal(record) : null"
              >
                Thay đổi
              </a-button>
            </template>
          </template>

          <template #expandedRowRender="{ record }">
            <p>
              Chấp nhận bởi: <strong>{{ record.approved_by ?? 'Chưa cập nhật...' }}</strong>
            </p>
            <p>
              Thời gian phản hồi: <strong>{{ record.approved_at ?? 'Chưa cập nhật...' }}</strong>
            </p>
            <p>
              Lý do từ chối: <strong>{{ record.rejection_reason ?? 'Chưa cập nhật...' }}</strong>
            </p>
          </template>
        </a-table>

        <a-modal
          :open="isModalVisible"
          title="Thay đổi trạng thái"
          @ok="handleSubmit"
          ok-text="Phê duyệt"
          cancel-text="Từ chối"
          @cancel="handleCancel"
          :cancel-button-props="{ danger: true }"
        >
          <div class="mb-7">
            <a-textarea v-model:value="rejectionReason" placeholder="Nhập lý do từ chối" />
          </div>
        </a-modal>

        <!-- End table -->
      </div>
    </template>
  </MasterLayout>
</template>

<script setup>
import {
  BreadcrumbComponent,
  MasterLayout,
  ToolboxComponent,
  InputComponent
} from '@/components/backend';
import { useCRUD, usePagination } from '@/composables';
import { debounce } from '@/utils/helpers';
import { onMounted, reactive, watch, ref } from 'vue';
import { useRoute } from 'vue-router';
import { columns, innerColumns } from './columns';
import { message } from 'ant-design-vue';

const isModalVisible = ref(false);
const rejectionReason = ref('');
const currentRecordId = ref(null);

const showModal = (record) => {
  currentRecordId.value = record.id;
  isModalVisible.value = true;
};



const handleCancel = async () => {
  if (!rejectionReason.value.trim()) {
    message.error('Phải có lý do từ chối!');
    return;
  }

  const response = await create('/status-change-requests/reject', { id: currentRecordId.value, rejection_reason: rejectionReason.value });

  if (!response) {
    return message.error(messages.value);
  }

  fetchData();
  isModalVisible.value = false;
  rejectionReason.value = '';
};

const handleSubmit = async () => {
  const payload = { id: currentRecordId.value, rejection_reason: rejectionReason.value };
  const response = await create('/status-change-requests/approve', payload);

  if (!response) {
    return message.error(messages.value);
  }

  fetchData();
  isModalVisible.value = false;
};

// STATE
const state = reactive({
  pageTitle: 'Danh sách yêu cầu',
  modelName: 'OrderStatusChangeRequest',
  endpoint: 'orders',
  isShowToolbox: false,
  modelIds: [],
  filterOptions: {},
  dataSource: []
});

// CRUD Operations
const { getAll, loading, create, messages } = useCRUD();
const route = useRoute();

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
  const response = await getAll(state.endpoint + '-status', payload);
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

onMounted(fetchData);
</script>
