<script setup>

useSeoMeta({
  title: 'Địa chỉ',
  ogTitle: 'Địa chỉ',
  description: 'Địa chỉ.',
  ogDescription: 'Địa chỉ.',
})

const { $axios, $authService } = useNuxtApp()
const authStore = useAuthStore()
const user = computed(() => authStore.getUser)
const isOpenModalAddress = ref(false)

const fetch = async (endpoint = '', payload = {}, action = 'update') => {
  try {
    let response =
      action == 'update'
        ? await $axios.post(endpoint, payload)
        : await $axios.delete(endpoint)

    if (response.status == 'success') {
      toast(response.messages)

      const user = await $authService.me()
      authStore.setUser(user.data)
    }
  } catch (error) {
    toast(
      formatMessages(error?.response?.data?.messages) || 'Thao tác thất bại',
      'error'
    )
  }
}

const handleUpdatePrimaryAddress = async (id) => {
  const endpoint = `users/addresses/${id}?_method=PUT`
  const payload = {
    is_primary: true,
  }
  fetch(endpoint, payload, 'update')
}

const handleRemoveAddress = async (id) => {
  const endpoint = `users/addresses/${id}`
  fetch(endpoint, {}, 'delete')
}
</script>

<template>
  <div class="page-content pt-2 mt-6">
    <div class="container">
      <div class="tab tab-vertical row gutter">
        <div class="col-lg-3">
          <UserSidebar />
        </div>
        <div class="col-lg-9">
          <v-card
            class="mx-auto pa-2"
            title="Địa chỉ của tôi"
            subtitle="Quản lý thông tin địa chỉ của bạn"
          >
            <v-divider class="border-opacity-100"></v-divider>

            <div class="px-2">
              <div class="d-flex justify-end">
                <v-btn
                  variant="tonal"
                  color="success"
                  @click="isOpenModalAddress = true"
                  prepend-icon="mdi-plus"
                  >Thêm địa chỉ mới</v-btn
                >
              </div>
              <v-row class="mt-4" v-if="user?.addresses?.length">
                <v-col cols="12" md="12">
                  <div
                    class="customer-user py-3"
                    v-for="item in user?.addresses"
                    :key="item.id"
                  >
                    <div class="row gutter-sm items-center">
                      <div class="col-xs-9">
                        <div class="v2-checkout-address-inner">
                          <div>
                            <div class="v2-address-title-container">
                              <span class="v2-address-title">{{
                                item.fullname
                              }}</span>
                              <span class="v2-mobile">{{ item.phone }}</span>
                            </div>
                          </div>
                          <div class="v2-address-info-item">
                            <span
                              class="v2-address-tag-label mr-2"
                              style="
                                background-image: linear-gradient(
                                  -143deg,
                                  rgb(255, 123, 83) 0%,
                                  rgb(255, 75, 40) 100%
                                );
                              "
                              >NHÀ RIÊNG</span
                            >
                            <span class="v2-address-info-address">
                              {{ item.shipping_address }}
                            </span>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-3">
                        <div class="d-flex justify-end mr-1">
                          <a
                            v-if="item.is_primary == 0"
                            @click.prevent="handleRemoveAddress(item.id)"
                            :href="`/delete/${item.id}`"
                          >
                            Xóa</a
                          >
                        </div>
                        <div class="mt-1 d-flex justify-end">
                          <v-btn
                            density="compact"
                            @click="handleUpdatePrimaryAddress(item.id)"
                            :color="item.is_primary == 1 ? 'primary' : ''"
                            :disabled="item.is_primary == 1"
                            >Mặc định</v-btn
                          >
                        </div>
                      </div>
                    </div>
                  </div>
                </v-col>
              </v-row>

              <v-empty-state
                class="mt-5"
                v-else
                icon="mdi-magnify"
                title="Không có dữ liệu."
                text="Bạn chưa có địa chỉ nào vui lòng thêm địa chỉ."
              ></v-empty-state>
            </div>
          </v-card>
        </div>

        <IncludesLocationModal :is-open-modal-address="isOpenModalAddress" />
      </div>
    </div>
  </div>
</template>
