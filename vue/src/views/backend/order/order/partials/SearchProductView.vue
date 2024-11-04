<template>
  <div class="flex items-center justify-between">
    <h1 class="mb-2 text-[16px] capitalize">Danh sách sản phẩm (Tối đa 50)</h1>
    <h2
      class="text-[14px] capitalize text-green-500"
      :class="{
        'text-red-500': state.productVariants?.length == 0 || state.productVariants?.length > 50
      }"
    >
      Sản phẩm: {{ state.productVariants?.length }}
    </h2>
  </div>
  <div class="mt-1">
    <a-input
      @click="state.open = true"
      placeholder="Tìm kiếm theo tên sản phẩm"
      size="large"
      readonly
    >
      <template #prefix>
        <i class="far fa-search pr-1 text-gray-500"></i>
      </template>
    </a-input>
    <a-row :gutter="[16, 10]" class="mt-5">
      <a-col
        span="24"
        class="rounded-lg py-2 hover:bg-slate-50"
        v-for="(item, index) in state.productVariants"
        :key="item.id"
      >
        <div class="flex items-center justify-between px-3">
          <div class="flex items-center">
            <span class="mr-2 font-bold">#{{ index + 1 }}</span>
            <div class="flex items-center">
              <div class="rounded border p-1">
                <img class="h-[50px] w-[50px] object-cover" :src="resizeImage(item.image, 100)" />
              </div>
              <div class="ml-3">
                <div class="w-[300px] truncate">{{ item.name }}</div>
                <small class="block text-primary-500">{{ item.attribute_values }}</small>
              </div>
            </div>
            <div class="flex flex-col pl-32">
              <span>{{ formatCurrency(item.sale_price || item.price) }}</span>
              <span class="text-[13px] text-gray-500" v-if="item.sale_price">
                <del>{{ formatCurrency(item.sale_price ?? 20000000) }}</del></span
              >
            </div>

            <div class="flex w-[250px] flex-col pl-20">
              <a-input-number v-model:value="state.quantities[item.id]" min="1">
                <template #addonBefore> Số lượng </template>
              </a-input-number>
            </div>
          </div>
          <a-button
            @click="handleDeleteRow(item.id)"
            type="primary"
            danger
            size="small"
            class="ml-2"
          >
            <i class="fas fa-trash-alt text-[12px]"></i>
          </a-button>
        </div>
      </a-col>
    </a-row>

    <!-- Modal -->
    <a-modal v-model:open="state.open" width="1500px" title="Nhập tìm kiếm sản phẩm" class="top-3">
      <div class="mt-5">
        <a-input
          v-model:value="state.search"
          @change="handleSearch"
          placeholder="Tìm kiếm theo tên sản phẩm"
          size="large"
        >
          <template #prefix>
            <i class="far fa-search pr-1 text-gray-500"></i>
          </template>
        </a-input>
      </div>
      <div class="mt-5">
        <a-row :gutter="[16, 16]">
          <a-col span="8">
            <SelectComponent
              name="brand_id"
              :options="state.brands"
              @onChange="handleGetBrandIds"
              placeholder="Lựa chọn thương hiệu sản phẩm"
            />
          </a-col>

          <a-col span="16">
            <TreeSelectComponent
              name="catalogue_ids"
              :options="state.productCatalogues"
              @onChange="handleGetCatalogueIds"
              placeholder="Lựa chọn nhóm sản phẩm"
            />
          </a-col>
        </a-row>
      </div>
      <div class="my-5 border-t pt-5" v-if="state.dataSource.length > 0">
        <a-table
          bordered
          :columns="columns"
          :data-source="state.dataSource"
          :row-selection="rowSelection"
          :pagination="pagination"
          :scroll="{ y: '57vh' }"
          :loading="loading"
          @change="handleTableChange"
        >
          <template #bodyCell="{ column, record }">
            <template v-if="column.dataIndex === 'name'">
              <div class="flex items-center">
                <div class="rounded border p-1">
                  <img
                    class="h-[50px] w-[50px] object-cover"
                    :src="resizeImage(record.image, 100)"
                    :alt="record.name"
                  />
                </div>
                <div class="ml-3">
                  <span>{{ record.name }}</span>
                  <small class="block text-primary-500">{{ record.attribute_values }}</small>
                </div>
              </div>
            </template>
            <template v-if="column.dataIndex === 'price'">
              {{ formatCurrency(record.price) }}
            </template>
            <template v-if="column.dataIndex === 'sale_price'">
              {{ formatCurrency(record.sale_price) }}
            </template>
          </template>
        </a-table>
      </div>
      <div v-else class="my-10">
        <a-empty
          image="https://gw.alipayobjects.com/mdn/miniapp_social/afts/img/A*pevERLJC9v0AAAAAAAAAAABjAQAAAQ/original"
          :image-style="{
            height: '60px',
            display: 'inline-block',
            'text-align': 'center'
          }"
        >
          <template #description>
            <span> Vui lòng nhập để tìm kiếm. </span>
          </template>
        </a-empty>
      </div>

      <template #footer>
        <a-button @click="state.open = false">Hủy bỏ</a-button>
        <a-button html-type="submit" @click="handleOk" type="primary">Lưu lại</a-button>
      </template>
    </a-modal>
  </div>
</template>
<script setup>
import { useCRUD, usePagination } from '@/composables';
import { formatCurrency, formatDataToSelect, formatDataToTreeSelect } from '@/utils/format';
import { debounce, resizeImage } from '@/utils/helpers';
import { useField } from 'vee-validate';
import { reactive, watch, onMounted } from 'vue';
import { SelectComponent, TreeSelectComponent } from '@/components/backend';

// STATE
const state = reactive({
  endpoint: 'products/variants',
  isShowToolbox: false,
  filterOptions: {},
  dataSource: [],
  open: false,
  search: '',
  productVariantIds: [],
  productVariants: [],
  productCatalogues: [],
  brands: [],
  quantities: {}
});

const columns = [
  {
    title: 'Sản phẩm',
    dataIndex: 'name',
    key: 'name'
  },
  {
    title: 'Giá bán',
    dataIndex: 'price',
    key: 'price',
    width: '15%'
  },
  {
    title: 'Giá khuyến mãi',
    dataIndex: 'sale_price',
    key: 'sale_price',
    width: '15%'
  }
];

const { getAll, loading, data } = useCRUD();

// Pagination
const {
  pagination,
  rowSelection,
  onChangePagination,
  selectedRowKeys,
  selectedRows,
  handleTableChange
} = usePagination();

const props = defineProps({
  oldValue: {
    type: Array,
    default: () => []
  }
});

const emits = defineEmits(['onSave']);

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

const debounceHandleSearch = debounce(() => {
  state.filterOptions.search = state.search;
  fetchData();
}, 400);

const handleSearch = () => {
  debounceHandleSearch();
};

const handleOk = () => {
  const newSelectedIds = Array.isArray(selectedRowKeys.value) ? selectedRowKeys.value : [];
  state.productVariantIds = [...new Set([...state.productVariantIds, ...newSelectedIds])];

  const newVariants = selectedRows.value.filter(
    (item) =>
      newSelectedIds.includes(item.id) && !state.productVariants.some((v) => v.id === item.id)
  );

  state.productVariants = [...state.productVariants, ...newVariants];

  handleChangeQuantity();
  state.open = false;
};

const handleChangeQuantity = () => {
  const quantities =
    state.productVariants?.map((item) => {
      return {
        product_variant_id: item.id,
        quantity: state.quantities[item.id] || 1,
        price: item.sale_price || item.price
      };
    }) ?? [];

  emits('onSave', quantities);
};

watch(
  state.quantities,
  () => {
    handleChangeQuantity();
  },
  { deep: true }
);

const handleDeleteRow = (id) => {
  state.productVariants = state.productVariants.filter((variant) => variant.id != id);
  state.productVariantIds = state.productVariantIds.filter((variantId) => variantId != id);
  handleChangeQuantity();
};

// Add this function to fetch product variants by IDs
const fetchProductVariants = async (ids) => {
  if (ids && ids.length > 0) {
    const response = await getAll(state.endpoint, { ids: ids.join(',') });
    state.productVariants = response;
    state.productVariantIds = ids;
  }
};

const debouncedFetchData = debounce(fetchData, 800);

const handleGetBrandIds = (brandId) => {
  state.filterOptions.brand_id = brandId;
  debouncedFetchData();
};

const handleGetCatalogueIds = (catalogueIds) => {
  const catalogueIdsJson = JSON.stringify(catalogueIds ?? []);
  state.filterOptions.catalogues = catalogueIdsJson;
  debouncedFetchData();
};

const getProductCatalogues = async () => {
  await getAll('products/catalogues');
  state.productCatalogues = formatDataToTreeSelect(data.value);
};

const getBrands = async () => {
  await getAll('brands');
  state.brands = formatDataToSelect(data.value);
};

// Modify the watch function
watch(
  () => props.oldValue,
  (newOldValue) => {
    if (newOldValue && newOldValue.length > 0) {
      fetchProductVariants(newOldValue);
    }
  },
  { immediate: true }
);

// Add onMounted hook
onMounted(() => {
  getProductCatalogues();
  getBrands();
  if (props.oldValue && props.oldValue.length > 0) {
    fetchProductVariants(props.oldValue);
  }
});
</script>
