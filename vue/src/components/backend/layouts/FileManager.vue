<template>
  <div
    class="bg-modal upload-image-ant relative h-screen w-full"
    @click.self="closeModal"
    v-if="visible"
  >
    <div
      class="container absolute mx-auto drop-shadow-xl"
      :style="{ top: top + 'px', left: left + 'px', transform: 'translate(-50%, -50%)' }"
      @mousedown="startDrag"
    >
      <div
        class="header flex cursor-move justify-end border-b bg-gray-100 py-2 pr-3"
        @mousedown.stop="enableDrag"
      >
        <a-button class="" type="text" @click="closeModal"
          ><i class="fal fa-times text-l"></i
        ></a-button>
      </div>
      <div class="mt-3">
        <div class="flex justify-between border-b px-2 pb-3">
          <a-space :size="10">
            <input
              type="file"
              class="hidden"
              multiple
              ref="fileInputRef"
              @change="handleUploadFile"
            />
            <a-button @click="fileInputRef?.click()">
              <i class="fal fa-upload mr-2"></i>
              <span> Tải tệp lên </span>
              <span v-if="selectedFileInput" class="ml-2 font-bold text-blue-500"
                >({{ selectedFileInput.length }}) tệp.</span
              >
            </a-button>
            <a-button @click="fetchData">
              <i class="fal fa-redo mr-2"></i>
              <span> Tải lại trang </span>
            </a-button>
          </a-space>
          <a-button type="link" class="text-black">
            <i class="fal fa-cog rotate-center text-[22px]"></i>
          </a-button>
        </div>
        <a-row>
          <a-col span="20" class="container-scroll overflow-y-auto border-r">
            <div class="mt-2 px-2">
              <AleartError :errors="errors || {}" />
            </div>
            <div class="spin-mask" v-if="loading">
              <a-spin />
            </div>
            <div class="grid w-full grid-cols-7 gap-3 p-2" :class="{ 'blur-[2px]': loading }">
              <div
                class="card-image overflow-hidden rounded-lg bg-gray-50 p-1 text-gray-600 drop-shadow-sm"
                v-for="image in dataImage"
                :key="image.id"
                :class="selectedFile.includes(image.link) ? 'active' : ''"
                @click="chooseFile(image)"
              >
                <img
                  class="w-full rounded-[6px] object-cover"
                  :src="resizeImage(image.link, 300)"
                  :alt="image.name"
                />
                <div class="mt-2 flex flex-col px-1 text-[12px]">
                  <span class="truncate">{{ image.name }}</span>
                </div>
                <span @click.stop="removeChooseFile(image.link)" class="icons delete-choose-file">
                  <i class="far fa-minus"></i>
                  <i class="far fa-check"></i>
                </span>
              </div>
            </div>

            <div class="mb-4 mt-4 flex justify-center">
              <a-pagination
                v-model:current="pagination.current"
                @change="onChangePage"
                :showQuickJumper="pagination.showQuickJumper"
                :total="pagination.total"
                :pageSizeOptions="['30', '50', '80', '100', '200']"
                :defaultPageSize="30"
                :pageSize="pagination.pageSize"
                :hideOnSinglePage="pagination.hideOnSinglePage"
              />
            </div>
          </a-col>
          <a-col span="4" class="container-scroll overflow-y-auto">
            <div class="p-3" v-if="imageInfo">
              <h2 class="text-l mb-2 uppercase">Chi tiết tệp đính kèm</h2>
              <a-image
                class="w-full rounded-[6px] border object-cover"
                :src="resizeImage(imageInfo.link)"
                :alt="imageInfo.name"
              />
              <div class="mt-2 flex flex-col px-1 text-[12px] text-gray-700">
                <span class="mb-1 font-bold text-blue-700">{{ imageInfo.name }}</span>
                <span>{{ formatTimestampToDate(imageInfo.lastModified) }}</span>
                <span>{{ formatBytesToKBMB(imageInfo.size) }}</span>
              </div>
              <a-space class="mt-2 pb-3">
                <a-popconfirm
                  title="Bạn có chắc chắn muốn xóa?"
                  ok-text="Xóa"
                  cancel-text="Hủy"
                  @confirm="handleDeleteFile(imageInfo.url)"
                >
                  <a-button danger size="small" type="primary">
                    <i class="fas fa-trash-alt mr-1 text-[12px]"></i>
                    <span class="text-[10px] font-bold uppercase">Xóa</span>
                  </a-button>
                </a-popconfirm>
              </a-space>
              <div class="border-t">
                <div class="mt-2">
                  <a-input v-model:value="imageInfo.link" readonly addon-before="URL" />
                </div>
              </div>

              <a-float-button
                shape="circle"
                type="primary"
                class="bottom-[30px] right-[40px]"
                @click="handleSaveFile"
              >
                <template #icon> <i class="fal fa-save"></i> </template>
              </a-float-button>
            </div>
          </a-col>
        </a-row>
      </div>
    </div>
  </div>
</template>

<script setup>
import { AleartError } from '@/components/backend';
import { useCRUD } from '@/composables';
import { formatBytesToKBMB, formatMessages, formatTimestampToDate } from '@/utils/format';
import { resizeImage } from '@/utils/helpers';
import { message } from 'ant-design-vue';
import { computed, reactive, ref, watch } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const routePath = computed(() => route.path);
const errors = ref({});
const dragging = ref(false);
const top = ref(window.innerHeight / 2);
const left = ref(window.innerWidth / 2);
const mouseX = ref(0);
const mouseY = ref(0);
const selectedFile = ref([]);
const selectedFileInput = ref(null);
const fileInputRef = ref(null);
const dataImage = ref(null);
const imageInfo = ref(null);
const pagination = reactive({
  current: 1,
  pageSize: 30,
  total: 0,
  showQuickJumper: true,
  hideOnSinglePage: true
});

const { getAll, create, deleteOne, data, messages, loading } = useCRUD();

const props = defineProps({
  isVisible: {
    type: Boolean,
    default: false
  },
  multiple: {
    type: Boolean,
    default: false
  }
});
const visible = ref(props.isVisible);

const emits = defineEmits(['onClose', 'onSelected']);
watch(
  () => props.isVisible,
  (newVal) => (visible.value = newVal)
);

watch(visible, () => {
  if (visible.value) fetchData();
});

const onChangePage = (page, pageSize) => fetchData({ page: page, pageSize: pageSize });

const handleUploadFile = async (event) => {
  try {
    selectedFileInput.value = event.target.files;

    const pathArray = routePath.value?.split('/');

    if (!pathArray || pathArray.length == 0 || pathArray[1] == '')
      throw new Error('Url không hợp lệ');

    const folderName = `${pathArray[1] || ''}/${selectedFileInput.value?.length > 1 ? 'album' : 'thumb'}`;

    const payload = new FormData();
    for (let i = 0; i < selectedFileInput.value.length; i++) {
      payload.append('files[]', selectedFileInput.value[i]);
    }
    payload.append('folder_name', folderName);
    await create('uploads', payload);
    errors.value = formatMessages(messages.value);
    dataImage.value = data.value.data;
    reloadAll();
  } catch (error) {
    message.error('Có lỗi vui lòng thử lại.');
  }
};

const handleSaveFile = async () => {
  emits('onSelected', selectedFile.value);
  closeModal();
};

const reloadAll = () => {
  selectedFile.value = [];
  selectedFileInput.value = null;
  imageInfo.value = null;
  errors.value = {};
  centerModal();
};
const closeModal = () => {
  reloadAll();
  emits('onClose');
};

const chooseFile = (image) => {
  const index = selectedFile.value.indexOf(image.link);

  imageInfo.value = image;

  if (index > -1) {
    return false;
  }

  selectedFile.value = props.multiple ? [...selectedFile.value, image.link] : [image.link];
};

const removeChooseFile = (link) => {
  const index = selectedFile.value.indexOf(link);
  if (index > -1) {
    selectedFile.value.splice(index, 1);
  }
  if (selectedFile.value.length === 0) {
    imageInfo.value = null;
  }
};

const centerModal = () => {
  top.value = window.innerHeight / 2;
  left.value = window.innerWidth / 2;
};

const enableDrag = (event) => {
  dragging.value = true;
  mouseX.value = event.clientX - left.value;
  mouseY.value = event.clientY - top.value;
  window.addEventListener('mousemove', onMove);
  window.addEventListener('mouseup', stopDrag);
};

const startDrag = (event) => {
  if (!dragging.value) return;
  mouseX.value = event.clientX - left.value;
  mouseY.value = event.clientY - top.value;
  window.addEventListener('mousemove', onMove);
  window.addEventListener('mouseup', stopDrag);
};

const stopDrag = () => {
  dragging.value = false;
  window.removeEventListener('mousemove', onMove);
  window.removeEventListener('mouseup', stopDrag);
};

const onMove = (event) => {
  if (dragging.value) {
    top.value = event.clientY - mouseY.value;
    left.value = event.clientX - mouseX.value;
  }
};

const handleDeleteFile = async (url) => {
  const payload = {
    url
  };
  await deleteOne('uploads', 1, payload);
  dataImage.value = data.value.data;
  message.success(messages.value);
};

const fetchData = async (payload = null) => {
  await getAll('uploads', payload);
  if (!data.value) return;
  pagination.total = data.value.total;
  pagination.pageSize = data.value.per_page;
  pagination.current = data.value.current_page;
  dataImage.value = data.value.data;
  reloadAll();
};
</script>

<style scoped>
.spin-mask {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1000;
  display: flex;
  justify-content: center;
  align-items: center;
  background: rgba(0, 0, 0, 0.05);
}
.bg-modal {
  background-color: rgba(0, 0, 0, 0.4);
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1000;
}
.header {
  user-select: none;
}
.container {
  width: 100%;
  height: calc(100vh - 100px);
  background-color: #fff;
  border-radius: 6px;
  overflow: hidden;
  transition:
    top 0.1s ease,
    left 0.1s ease; /* Smooth transition for dragging */
}
.tabs-dropdown:hover {
  background-color: #f5f5f5;
}

::-webkit-scrollbar {
  width: 5px;
}
::-webkit-scrollbar-track {
  background: #f1f1f1;
}
::-webkit-scrollbar-thumb {
  background: #9999;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: #999;
}
.card-image {
  position: relative;
  transition: box-shadow 0.2s ease-in-out;
  cursor: pointer;
  border: 2px solid transparent;
  user-select: none;
}
.card-image img {
  user-select: none;
  pointer-events: none;
  object-fit: contain;
}
.card-image::before {
  position: absolute;
  content: '';
  bottom: -78px;
  right: -77px;
  display: block;
  height: 100px;
  width: 100px;
  background-color: transparent;
  transform: rotate(90);
  border-radius: 8px;
}
.card-image.active {
  border-color: #3b74f0dc;
}
.card-image.active::before {
  background-color: #3b74f0dc;
}
.card-image:hover {
  box-shadow: 3px 1px 15px rgba(0, 0, 0, 0.1);
}
.delete-choose-file {
  position: absolute;
  bottom: -5px;
  right: 3px;
  color: #fff;
  font-size: 18px;
  cursor: pointer;
  font-weight: bolder;
  transform: scale(1);
  display: none;
}
.card-image.active .delete-choose-file {
  display: block;
}
.delete-choose-file .fa-check {
  font-size: 15px;
}
.delete-choose-file .fa-minus {
  display: none;
}

.delete-choose-file:hover .fa-check {
  display: none;
}
.delete-choose-file:hover .fa-minus {
  display: inline;
}
.container-scroll {
  height: calc(100vh - 210px);
}
</style>
