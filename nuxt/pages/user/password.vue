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
            title="Đổi mật khẩu"
            subtitle="Quản lý thông tin mật khẩu của bạn"
          >
            <v-divider class="border-opacity-100"></v-divider>

            <div class="px-2">
              <div class="mb-3">
                <v-text-field
                  label="Mật khẩu cũ"
                  hide-details
                  variant="outlined"
                ></v-text-field>
              </div>
              <div class="mb-3">
                <v-text-field
                  label="Mật khẩu mới"
                  hide-details
                  variant="outlined"
                ></v-text-field>
              </div>
              <div class="mb-3">
                <v-text-field
                  label="Xác nhận mật khẩu mới"
                  hide-details
                  variant="outlined"
                ></v-text-field>
              </div>
              <div class="mb-3">
                <v-btn color="success" variant="tonal" class="text-capitalize">
                  Cập nhập mật khẩu
                </v-btn>
              </div>
            </div>
          </v-card>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
useSeoMeta({
  title: 'Đổi mật khẩu',
  ogTitle: 'Đổi mật khẩu',
  description: 'Đổi mật khẩu.',
  ogDescription: 'Đổi mật khẩu.',
})
</script>
