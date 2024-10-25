<script setup>
import { onMounted, ref } from 'vue';
import axios from '@/configs/axios';
import { debounce, resizeImage } from '@/utils/helpers';
import { TooltipComponent } from '@/components/backend';

const currentUser = ref(null);
const user_address = ref(null);
const users = ref([]);
const search = ref('');

const emits = defineEmits(['changeUser']);

const getAllUser = async (search = '') => {
  try {
    const response = await axios.get('/users?page=1&pageSize=6&publish=0&search=' + search);
    users.value = response?.data.data;
  } catch (error) {
    console.log(error);
  }
};

const debounceHandleSearch = debounce(() => {
  getAllUser(search.value);
}, 500);

const handleSearch = () => {
  debounceHandleSearch();
};

const getUser = async (user) => {
  currentUser.value = user;
  user_address.value = user?.addresses?.find((item) => item.is_primary == 1);
  users.value = [];
  search.value = '';

  emits('changeUser', {
    address: user_address.value,
    user: currentUser.value
  });
};

onMounted(() => {
  //   getAllUser();
});
</script>
<template>
  <a-card class="mt-3">
    <template #title>
      khách hàng
      <TooltipComponent
        title="Bạn có thể tìm kiếm khách hàng bằng tên, email hoặc số điện thoại."
      />

      <a-button @click="currentUser = null" shape="circle" class="float-end" v-if="currentUser">
        <i class="fal fa-times"></i>
      </a-button>
    </template>
    <div class="relative">
      <div>
        <a-input
          v-model:value="search"
          @change="handleSearch"
          placeholder="Tìm kiếm khách hàng "
          size="large"
        >
          <template #prefix>
            <i class="far fa-search pr-1 text-gray-500"></i>
          </template>
        </a-input>
      </div>

      <div class="list-user absolute" v-if="users?.length">
        <ul>
          <li class="list-group-item" v-for="user in users" :key="user.id" @click="getUser(user)">
            <div class="px-[20px] py-[18px]">
              <div class="flex items-center">
                <div class="mr-2">
                  <img
                    class="w-[50px] rounded-[4px]"
                    :src="resizeImage(user?.image, 200)"
                    :alt="user?.fullname"
                  />
                </div>
                <div class="col text-truncate">
                  <div class="">{{ user?.fullname }}</div>
                  <a :href="user?.email" target="_blank" class="text-primary-500"
                    >{{ user?.email }}
                  </a>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <div v-if="currentUser">
      <div class="mb-3 mt-6">
        <a-avatar :size="55">
          <template #icon>
            <img :src="currentUser?.image" :alt="currentUser?.fullname" />
          </template>
        </a-avatar>
      </div>
      <div class="flex flex-col gap-1">
        <span class="font-bold">{{ currentUser?.fullname }}</span>
        <a class="text-blue-500" :href="`mailto:${currentUser?.email}`">{{ currentUser?.email }}</a>
        <a class="text-blue-500" :href="`tel:${currentUser?.phone}`">{{ currentUser?.phone }}</a>
      </div>
      <a-divider />

      <div>
        <div class="flex items-center justify-between">
          <h2 class="mb-1 text-[16px] text-[#222]">Địa chỉ giao hàng</h2>
          <a-tooltip title="Thay đổi địa chỉ">
            <i class="far fa-pen cursor-pointer"></i>
          </a-tooltip>
        </div>

        <ul class="address">
          <li>{{ currentUser?.fullname }}</li>
          <li class="text-blue-500">
            <i class="fas fa-phone mr-2"> </i>
            <a class="text-blue-500" :href="`tel:${currentUser?.phone}`">{{
              currentUser?.phone
            }}</a>
          </li>
          <li>{{ user_address?.shipping_address }}</li>
          <li>{{ user_address?.ward }}</li>
          <li>{{ user_address?.district }}</li>
          <li>{{ user_address?.province }}</li>
          <li>
            <a
              class="text-blue-500"
              :href="`https://maps.google.com/?q=${user_address?.shipping_address}`"
              target="_blank"
              >Xem địa chỉ trên bản đồ</a
            >
          </li>
        </ul>
      </div>
    </div>
  </a-card>
</template>
<style scoped>
.list-user {
  top: 45px;
  width: 100%;
  background-color: #fff;
  box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
  border-radius: 4px;
}
.list-group-item {
  border-bottom: 1px solid #ebebeb;
  transition: all 0.15s ease-in-out;
  cursor: pointer;
}
.list-group-item:last-child {
  border-bottom: none;
}
.list-group-item:hover {
  background-color: #f8f8f8;
}

.address {
  margin-top: 7px;
  margin-bottom: 0;
}
.address li {
  margin-bottom: 5px;
}
</style>
