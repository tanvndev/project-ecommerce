<script setup>
import BaseService from '@/services/BaseService';
import { PUBLISH as publishFilter } from '@/static/constants';
import { ORDER_STATUS_SELECT, PAYMENT_STATUS_SELECT } from '@/static/order';
import { debounce } from '@/utils/helpers';
import { message } from 'ant-design-vue';
import { reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const emits = defineEmits(['onChangeToolbox', 'onFilter', 'onSearch']);
const route = useRoute();
const router = useRouter();
const hasArchive = ref(false);
const props = defineProps({
  isShowToolbox: {
    type: Boolean
  },
  routeCreate: {
    type: String,
    required: true
  },
  modelName: {
    type: String,
    required: true
  },
  modelIds: {
    type: [Object, Array]
  }
});

const handleChangeArchive = () => {
  const currentQuery = { ...route.query };

  if (currentQuery.archive === 'true') {
    hasArchive.value = false;
    delete currentQuery.archive;
  } else {
    hasArchive.value = true;
    currentQuery.archive = 'true';
  }

  router.push({ path: route.path, query: currentQuery });
};

const handleArchive = async () => {
  const payload = {
    modelName: props.modelName,
    modelIds: props.modelIds
  };
  const response = await BaseService.archiveMultiple(payload);
  const type = response.success ? 'success' : 'error';

  emits('onChangeToolbox');
  message[type](response.messages);
};

const handleChangePublish = async (value) => {
  const payload = {
    modelName: props.modelName,
    modelIds: props.modelIds,
    field: 'publish',
    value
  };

  const response = await BaseService.changeStatusAll(payload);
  const type = response.success ? 'success' : 'error';

  emits('onChangeToolbox');
  message[type](response.messages);
};

const handleDeleteMultiple = async (force = 0) => {
  const payload = {
    modelName: props.modelName,
    modelIds: props.modelIds,
    forceDelete: force
  };
  const response = await BaseService.deleteMultiple(payload);
  const type = response.success ? 'success' : 'error';

  emits('onChangeToolbox');
  message[type](response.messages);
};

const filterOptions = reactive({
  publish: 0,
  search: '',
  order_status: ORDER_STATUS_SELECT[0].value,
  payment_status: PAYMENT_STATUS_SELECT[0].value
});

const hideArchive = () => {
  const routeHide = ['attribute.index', 'permission.index', 'order.index', 'evaluate.index', 'order.change-status-request', 'flash-sale.index'];
  if (routeHide.includes(route.name)) {
    return false;
  }
  return true;
};

const removeDelete = () => {
  const routeHide = [
    'permission.index',
    'attribute.index',
    'attribute.update',
    'order.index',
    'evaluate.index'
  ];
  if (routeHide.includes(route.name)) {
    return false;
  }
  return true;
};
const removePublish = () => {
  const routeHide = ['permission.index', 'attribute.index', 'attribute.update', 'order.index', 'order.change-status-request', 'flash-sale.index'];
  if (routeHide.includes(route.name)) {
    return false;
  }
  return true;
};

const removeOrderFilter = () => {
  const routeHide = ['order.index'];
  if (routeHide.includes(route.name)) {
    return true;
  }
  return false;
};
const onSearch = (searchValue) => {
  filterOptions.search = searchValue.target.value;
  emits('onFilter', filterOptions);
};

const handleDebounceSearch = debounce(onSearch, 500);

const handleFilterChange = debounce(() => {
  emits('onFilter', filterOptions);
}, 300);

const handleReloadData = () => {
  filterOptions.publish = 0;
  filterOptions.search = '';
  filterOptions.order_status = ORDER_STATUS_SELECT[0].value;
  filterOptions.payment_status = PAYMENT_STATUS_SELECT[0].value;

  handleFilterChange();
};
</script>

<template>
  <a-card>
    <div class="flex justify-between">
      <a-space :size="12" class="flex justify-end">
        <a-select
          v-if="removePublish()"
          size="large"
          @change="handleFilterChange"
          v-model:value="filterOptions.publish"
          class="w-[230px]"
          :options="publishFilter"
        >
        </a-select>
        <a-select
          v-if="removeOrderFilter()"
          size="large"
          @change="handleFilterChange"
          v-model:value="filterOptions.order_status"
          class="w-[230px]"
          :options="ORDER_STATUS_SELECT"
        >
        </a-select>
        <a-select
          v-if="removeOrderFilter()"
          size="large"
          @change="handleFilterChange"
          v-model:value="filterOptions.payment_status"
          class="w-[230px]"
          :options="PAYMENT_STATUS_SELECT"
        >
        </a-select>

        <a-input
          class="w-[400px]"
          placeholder="Nhập để tìm kiếm..."
          size="large"
          :allowClear="true"
          @change="handleDebounceSearch"
        >
          <template #suffix>
            <i class="fas fa-search pr-1 text-gray-500"></i>
          </template>
        </a-input>
      </a-space>
      <a-space :size="7" class="flex">
        <div v-if="props.isShowToolbox">
          <a-dropdown trigger="click" class="mr-2" v-if="removePublish()">
            <a-button size="large">
              <i class="far fa-tools mr-2 text-[14px]"></i>
              <span>Công cụ</span>
            </a-button>
            <template #overlay>
              <a-menu>
                <a-menu-item>
                  <a-popconfirm
                    title="Bạn có chắc chắn muốn xuất bản toàn bộ không?"
                    ok-text="Xác nhận"
                    cancel-text="Hủy"
                    @confirm="handleChangePublish(1)"
                  >
                    <a-button type="link" class="block w-full text-left text-black">
                      <span>Xuất bản toàn bộ</span>
                    </a-button>
                  </a-popconfirm>
                </a-menu-item>
                <a-menu-item>
                  <a-popconfirm
                    title="Bạn có chắc chắn muốn hủy xuất bản toàn bộ không?"
                    ok-text="Xác nhận"
                    cancel-text="Hủy"
                    @confirm="handleChangePublish(2)"
                  >
                    <a-button type="link" class="block w-full text-left text-black">
                      <span>Hủy xuất bản toàn bộ</span>
                    </a-button>
                  </a-popconfirm>
                </a-menu-item>
              </a-menu>
            </template>
          </a-dropdown>

          <a-dropdown trigger="click" class="" v-if="removeDelete()">
            <a-button size="large">
              <i class="fas fa-trash-alt mr-2 text-[14px]"></i>
              <span>Thùng rác</span>
            </a-button>
            <template #overlay>
              <a-menu>
                <a-menu-item v-if="hideArchive()">
                  <!-- Khôi phục Lưu trữ -->
                  <a-popconfirm
                    v-if="hasArchive"
                    title="Bạn có chắc chắn muốn hủy hủy lưu trữ toàn bộ không?"
                    ok-text="Xác nhận"
                    cancel-text="Hủy"
                    @confirm="handleArchive()"
                  >
                    <a-button type="link" class="block w-full text-left text-black">
                      <span>Hủy lưu trữ toàn bộ</span>
                    </a-button>
                  </a-popconfirm>

                  <!-- Luu trữ -->
                  <a-popconfirm
                    v-else
                    title="Bạn có chắc chắn muốn lưu trữ toàn bộ không?"
                    ok-text="Xác nhận"
                    cancel-text="Hủy"
                    @confirm="handleDeleteMultiple(0)"
                  >
                    <a-button type="link" class="block w-full text-left text-black">
                      <span>Lưu trữ toàn bộ</span>
                    </a-button>
                  </a-popconfirm>
                </a-menu-item>
                <a-menu-item>
                  <a-popconfirm
                    title="Bạn có chắc chắn muốn xuất bản toàn bộ không?"
                    ok-text="Xác nhận"
                    cancel-text="Hủy"
                    @confirm="handleDeleteMultiple(1)"
                  >
                    <a-button type="link" class="block w-full text-left text-black">
                      <span>Xóa toàn bộ</span>
                    </a-button>
                  </a-popconfirm>
                </a-menu-item>
              </a-menu>
            </template>
          </a-dropdown>
        </div>

        <a-button size="large" @click="handleReloadData">
          <i class="far fa-redo mr-2 text-[14px]"></i>
          <span>Tải lại trang</span>
        </a-button>
        <a
          v-if="hideArchive()"
          href="#"
          @click.prevent="handleChangeArchive"
          class="rounded-[4px] border border-orange-500 px-[19px] py-[9px] text-[16px] text-orange-500 hover:bg-orange-500 hover:text-white"
          :class="{ 'bg-orange-500 text-white': hasArchive }"
        >
          <i class="fas fa-archive mr-2 text-[14px]"></i>
          <span>Lưu trữ</span>
        </a>
      </a-space>
    </div>
  </a-card>
</template>
