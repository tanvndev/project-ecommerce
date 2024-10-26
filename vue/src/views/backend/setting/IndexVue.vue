<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 h-screen min-h-screen">
        <a-row :gutter="[16, 16]" class="mt-4">
          <a-col span="24">
            <a-card>
              <a-tabs v-model:activeKey="activeKey" tab-position="left">
                <a-tab-pane key="store">
                  <template #tab>
                    <i class="far fa-store mr-2"></i>
                    <span>Cấu hình chung</span>
                  </template>
                  <!-- Store -->
                  <StoreView />
                </a-tab-pane>
                <a-tab-pane key="payment">
                  <template #tab>
                    <i class="far fa-money-check mr-2"></i>
                    <span>Phương thức thanh toán</span>
                  </template>
                  <!-- Payment -->
                  <PaymentView />
                </a-tab-pane>
                <a-tab-pane key="shipping">
                  <template #tab>
                    <i class="far fa-truck-couch mr-2"></i>
                    <span>Hình thức vận chuyển</span>
                  </template>
                  <!-- Shipping -->
                  <ShippingView />
                </a-tab-pane>
              </a-tabs>
            </a-card>
          </a-col>
          <a-col span="16"> </a-col>
        </a-row>
      </div>
    </template>
  </MasterLayout>
</template>
<script setup>
import { MasterLayout } from '@/components/backend';
import { ref } from 'vue';
import PaymentView from './partials/PaymentView.vue';
import ShippingView from './partials/ShippingView.vue';
import StoreView from './partials/StoreView.vue';

const activeKey = ref('payment');
</script>
