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

        <form @submit.prevent="onSubmit">
          <a-card class="mx-auto my-5 max-w-md shadow-md">
            <a-row :gutter="[16, 16]">
              <a-col :span="19">
                <InputComponent :required="true" name="keyword" placeholder="Từ ngữ" />
              </a-col>
              <a-col :span="5">
                <a-button html-type="submit" type="primary" size="large">Submit</a-button>
              </a-col>
            </a-row>
          </a-card>
        </form>

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
            <template v-if="column.dataIndex === 'keyword'">
              <div class="cursor-pointer text-blue-500" @click="editRecord(record.key)">
                {{ record.keyword }}
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
  ToolboxComponent,
  InputComponent
} from '@/components/backend';
import { onMounted, reactive, watch } from 'vue';
import { columns } from './columns';
import { useForm } from 'vee-validate';
import * as yup from 'yup';
import { formatMessages } from '@/utils/format';
import { message } from 'ant-design-vue';

import { useCRUD, usePagination } from '@/composables';

const state = reactive({
  pageTitle: 'Danh sách từ ngữ khiếm nhã',
  modelName: 'ProhibitedWord',
  endpoint: 'prohibited-words',
  isShowToolbox: false,
  modelIds: [],
  filterOptions: {},
  dataSource: [],
  selectedRecordId: null
});

// CRUD Operations
const { getAll, loading, create, update, messages } = useCRUD();

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

// Hàm để lấy ID của record khi bấm vào keyword
const editRecord = (id) => {
  const record = state.dataSource.find((item) => item.key === id);

  if (record) {
    state.selectedRecordId = id;
    setFieldValue('keyword', record.keyword); // Đặt giá trị keyword trong form
  }
};

// Lifecycle Hook
onMounted(fetchData);

const schema = yup.object({
  keyword: yup.string().required('Vui lòng nhập từ ngữ').min(3, 'Từ ngữ phải có ít nhất 3 ký tự')
});

const { handleSubmit, errors, setValues, setFieldValue } = useForm({
  validationSchema: schema
});

const onSubmit = handleSubmit(async (values) => {
  const response = state.selectedRecordId
    ? await update(state.endpoint, state.selectedRecordId, values)
    : await create(state.endpoint, values);


  if (!response) {
    return (state.errors = formatMessages(messages.value));
  }

  message.success(state.selectedRecordId ? 'Cập nhật thành công' : 'Thêm mới thành công');
  state.errors = {};

  state.selectedRecordId = null;
  setFieldValue('keyword', '');
  await fetchData();
});
</script>
