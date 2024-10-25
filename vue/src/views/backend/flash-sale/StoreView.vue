<template>
  <MasterLayout>
    <template #template>
      <div class="bg-gray-50 p-6">
        <form @submit.prevent="submitForm">
          <div class="grid grid-cols-12 gap-6">
            <!-- Left Column (Name and Products Fields) -->
            <div class="col-span-9 space-y-6">
              <!-- Name Field -->
              <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <InputComponent
                  label="Tên Flash Sale"
                  :required="true"
                  name="name"
                  placeholder="Tên Flash Sale"
                />
              </div>

              <!-- Products Field -->
              <div class="relative rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <label
                  class="mb-4 block border-b border-gray-200 pb-2 text-sm font-medium text-gray-700"
                >
                  Sản phẩm
                </label>
                <input
                  ref="productInput"
                  type="text"
                  placeholder="Tìm kiếm sản phẩm"
                  v-model="searchTerm"
                  @focus="isDropdownVisible = true"
                  @blur="isDropdownVisible = false"
                  class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm"
                />
                <span v-if="form.errors.products" class="text-sm text-red-500">{{
                  form.errors.products
                }}</span>

                <ul
                  class="show-products absolute z-10 mt-1 w-[96.5%] overflow-auto rounded-md border border-gray-300 bg-white shadow-lg"
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
                  class="flex items-center space-x-2 rounded-lg bg-blue-100 p-2"
                >
                  <img :src="product.image" alt="" class="mr-2 h-6 w-6" />
                  <span class="flex-1 text-blue-700">{{ product.name }}</span>

                  <!-- Giá sản phẩm -->
                  <input
                    type="number"
                    placeholder="Giá"
                    v-model="product.price"
                    @blur="handleBlur(`products.${index}.price`)"
                    @input="validateProductField('price', index)"
                    class="w-30 ml-2 rounded-md border border-gray-300 px-2 py-1 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm"
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
                    class="w-30 ml-2 rounded-md border border-gray-300 px-2 py-1 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm"
                  />
                  <span
                    v-if="form.errors.products && form.errors.products[index]?.quantity"
                    class="text-sm text-red-500"
                  >
                    {{ form.errors.products[index].quantity }}
                  </span>

                  <button @click="removeProduct(index)" class="text-red-500 hover:text-red-700">
                    &times;
                  </button>
                </div>
              </div>
            </div>

            <!-- Right Column (Publish, Status, End Date) -->
            <div class="col-span-3 space-y-6">
              <!-- Publish Section -->
              <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700">Xuất bản</h3>
                <div class="mt-4 flex space-x-2">
                  <!-- Save Button -->
                  <button
                    type="submit"
                    class="flex w-[60%] items-center justify-center rounded-md bg-blue-700 px-0 py-1 text-gray-200 hover:bg-blue-600 focus:outline-none"
                  >
                    Lưu
                  </button>
                  <!-- Save & Exit Button -->
                  <button
                    @click="submitForm(true)"
                    type="button"
                    class="flex w-full items-center justify-center rounded-md bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200 focus:outline-none"
                  >
                    Lưu & Thoát
                  </button>
                </div>
              </div>

              <!-- Status Section -->
              <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <SelectComponent
                  name="publish"
                  label="Trạng thái"
                  v-model="form.values.publish"
                  :options="PUBLISH"
                  :required="true"
                  placeholder="Chọn trạng thái"
                />
              </div>

              <!-- Start Date Section -->
              <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <InputDateComponent
                  name="start_date"
                  label="Ngày bắt đầu"
                  :required="true"
                  placeholder="Ngày bắt đầu"
                />
              </div>

              <!-- End Date Section -->
              <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <InputDateComponent
                  name="end_date"
                  label="Ngày kết thúc"
                  :required="true"
                  placeholder="Chọn ngày kết thúc"
                />
              </div>
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
import { ref, onMounted, computed, reactive } from 'vue';
import { useCRUD } from '@/composables';
import { useForm } from 'vee-validate';
import { validationSchema } from './validationSchema';
import { PUBLISH } from '@/static/constants';
import router from '@/router';
import { message } from 'ant-design-vue';
import { formatMessages } from '@/utils/format';

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
    publish: '',
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
    const response = await getAll('products/variants');
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

// Khi component được mount
onMounted(() => {
  fetchProducts();
});

</script>

<style scoped>
.show-products {
  max-height: 20rem;
  overflow-y: auto;
}
</style>
