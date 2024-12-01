<template>
  <a-col :span="7">
    <a-card class="mt-3" title="Ảnh sản phẩm">
      <InputFinderComponent name="image" />
    </a-card>

    <a-card class="mt-3" title="Thương hiệu">
      <SelectComponent
        name="brand_id"
        :options="state.brands"
        placeholder="Chọn thương hiệu sản phẩm"
      />
    </a-card>

    <a-card class="mt-3" title="Nhóm sản phẩm">
      <TreeSelectComponent
        name="product_catalogue_id"
        :treeDefaultExpandAll="false"
        :options="state.productCatalogues"
        placeholder="Chọn danh mục sản phẩm"
      />
    </a-card>
  </a-col>
</template>
<script setup>
import { InputFinderComponent, SelectComponent, TreeSelectComponent } from '@/components/backend';
import { useCRUD } from '@/composables';
import { formatDataToSelect, formatDataToTreeSelect } from '@/utils/format';
import { onMounted, reactive } from 'vue';

const { getAll, data } = useCRUD();

// STATE
const state = reactive({
  productCatalogues: [],
  brands: []
});

// LAY RA TOAN BO PRODUCT CATALOGUE
const getProductCatalogues = async () => {
  await getAll('products/catalogues');
  state.productCatalogues = formatDataToTreeSelect(data.value);
};

// LAY RA TOAN BO BRAND
const getBrands = async () => {
  await getAll('brands');
  state.brands = formatDataToSelect(data.value);
};

// LAY RA TOAN BO SUPPLIER

onMounted(() => {
  getBrands();
  getProductCatalogues();
});
</script>
