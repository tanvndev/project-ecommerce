<template>
  <MasterLayout>
    <template #template>
      <div
        class="chat-container-wrapper mx-10 my-2 flex overflow-hidden rounded border bg-gray-100"
      >
        <!-- Sidebar -->
        <div class="w-1/4 rounded-[4px] border-r bg-white">
          <div class="p-4 pb-2">
            <h2 class="mb-3 text-lg font-bold">Tin nhắn</h2>
            <input
              type="text"
              placeholder="Tìm kiếm..."
              v-model="searchTearm"
              class="mb-4 w-full rounded border p-2 text-[14px]"
              @change="handleSearch"
            />
          </div>
          <div class="scrollable">
            <div
              v-for="chat in chatLists"
              :key="chat.id"
              class="chat-item flex cursor-pointer items-center px-3 py-3"
              :class="selectedChatUser.id == chat.id ? 'selected' : ''"
              @click="handleSelectChat(chat.id)"
            >
              <div class="relative">
                <img
                  :src="chat.image || 'https://via.placeholder.com/40'"
                  alt=""
                  class="mr-3 h-[50px] w-[50px] rounded-full"
                />
                <div class="status-indicator-1 active"></div>
              </div>
              <div class="w-full">
                <div class="flex w-full items-center justify-between">
                  <div class="header-title">{{ chat.fullname }}</div>
                  <small class="me-2 text-gray-500">
                    {{ chat.last_at && timeAgo(chat.last_at) }}
                  </small>
                </div>
                <div class="header-meta w-64 truncate font-bold">
                  {{ chat.last_message || 'No messages yet.' }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Chat Box -->
        <div class="flex flex-1 flex-col bg-white" v-if="selectedChatUser !== null">
          <!-- Top: Phần top đứng yên -->
          <div class="flex-none border-b p-4">
            <div class="flex items-center">
              <div class="relative">
                <img
                  :src="selectedChatUser?.image || 'https://via.placeholder.com/40'"
                  alt=""
                  class="mr-3 h-[50px] w-[50px] rounded-full"
                />
                <div class="status-indicator active"></div>
              </div>
              <div>
                <div class="header-title">{{ selectedChatUser?.fullname }}</div>
                <div class="header-meta">Đang hoạt động</div>
              </div>
            </div>
          </div>

          <!-- Middle: Phần giữa có thể cuộn -->
          <div
            class="chat-container flex-grow overflow-y-auto p-4"
            v-if="messages?.length > 0"
            ref="messagesContainer"
          >
            <div v-for="message in messages" :key="message.id" class="mb-4">
              <div v-if="message.sender_id == user?.id" class="mt-2 flex justify-end">
                <div>
                  <div class="mb-2 ml-2" v-if="message?.images && message?.images.length">
                    <div class="flex justify-end">
                      <div
                        v-for="(image, index) in message?.images"
                        class="image-upload mr-1"
                        :key="index"
                      >
                        <img width="50" height="50" :src="image" alt="" />
                      </div>
                    </div>
                  </div>
                  <div>
                    <p class="inline-block rounded-[6px] bg-[#3b71ca] px-[16px] py-3 text-white">
                      {{ message?.message }}
                    </p>
                  </div>
                </div>
              </div>
              <div v-else class="mt-2 flex justify-start">
                <div>
                  <div class="mb-2 ml-11" v-if="message?.images && message?.images.length">
                    <div class="flex justify-end">
                      <div
                        v-for="(image, index) in message?.images"
                        class="image-upload mr-1"
                        :key="index"
                      >
                        <img
                          width="50"
                          height="50"
                          :src="image || 'https://via.placeholder.com/40'"
                          alt=""
                        />
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center">
                    <img
                      :src="selectedChatUser?.image || 'https://via.placeholder.com/40'"
                      alt="User Image"
                      class="mr-3 h-8 w-8 rounded-full"
                    />

                    <p class="inline-block rounded-[6px] bg-gray-100 px-[16px] py-3">
                      {{ message?.message }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom: Phần input tin nhắn đứng yên -->
          <div class="flex border-t p-4">
            <div class="flex w-[50px] items-center">
              <button
                type="button"
                class="h-[35px] w-[35px] rounded-full bg-gray-200 transition-all duration-100 hover:bg-blue-200 hover:text-blue-900"
              >
                <i class="fas fa-image"></i>
              </button>
            </div>
            <div class="flex flex-1 items-center">
              <input
                type="text"
                placeholder="Nhập tin nhắn..."
                class="message-input"
                v-model="newMessage"
                @keyup.enter="sendMessage"
              />
              <button class="rounded-r bg-blue-500 px-4 py-[9px] text-white" @click="sendMessage">
                <i class="fas fa-paper-plane"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>
  </MasterLayout>
</template>

<script setup>
import { MasterLayout } from '@/components/backend';
import { useCRUD } from '@/composables';
import pusher from '@/plugins/pusher';
import { debounce } from '@/utils/helpers';
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import { useStore } from 'vuex';

import { timeAgo } from '@/utils/helpers';
const chatLists = ref([]);
const selectedChatUser = ref(null);
const messages = ref([]);
const newMessage = ref('');
const messagesContainer = ref(null);
const searchTearm = ref('');

const store = useStore();
const { getAll, getOne, create, data } = useCRUD();
const user = computed(() => store.getters['authStore/getUser']);

const fetchChatList = async () => {
  try {
    await getAll('chats/list', {
      search: searchTearm.value
    });
    chatLists.value = data.value.data;
    selectedChatUser.value = data.value.data[0];
  } catch (error) {
    console.error('Error fetching chat list:', error);
  }
};
const debounceFetchChatList = debounce(fetchChatList, 300);

const handleSearch = () => {
  debounceFetchChatList();
};

const handleSelectChat = async (receiver_id) => {
  selectedChatUser.value = chatLists.value?.find((user) => user.id === receiver_id);

  await fetchReceiverMessages(receiver_id);
};

const fetchReceiverMessages = async (receiver_id) => {
  try {
    const response = await getOne('chats/message', receiver_id);
    messages.value = response;

    await nextTick();
    scrollToBottom();
  } catch (error) {
    console.error('Error fetching messages:', error);
  }
};

const sendMessage = async () => {
  if (newMessage.value.trim() === '') return;

  const message = {
    message: newMessage.value,
    sender_id: user.value.id
  };

  try {
    await create(
      `chats/${selectedChatUser.value.id}/send`,
      {
        message: newMessage.value
      },
      false
    );

    messages.value.push(message);
    newMessage.value = '';

    await nextTick();
    scrollToBottom();
  } catch (error) {
    console.error('Error sending message:', error);
  }
};

const scrollToBottom = () => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
  }
};

const initializeChat = async () => {
  try {
    await fetchChatList();
    await handleSelectChat(selectedChatUser.value.id);

    const channel = pusher.subscribe(`private-chat-channel.${user.value.id}`);
    channel.bind('message-sent-event', handleIncomingMessage);
  } catch (error) {
    console.error('Error initializing chat:', error);
  }
};

const handleIncomingMessage = async (data) => {
  if (data.message.sender_id !== user.value.id) {
    messages.value.push(data.message);

    await nextTick();
    scrollToBottom();
  }
};

onMounted(async () => {
  await initializeChat();
});

onBeforeUnmount(() => {
  pusher.unsubscribe('private-chat-channel.' + user.value.id);
});
</script>

<style scoped>
.image-upload {
  position: relative;
  border: 1px solid #f1f1f1;
  z-index: 1;
  border-radius: 4px;
  padding: 5px;
  display: grid;
  place-content: center;
}
.image-upload .icon-delete {
  position: absolute;
  top: -10px;
  right: -7px;
  transition: all 0.1s linear;
  cursor: pointer;
  font-size: 16px;
  z-index: 3;
}
.image-upload .icon-delete:hover {
  color: #dc4c64;
}
</style>
