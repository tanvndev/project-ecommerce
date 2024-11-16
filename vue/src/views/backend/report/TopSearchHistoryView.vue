<template>
    <MasterLayout>
      <template #template>
        <div class="mx-10 mb-5 min-h-screen">
          <div class="flex items-center justify-between py-5">
            <div class="flex items-center">
              <a-button class="mr-3" @click="router.push({ name: 'report.index' })">
                <i class="fas fa-arrow-left text-gray-500"></i>
              </a-button>
              <h2 class="text-[18px] uppercase leading-none">Lương tìm kiếm theo thời gian</h2>
            </div>
            <div>
              <a-button class="mr-3" type="text">
                <i class="fas fa-download mr-2 text-gray-500"></i>
                Xuất báo cáo
              </a-button>
            </div>
          </div>
  
          <div class="flex justify-between">
            <!-- Toolbox Filter -->
            <ToolboxFilter @onChangeDate="handleOnChangeDate" />
  
            <a-select style="width: 150px" size="large" placeholder="Dạng biểu đồ" v-model="chartFor">
              <a-select-option value="day">Theo ngày</a-select-option>
              <a-select-option value="month">Theo tháng</a-select-option>
              <a-select-option value="year">Theo năm</a-select-option>
            </a-select>
          </div>
  
          <a-table
            class="mt-4"
            :dataSource="dataSource"
            :columns="columns"
            :pagination="pagination"
            :loading="isLoading"
            @change="handleTableChange"
          >
          </a-table>
        </div>
      </template>
    </MasterLayout>
  </template>
  <script setup>
  import { MasterLayout, ToolboxFilter } from '@/components/backend';
  import { usePagination } from '@/composables';
  import axios from '@/configs/axios';
  import { debounce } from '@/utils/helpers';
  import _ from 'lodash';
  import { computed, ref, watch } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  
  const router = useRouter();
  const route = useRoute();
  const chartFor = ref('day');
  const date = computed(() => route.query.date || 'last_30_days');
  const start_date = computed(() => route.query.start_date || '');
  const end_date = computed(() => route.query.end_date || '');
  const isLoading = ref(false);
  const columns = [
    {
      title: 'Từ khóa',
      dataIndex: 'keyword',
      key: 'keyword'
    },
    {
      title: 'Lượt tìm kiếm',
      dataIndex: 'total_count',
      key: 'total_count'
    },
  ];
  const { pagination, onChangePagination, handleTableChange } = usePagination();
  
  const dataSource = ref([]);
  
  const fetchData = async () => {
    isLoading.value = true;
    try {
      const { data } = await axios.get('/statistics/search-history', {
        params: {
          date: date.value,
          start_date: start_date.value,
          end_date: end_date.value,
          page: pagination.current,
          pageSize: pagination.pageSize
        }
      });
  
      const newData = _.map(data?.data, (value) => value);
  
      dataSource.value = newData;
      pagination.current = data?.current_page;
      pagination.total = data?.total;
      pagination.pageSize = data?.per_page;
    } catch (error) {
      console.log(error);
    } finally {
      isLoading.value = false;
    }
  };
  
  const debounceGetData = debounce(fetchData, 500);
  
  watch(onChangePagination, () => debounceGetData());
  
  const handleOnChangeDate = async ({ allDay }) => {
    debounceGetData();
  };
  </script>
  