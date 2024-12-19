<script setup>
import {
  AleartError,
  BreadcrumbComponent,
  InputComponent,
  InputDateComponent,
  InputFinderComponent,
  InputNumberComponent,
  MasterLayout,
  RadioComponent
} from '@/components/backend';
import { useCRUD } from '@/composables';
import router from '@/router';
import { DISCOUNT_CONDITION_APPLY, DISCOUNT_TYPE } from '@/static/constants';
import { formatMessages } from '@/utils/format';
import { handleBeforeUnload, handleDateChangeToAnt } from '@/utils/helpers';
import { message } from 'ant-design-vue';
import { useForm } from 'vee-validate';
import { computed, onMounted, onUnmounted, reactive } from 'vue';
import * as yup from 'yup';
// VARIABLES

const { getOne, create, update, messages, data } = useCRUD();
const id = computed(() => router.currentRoute.value.params.id || null);

// STATE
const state = reactive({
  endpoint: 'vouchers',
  pageTitle: 'Thêm mới mã giảm giá',
  errors: {},
  valueType: 'fixed',
  conditionApply: 'all',
  usage_limit: 'unlimited'
});

const { handleSubmit, setValues } = useForm({
  validationSchema: yup.object({
    name: yup.string().required('Tên mã giảm giá không được để trống.'),
    voucher_time: yup.array().required('Thời gian không được để trống.'),
    value: yup.string().required('Vui lòng nhập giá trị mã giảm giá.'),
    quantity: yup.string().required('Vui lòng nhập số lượng mã giảm giá.'),
    code: yup.string().required('Vui có nhập mã code.')
  })
});

const handleChangeValueType = (value) => {
  state.valueType = value?.target?.value;
};
const handleChangeConditionApply = (value) => {
  state.conditionApply = value?.target?.value;
};

const handleChangeQuantity = (value) => {
  state.usage_limit = value?.target?.value;
};

const onSubmit = handleSubmit(async (values) => {
  const response =
    id.value && id.value > 0
      ? await update(state.endpoint, id.value, values)
      : await create(state.endpoint, values);
  if (!response) {
    return (state.errors = formatMessages(messages.value));
  }

  message.success(messages.value);
  state.errors = {};
  router.push({ name: 'voucher.index' });
});

const fetchOne = async () => {
  await getOne(state.endpoint, id.value);
  const {
    name,
    description,
    code,
    quantity,
    image,
    value,
    value_type,
    voucher_time,
    condition_apply,
    min_quantity,
    subtotal_price,
    value_limit_amount,
    usage_limit
  } = data.value;

  setValues({
    name,
    description,
    code,
    image,
    quantity,
    value_type,
    value,
    condition_apply,
    min_quantity,
    subtotal_price,
    value_limit_amount,
    voucher_time: handleDateChangeToAnt(voucher_time),
    quantity_condition: usage_limit ? 'limited' : 'unlimited',
    usage_limit: usage_limit
  });

  state.valueType = value_type;
  state.conditionApply = condition_apply;
  state.usage_limit = usage_limit ? 'limited' : 'unlimited';
};

onMounted(() => {
  window.addEventListener('beforeunload', handleBeforeUnload);
  if (id.value && id.value > 0) {
    state.pageTitle = 'Cập nhập mã giảm giá.';
    fetchOne();
  }
});

onUnmounted(() => {
  window.removeEventListener('beforeunload', handleBeforeUnload);
});
</script>

<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-[50px] min-h-screen">
        <BreadcrumbComponent :titlePage="state.pageTitle" @on-save="onSubmit" />
        <form @submit.prevent="onSubmit">
          <a-row :gutter="16">
            <a-col :span="16">
              <a-card class="mt-3" title="Thông tin chung">
                <AleartError :errors="state.errors" />
                <a-row :gutter="[16, 16]">
                  <a-col :span="12">
                    <InputComponent
                      name="name"
                      label="Tên mã giảm giá"
                      :required="true"
                      placeholder="Tên mã giảm giá"
                    />
                  </a-col>
                  <a-col :span="12">
                    <InputComponent
                      name="code"
                      label="Mã"
                      :required="true"
                      placeholder="Mã"
                      :showGenerate="true"
                    />
                  </a-col>
                  <a-col :span="24">
                    <InputComponent
                      typeInput="textarea"
                      name="description"
                      label="Mô tả mã giảm giá"
                      placeholder="Mô tả cho mã giảm giá"
                    />
                  </a-col>
                </a-row>
              </a-card>

              <!-- Quantity -->
              <a-card class="mt-3" title="Số lượng ">
                <a-row :gutter="[16, 16]">
                  <a-col :span="24">
                    <RadioComponent
                      name="quantity_condition"
                      :options="[
                        {
                          value: 'unlimited',
                          label: 'Không giới hạn'
                        },
                        {
                          value: 'limited',
                          label: 'Giới hạn'
                        }
                      ]"
                      option-type="button"
                      old-value="unlimited"
                      button-style="solid"
                      @on-change="handleChangeQuantity"
                    />
                  </a-col>
                  <a-col :span="12">
                    <InputNumberComponent name="quantity" label="Số lượng" placeholder="Số lượng" />
                  </a-col>
                  <a-col :span="12" v-if="state.usage_limit === 'limited'">
                    <InputNumberComponent
                      name="usage_limit"
                      label="Giới hạn sử dụng"
                      placeholder="Giới hạn sử dụng"
                    />
                  </a-col>
                </a-row>
              </a-card>
              <!-- Value -->

              <a-card class="mt-3" title="Giá trị ">
                <a-row :gutter="[16, 16]">
                  <a-col :span="24">
                    <RadioComponent
                      name="value_type"
                      :options="DISCOUNT_TYPE"
                      option-type="button"
                      old-value="fixed"
                      button-style="solid"
                      @on-change="handleChangeValueType"
                    />
                  </a-col>
                  <a-col :span="12" v-if="state.valueType === 'fixed'">
                    <InputNumberComponent
                      name="value"
                      label="Giá trị mã giảm giá (₫)"
                      placeholder="Giá trị mã giảm giá (₫)"
                    />
                  </a-col>
                </a-row>
                <a-row :gutter="[16, 16]" class="mt-4" v-if="state.valueType === 'percentage'">
                  <a-col :span="10">
                    <InputNumberComponent
                      name="value"
                      label="Giá trị mã giảm giá (%)"
                      type-input="percent"
                      placeholder="Giá trị mã giảm giá (%)"
                    />
                  </a-col>
                  <a-col :span="14">
                    <InputNumberComponent
                      name="value_limit_amount"
                      label="Giá trị giảm tối đa (₫)"
                      placeholder="Giá trị giảm tối đa (₫)"
                    />
                  </a-col>
                </a-row>
              </a-card>

              <!-- Condition -->
              <a-card class="mt-3" title="Điều kiện áp dụng ">
                <a-row :gutter="[16, 16]">
                  <a-col :span="24">
                    <RadioComponent
                      name="condition_apply"
                      :options="DISCOUNT_CONDITION_APPLY"
                      option-type="button"
                      old-value="all"
                      button-style="solid"
                      @on-change="handleChangeConditionApply"
                    />
                  </a-col>
                  <a-col :span="24" v-if="state.conditionApply === 'subtotal_price'">
                    <InputNumberComponent
                      name="subtotal_price"
                      label="Tổng giá trị đơn hàng tối thiểu (₫)"
                      placeholder="Tổng giá trị đơn hàng tối thiểu (₫)"
                    />
                  </a-col>
                  <a-col :span="24" v-if="state.conditionApply === 'min_quantity'">
                    <InputNumberComponent
                      name="min_quantity"
                      label="Tổng số lượng sản phẩm được khuyến mại tối thiểu"
                      placeholder="Tổng số lượng sản phẩm được khuyến mại tối thiểu"
                    />
                  </a-col>
                </a-row>
              </a-card>

              <!-- Voucher Time -->
              <a-card class="mt-3" title="Thời gian ">
                <a-row :gutter="[16, 16]">
                  <a-col span="24">
                    <InputDateComponent type="date-range" name="voucher_time" :showTime="true" />
                  </a-col>
                </a-row>
              </a-card>
            </a-col>

            <a-col :span="8">
              <a-card class="mt-3" title="Ảnh mã giảm giá">
                <InputFinderComponent name="image" />
              </a-card>
            </a-col>
          </a-row>

          <div class="fixed bottom-0 right-[19px] p-10">
            <a-button html-type="submit" type="primary" size="large">
              <i class="fas fa-save mr-2"></i>
              <span>Lưu thông tin</span>
            </a-button>
          </div>
        </form>
      </div>
    </template>
  </MasterLayout>
</template>
