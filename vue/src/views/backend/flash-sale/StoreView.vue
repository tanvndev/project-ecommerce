<template>
  <MasterLayout>
    <template #template>
      <div class="container mx-auto mb-[50px] mt-6 min-h-screen">
        <form @submit.prevent="submitForm">
          <div class="grid grid-cols-12 gap-6">
            <!-- Left Column (Name and Products Fields) -->
            <div class="col-span-9 space-y-3">
              <!-- Name Field -->
              <a-card title="Thông tin chung">
                <InputComponent
                  label="Tên Flash Sale"
                  :required="true"
                  name="name"
                  placeholder="Tên Flash Sale"
                />
              </a-card>

              <a-card title="Sản phẩm">
                <!-- Products Field -->
                <div class="relative">
                  <input
                    ref="productInput"
                    type="text"
                    placeholder="Tìm kiếm sản phẩm"
                    v-model="searchTerm"
                    @focus="isDropdownVisible = true"
                    @blur="isDropdownVisible = false"
                    class="mt-1 block w-full rounded-[4px] border border-gray-300 px-3 py-[9px] shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm"
                  />
                  <span v-if="form.errors.products" class="text-sm text-red-500">{{
                    form.errors.products
                  }}</span>

                  <ul
                    class="show-products absolute left-0 right-0 z-10 mt-3 overflow-auto rounded-md border border-gray-300 bg-white shadow-lg"
                    v-if="isDropdownVisible && filteredProducts.length"
                  >
                    <li
                      v-for="product in filteredProducts"
                      :key="product.id"
                      class="cursor-pointer border-b border-gray-200 px-4 py-3 hover:bg-gray-100"
                      @mousedown.prevent="selectProduct(product)"
                    >
                      <img :src="product.image" alt="" class="mr-2 inline-block h-8 w-auto" />
                      {{ product.name }}
                    </li>
                  </ul>
                </div>

                <!-- Display Selected Products -->
                <div v-if="selectedProducts.length" class="mt-4 flex flex-col space-y-2">
                  <div
                    v-for="(product, index) in selectedProducts"
                    :key="product.id"
                    class="flex items-center space-x-2 rounded-lg bg-slate-50 px-4 py-3 transition-all duration-100 hover:bg-slate-100"
                  >
                    <img :src="product.image" alt="" class="mr-2 h-[50px] w-[50px]" />
                    <span class="flex-1 text-blue-700">{{ product.name }}</span>

                    <!-- Giá sản phẩm -->
                    <input
                      type="number"
                      placeholder="Giá"
                      v-model="product.price"
                      @blur="handleBlur(`products.${index}.price`)"
                      @input="validateProductField('price', index)"
                      class="w-30 ml-2 rounded-[4px] border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm"
                    />
                    <span
                      v-if="form.errors.products && form.errors.products[index]?.price"
                      class="text-sm text-red-500"
                    >
                      {{ form.errors.products[index].price }}
                    </span>

                    <!-- Số lượng sản phẩm -->
                    <input
                      type="number"
                      placeholder="Số lượng"
                      v-model="product.quantity"
                      @blur="handleBlur(`products.${index}.quantity`)"
                      @input="validateProductField('quantity', index)"
                      class="w-30 ml-2 rounded-[4px] border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm"
                    />
                    <span
                      v-if="form.errors.products && form.errors.products[index]?.quantity"
                      class="text-sm text-red-500"
                    >
                      {{ form.errors.products[index].quantity }}
                    </span>

                    <a-button
                      type="text"
                      shape="circle"
                      danger
                      @click="removeProduct(index)"
                      class="text-red-500 hover:text-white"
                    >
                      &times;
                    </a-button>
                  </div>
                </div>
              </a-card>
            </div>

            <!-- Right Column (Publish, Status, End Date) -->
            <div class="col-span-3 space-y-3">
              <!-- Publish Section -->
              <a-card title="Xuất bản">
                <div class="flex space-x-2">
                  <!-- Save Button -->
                  <a-button html-type="submit" type="primary" size="large" class="w-[60%]">
                    <i class="fas fa-save mr-2"></i>
                    Lưu lại
                  </a-button>
                  <!-- Save & Exit Button -->
                  <a-button
                    @click="submitForm(true)"
                    size="large"
                    class="w-[80%]"
                    html-type="button"
                  >
                    Lưu & Thoát
                  </a-button>
                </div>
              </a-card>

              <!-- Status Section -->
              <a-card title="Trạng thái">
                <SelectComponent
                  name="publish"
                  :options="PUBLISH"
                  :required="true"
                  :placeholder="`Chọn trạng thái`"
                />
              </a-card>

              <!-- Start Date Section -->
              <a-card title="Ngày bắt đầu">
                <InputDateComponent name="start_date" :required="true" placeholder="Ngày bắt đầu" />
              </a-card>

              <!-- End Date Section -->
              <a-card title="Ngày kết thúc">
                <InputDateComponent
                  name="end_date"
                  :required="true"
                  placeholder="Chọn ngày kết thúc"
                />
              </a-card>
            </div>
          </div>
        </form>
      </div>
    </template>
  </MasterLayout>
</template>

<script setup>
import {
  MasterLayout,
  InputComponent,
  SelectComponent,
  InputDateComponent
} from '@/components/backend';
import { ref, onMounted, computed, reactive, watch } from 'vue';
import { useCRUD } from '@/composables';
import { useForm } from 'vee-validate';
import { validationSchema } from './validationSchema';
import { PUBLISH } from '@/static/constants';
import router from '@/router';
import { message } from 'ant-design-vue';
import { formatMessages } from '@/utils/format';
import { debounce } from '@/utils/helpers';

const products = ref([]);
const isDropdownVisible = ref(false);
const searchTerm = ref('');
const selectedProducts = ref([]);
const productInput = ref(null);

const state = reactive({
  error: {}
});

const form = useForm({
  validationSchema,
  initialValues: {
    name: '',
    products: [],
    publish: 0,
    start_date: '',
    end_date: ''
  }
});

const validateProductField = async (field, index) => {
  await form.validateField(`products.${index}.${field}`);
};

// Define the handleBlur function
const handleBlur = (field) => {
  form.setFieldTouched(field, true);
  form.validateField(field);
};

const { getAll, create, messages } = useCRUD();

// Fetch products from the API
const fetchProducts = async () => {
  try {
    const payload = {
      page: 1,
      pageSize: 20,
      search: searchTerm.value
    };
    const response = await getAll('products/variants', payload);
    products.value = response.data;
  } catch (error) {
    console.error('Không thể lấy sản phẩm:', error);
  }
};

const submitForm = async (exitAfterSave = false) => {
  const isValid = await form.validate();

  if (!isValid) {
    return;
  }

  const maxQuantities = {};
  const salePrices = {};

  selectedProducts.value.forEach((product) => {
    maxQuantities[product.id] = product.quantity;
    salePrices[product.id] = product.price;
  });

  const dataToSend = {
    name: form.values.name,
    start_date: form.values.start_date,
    publish: form.values.publish,
    end_date: form.values.end_date,
    max_quantities: maxQuantities,
    sale_prices: salePrices
  };

  state.error = {};

  try {
    const response = await create('flash-sales', dataToSend);
    if (exitAfterSave) {
      if (!response) {
        return (state.error = formatMessages(messages.value));
      }

      state.error = {};
      message.success(messages.value);
      router.push({ name: 'flash-sale.index' });
    }
  } catch (error) {
    console.error('Lỗi khi lưu:', error);
  }
};

const selectProduct = (product) => {
  const newProduct = JSON.parse(JSON.stringify({ ...product, price: '', quantity: '' }));
  selectedProducts.value.push(newProduct);
  form.setFieldValue('products', [...form.values.products, newProduct]);
  searchTerm.value = '';
  isDropdownVisible.value = false;
  productInput.value.blur();
};

const removeProduct = (index) => {
  selectedProducts.value.splice(index, 1);
};

// Tìm kiếm sản phẩm
const filteredProducts = computed(() => {
  return products.value.filter((product) =>
    product.name.toLowerCase().includes(searchTerm.value.toLowerCase())
  );
});
const debounceFechProducts = debounce(fetchProducts, 500);

watch(searchTerm, () => {
  debounceFechProducts();
});

// Khi component được mount
onMounted(() => {
  debounceFechProducts();
});
</script>

<style scoped>
.show-products {
  max-height: 20rem;
  overflow-y: auto;
}
</style>
