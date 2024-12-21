<template>
  <a-card class="mt-3">
    <template #title>
      <span> Dữ liệu sản phẩm </span>
    </template>
    <a-row :gutter="[16, 16]">
      <a-col :span="12">
        <SelectComponent
          name="product_type"
          label="Loại sản phẩm"
          :required="true"
          :options="PRODUCT_TYPE"
          :showSearch="false"
          tooltip-text="Sản phẩm đơn giản là sản phẩm có không có phiên bản. Sản phẩm biến thể có nhiều phiên bản khác nhau."
          placeholder="Chọn loại sản phẩm"
          @onChange="handleType"
        />
      </a-col>
      <a-col :span="12">
        <InputComponent name="sku" label="SKU" placeholder="Tự sinh nếu không nhập" />
      </a-col>

      <a-col :span="24" v-if="props.update || state.productType">
        <a-tabs v-model:activeKey="state.activeKey" tab-position="top">
          <!-- Chung -->
          <a-tab-pane key="1">
            <template #tab>
              <span>
                <i class="fas fa-wrench mr-1"></i>
                Chung
              </span>
            </template>
            <!-- Chung partials -->
            <CommonView :product-type="state.productType" />
          </a-tab-pane>

          <!-- Thuoc tinh -->
          <a-tab-pane key="5">
            <template #tab>
              <span>
                <i class="fas fa-tasks-alt mr-1"></i>
                Các thuộc tính
              </span>
            </template>
            <AttributeView :product-type="state.productType" />
          </a-tab-pane>
          <!-- Bien the -->
          <a-tab-pane key="6" v-if="state.productType === 'variable'">
            <template #tab>
              <span>
                <i class="far fa-table mr-1"></i>
                Các biến thể
              </span>
            </template>
            <VariantView />
          </a-tab-pane>

          <!-- Giao hang -->
          <a-tab-pane key="3">
            <template #tab>
              <span>
                <i class="far fa-shipping-fast mr-1"></i>
                Giao hàng
              </span>
            </template>
            <!-- Giao hang partials -->
            <ShippingView />
          </a-tab-pane>
          <!-- Upsell -->
          <a-tab-pane key="4">
            <template #tab>
              <span>
                <i class="fas fa-link mr-1"></i>
                Các sản phẩm được kết nối
              </span>
            </template>
            <UpsellView />
          </a-tab-pane>
        </a-tabs>
      </a-col>
    </a-row>
  </a-card>
</template>

<script setup>
import { InputComponent, SelectComponent } from '@/components/backend';
import { PRODUCT_TYPE } from '@/static/constants';
import { reactive } from 'vue';
import { useStore } from 'vuex';
import AttributeView from './AttributeView.vue';
import CommonView from './CommonView.vue';
import ShippingView from './ShippingView.vue';
import UpsellView from './UpsellView.vue';
import VariantView from './VariantView.vue';

const props = defineProps({
  update: {
    type: Boolean,
    default: false
  }
});
// STATE
const state = reactive({
  productType: '',
  activeKey: '1'
});

const store = useStore();

// XU LY KIEU SAN PHAM
const handleType = (value) => {
  state.productType = value;
  store.commit('productStore/setProductType', value);
};
</script>
