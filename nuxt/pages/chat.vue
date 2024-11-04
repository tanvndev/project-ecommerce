<script setup>
const { $axios, $pusher } = useNuxtApp()
const authStore = useAuthStore()
const chatLists = ref([])
const selectedChatUser = ref(null)
const messages = ref([])
const messagesContainer = ref(null)
const user = computed(() => authStore.getUser)
const newMessage = ref('')
const uploadedImages = ref([])
const fileInput = ref(null)

const getChatLists = async () => {
  try {
    const response = await $axios.get('/chats/user/list')
    chatLists.value = response.data
    selectedChatUser.value = response.data[0]
  } catch (error) {
    console.log(error)
  }
}

const handleSelectChat = async (receiver_id) => {
  selectedChatUser.value = chatLists.value?.find(
    (user) => user.id === receiver_id
  )

  await fetchReceiverMessages(receiver_id)
}

const fetchReceiverMessages = async (receiver_id) => {
  try {
    const response = await $axios.get('chats/message/' + receiver_id)
    messages.value = response.data

    await nextTick()
    scrollToBottom()
  } catch (error) {
    console.error('Error fetching messages:', error)
  }
}

const scrollToBottom = () => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const initializeChat = async () => {
  try {
    await getChatLists()
    await handleSelectChat(selectedChatUser.value.id)

    const channel = $pusher.subscribe(`private-chat-channel.${user.value.id}`)
    channel.bind('message-sent-event', handleIncomingMessage)
  } catch (error) {
    console.error('Error initializing chat:', error)
  }
}

const handleIncomingMessage = async (data) => {
  if (data.message.sender_id !== user.value.id) {
    messages.value.push(data.message)

    await nextTick()
    scrollToBottom()
  }
}

const sendMessage = async () => {
  if (newMessage.value.trim() === '') return

  const formData = new FormData()
  const message = {
    message: newMessage.value,
    sender_id: user.value.id,
  }

  formData.append('message', newMessage.value)

  for (let i = 0; i < uploadedImages.value.length; i++) {
    const imageBlob = await fetch(uploadedImages.value[i]).then((res) =>
      res.blob()
    )
    formData.append('images[]', imageBlob, `image_${i}.png`)
  }
  if (uploadedImages.value.length) {
    message.images = uploadedImages.value
  }

  try {
    await $axios.post(`chats/${selectedChatUser.value?.id}/send`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

    uploadedImages.value = []
    newMessage.value = ''

    messages.value.push(message)

    await nextTick()
    scrollToBottom()
  } catch (error) {
    console.error('Error sending message:', error)
  }
}

const handleFileChange = (event) => {
  const files = event.target.files

  if (uploadedImages.value?.length >= 5) {
    return
  }

  if (files.length + uploadedImages.value.length > 5) {
    return toast('Vui lòng chọn tối đa 5 hình ảnh.', 'error')
  }

  const filePromises = Array.from(files).map((file) => {
    return new Promise((resolve) => {
      const reader = new FileReader()

      reader.onload = (e) => {
        resolve(e.target.result)
      }

      reader.readAsDataURL(file)
    })
  })

  Promise.all(filePromises)
    .then((imageData) => {
      uploadedImages.value.push(...imageData)
    })
    .catch((error) => {
      console.error('Error reading files:', error)
    })
}

const removeImage = (index) => {
  uploadedImages.value.splice(index, 1)
}

onMounted(async () => {
  await initializeChat()
})

onBeforeUnmount(() => {
  $pusher.unsubscribe('private-chat-channel.' + user.value.id)
})
</script>

<template>
  <section class="chat-section">
    <div class="py-3 container px-0">
      <div class="row w-100">
        <!-- <div class="col-md-6 col-lg-5 col-xl-4 mb-4 mb-md-0 sidebar-chat">
          <div class="p-3 mt-9">
            <div class="chat-wrap">
              <ul class="list-unstyled mb-0 chat-list">
                <li
                  class="chat-item"
                  :class="item.id == selectedChatUser?.id ? 'selected' : ''"
                  v-for="item in chatLists"
                  :key="item?.id"
                  @click="handleSelectChat(item.id)"
                >
                  <div class="d-flex justify-content-between">
                    <div class="d-flex flex-row">
                      <div>
                        <v-img
                          :src="item.image"
                          :alt="item.fullname"
                          class="d-flex align-self-center me-3 w-40"
                          width="50"
                          rounded="circle"
                        />
                        <span class="badge bg-success badge-dot"></span>
                      </div>
                      <div class="pt-1">
                        <p class="fw-bold mb-0">{{ item.fullname }}</p>
                        <p class="mb-0 text-muted">{{ item.last_message }}</p>
                      </div>
                    </div>
                    <div class="pt-1">
                      <p class="mb-0 text-muted">{{ timeAgo(item.last_at) }}</p>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div> -->
        <div class="col-md-12 col-lg-12 col-xl-12 pl-0">
          <div class="card-wrap">
            <div
              class="card-header d-flex justify-content-between align-items-center p-3"
            >
              <h5 class="mb-0 header-title">Trò chuyện cùng người bán</h5>
            </div>
            <div class="card" ref="messagesContainer">
              <div class="card-body">
                <div v-for="message in messages" :key="message.id" class="mb-2">
                  <div
                    v-if="message.receiver_id == user?.id"
                    class="d-flex flex-row justify-content-start message-inner"
                  >
                    <img
                      :src="
                        selectedChatUser.image ||
                        'https://via.placeholder.com/40'
                      "
                      alt="avatar 1"
                      style="width: 45px; height: 100%"
                    />
                    <div style="max-width: 75%">
                      <div
                        class="mb-2 ml-2"
                        v-if="message.images && message.images.length"
                      >
                        <div class="d-flex">
                          <div
                            v-for="(image, index) in message.images"
                            class="mr-1 image-upload"
                            :key="index"
                          >
                            <v-img
                              width="50"
                              height="50"
                              :src="image"
                              alt=""
                              rounded
                              contain
                            />
                          </div>
                        </div>
                      </div>
                      <p class="text-item bg-body-tertiary">
                        {{ message.message }}
                      </p>
                    </div>
                  </div>

                  <div
                    class="d-flex flex-row justify-content-end mb-0 pt-1 message-inner"
                    v-else
                  >
                    <div>
                      <div
                        class="mb-2 ml-2"
                        v-if="message.images && message.images.length"
                      >
                        <div class="d-flex">
                          <div
                            v-for="(image, index) in message.images"
                            class="mr-1 image-upload"
                            :key="index"
                          >
                            <v-img
                              width="50"
                              height="50"
                              :src="image"
                              alt=""
                              rounded
                              contain
                            />
                          </div>
                        </div>
                      </div>
                      <p
                        class="text-item sender text-white rounded-3 bg-primary"
                      >
                        {{ message.message }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              <!--  -->
              <div class="card-footer text-muted">
                <div class="mb-2 pl-49" v-if="uploadedImages.length > 0">
                  <div class="d-flex">
                    <div
                      v-for="(image, index) in uploadedImages"
                      class="mr-3 image-upload"
                      :key="index"
                    >
                      <v-img
                        width="50"
                        height="50"
                        :src="image"
                        alt="image"
                        rounded
                        contain
                      />
                      <span class="icon-delete" @click="removeImage(index)">
                        <i class="fas fa-times-circle text-danger"></i>
                      </span>
                    </div>
                  </div>
                </div>
                <div
                  class="d-flex justify-content-start align-items-center w-100"
                >
                  <input
                    type="file"
                    ref="fileInput"
                    accept="image/*"
                    @change="handleFileChange"
                    style="display: none"
                    multiple
                  />
                  <v-btn
                    icon="mdi-image"
                    color="primary"
                    variant="text"
                    @click="fileInput.click()"
                  >
                  </v-btn>
                  <div class="input-group">
                    <v-text-field
                      v-model.value="newMessage"
                      variant="outlined"
                      :clearable="true"
                      placeholder="Nhập tin nhắn..."
                      @keyup.enter="sendMessage"
                      density="comfortable"
                    ></v-text-field>
                  </div>
                  <v-btn
                    @click="sendMessage"
                    icon="mdi-send"
                    color="primary"
                    variant="text"
                  >
                  </v-btn>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<style scoped>
.image-upload {
  position: relative;
  border: 1px solid #f1f1f1;
  z-index: 1;
  border-radius: 4px;
  padding: 5px;
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
