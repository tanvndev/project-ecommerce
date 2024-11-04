<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen">
        <BreadcrumbComponent :titlePage="state.pageTitle" @on-save="onSubmit" />
        <a-card class="mt-3">
          <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full border-collapse text-left text-sm text-gray-500">
              <thead class="bg-gray-100 text-xs uppercase text-gray-700 dark:text-gray-400">
                <tr>
                  <th>Tên quyền</th>
                  <th
                    class="text-center"
                    v-for="userCatalogue in state.userCatalogues"
                    :key="userCatalogue.key + 'userCatalogues'"
                  >
                    {{ userCatalogue.name }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  class="border-b bg-white hover:bg-gray-50"
                  v-for="permission in state.permissions"
                  :key="permission.key + 'permission'"
                >
                  <td>
                    <div class="flex justify-between">
                      <span class="text-blue-500">{{ permission.name }}</span>
                      <span class="text-orange-500">({{ permission.canonical }})</span>
                    </div>
                  </td>

                  <td
                    class="text-center"
                    v-for="userCatalogue in state.userCatalogues"
                    :key="userCatalogue.id + 'checkbox'"
                  >
                    <label class="container-cbx">
                      <input
                        type="checkbox"
                        :checked="isChecked(userCatalogue.id, permission.id)"
                        @change="handleChecked(userCatalogue, permission.id)"
                      />
                      <div class="checkmark"></div>
                    </label>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="fixed bottom-[40px] right-[40px]">
            <a-button @click="onSubmit" :loading="loading" type="primary">
              <i class="fas fa-save mr-2"></i>
              <span>Lưu thông tin</span>
            </a-button>
          </div>
        </a-card>
      </div>
    </template>
  </MasterLayout>
</template>

<script setup>
import { BreadcrumbComponent, MasterLayout } from '@/components/backend';
import { useCRUD } from '@/composables';
import { onMounted, reactive } from 'vue';
import { useStore } from 'vuex';

const store = useStore();
const { getAll, update, loading, data, messages } = useCRUD();

// STATE
const state = reactive({
  endpoint: 'users/catalogues/permissions',
  pageTitle: 'Cập nhập phân quyền người dùng',
  permissions: [],
  userCatalogues: [],
  permissionChecked: {}
});

const isChecked = (userCatalogueId, permissionId) => {
  if (!state.permissions || !state.userCatalogues) return false;

  const userCatalogue = state.userCatalogues.find((uc) => uc.id === userCatalogueId);
  if (!userCatalogue) return false;

  return userCatalogue.permissions.some((p) => p.id === permissionId);
};

const onSubmit = async () => {
  const formData = new FormData();
  formData.append('permissions', JSON.stringify(state.permissionChecked));
  await update(state.endpoint, 1, formData);

  store.dispatch('antStore/showMessage', { type: 'success', message: messages.value });
};

const handleChecked = (userCatalogue, permissionId) => {
  if (!state.permissionChecked[userCatalogue.id]) {
    state.permissionChecked[userCatalogue.id] = [];
  }

  const index = state.permissionChecked[userCatalogue.id].indexOf(permissionId);
  if (index === -1) {
    state.permissionChecked[userCatalogue.id].push(permissionId);
  } else {
    state.permissionChecked[userCatalogue.id].splice(index, 1);
  }
};

const getPermissions = async () => {
  await getAll('permissions');
  state.permissions = data.value;
};

const getUserCatalogues = async () => {
  await getAll('users/catalogues');

  data.value.forEach((userCatalogue) => {
    const permissionIds = userCatalogue.permissions.map((p) => p.id);
    state.permissionChecked[userCatalogue.id] = permissionIds;
  });

  // Gán userCatalogues.value từ data.value
  state.userCatalogues = data.value;
};

// Lifecycle hook
onMounted(() => {
  getPermissions();
  getUserCatalogues();
});
</script>

<style scoped>
.container-cbx {
  width: 35px;
  height: 35px;
  display: inline-block;
}

.checkmark::after {
  left: 0.96em;
  top: 0.7em;
  width: 0.4em;
  height: 0.8em;
}
thead th {
  text-align: center;
  font-weight: bold;
  font-size: 15px;
  padding: 16px 24px;
}
tbody td {
  padding: 16px 24px;
  text-align: center;
  font-size: 15px;
}
</style>
