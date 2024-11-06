<template>
  <MasterLayout>
    <template #template>
      <div class="content-wrapper">
        <a-row :gutter="16">
          <!-- Left Column (Cấu hình cơ bản, Cấu hình nâng cao, Shortcode) -->
          <a-col :span="8">
            <!-- Basic Configuration Section -->
            <a-card title="Cấu hình cơ bản" bordered>
              <form layout="vertical">
                <a-row :gutter="[16, 16]">
                  <a-col :span="24">
                    <!-- <a-input placeholder="Slide chính" v-model="slideConfig.name" /> -->
                    <InputComponent
                      label="Tiêu đề sản phẩm"
                      :required="true"
                      name="name"
                      placeholder="Tiêu đề sản phẩm"
                    />
                  </a-col>

                  <a-col :span="24">
                    <!-- <a-input placeholder="main-slide" v-model="slideConfig.keyword" /> -->
                    <InputComponent
                      label="Keyword"
                      :required="true"
                      name="keyword"
                      placeholder="Keyword"
                    />
                  </a-col>

                  <a-col :span="24">
                    <InputComponent
                      label="Chiều rộng"
                      name="width"
                      placeholder="Chiều rộng"
                      suffix="px"
                    />
                  </a-col>

                  <a-col :span="24">
                    <InputComponent
                      label="Chiều dài"
                      name="height"
                      placeholder="Chiều dài"
                      suffix="px"
                    />
                  </a-col>

                  <a-col :span="24">
                    <SelectComponent
                      name="effect"
                      label="Hiệu ứng"
                      :options="EFFECT"
                      :showSearch="false"
                      placeholder="Chọn hiệu ứng"
                    />
                  </a-col>

                  <a-col :span="24">
                    <a-form-item label="Mũi tên">
                      <div class="ms-4 flex items-center">
                        <label class="switch">
                          <input type="checkbox" :checked="true" />
                          <span class="slider"></span>
                        </label>
                        <span class="status-text">Trạng thái <strong>Tắt</strong></span>
                      </div>
                    </a-form-item>
                  </a-col>
                </a-row>

                <a-form-item label="Điều hướng">
                  <a-radio-group v-model="slideConfig.navigation">
                    <a-radio value="dots">Dấu chấm</a-radio>
                    <a-radio value="thumbnails">Ảnh thumbnails</a-radio>
                  </a-radio-group>
                </a-form-item>
              </form>
            </a-card>

            <!-- Advanced Configuration Section -->
            <a-card title="Cấu hình nâng cao" bordered style="margin-top: 16px">
              <a-form-item label="Tự động chạy">
                <div class="ms-4 flex items-center">
                  <label class="switch">
                    <input type="checkbox" :checked="true" />
                    <span class="slider"></span>
                  </label>
                  <span class="status-text">Trạng thái <strong>Bật</strong></span>
                </div>
              </a-form-item>

              <a-form-item label="Dừng khi di chuột">
                <div class="ms-4 flex items-center">
                  <label class="switch">
                    <input type="checkbox" :checked="true" />
                    <span class="slider"></span>
                  </label>
                  <span class="status-text">Trạng thái <strong>Bật</strong></span>
                </div>
              </a-form-item>

              <InputComponent
                label="Chuyển cảnh"
                name="transitionSpeed"
                placeholder="Chuyển cảnh"
                suffix="ms"
              />

              <div class="mt-4">
                <InputComponent
                  label="Tốc độ hiệu ứng"
                  name="effectSpeed"
                  placeholder="Tốc độ hiệu ứng"
                  suffix="ms"
                />
              </div>
            </a-card>

            <!-- Shortcode Section -->
            <a-card title="Shortcode" bordered style="margin-top: 16px">
              <InputComponent
                name="shortcode"
                typeInput="textarea"
                placeholder="Shortcode"
                label="Shortcode"
              />
            </a-card>
          </a-col>

          <!-- Right Column (Danh sách slides) -->
          <a-col :span="16">
            <a-card title="Danh sách slides" bordered>
              <template #extra>
                <a-button type="dashed" @click="addSlide">Thêm slide</a-button>
              </template>
              <a-list :data-source="slides" item-layout="horizontal" style="margin-top: 16px">
                <template #item="{ item }">
                  <a-list-item :key="item.id">
                    <a-row>
                      <a-col :span="6">
                        <a-upload listType="picture-card" :file-list="[]">
                          <div>
                            <a-icon type="plus" />
                            <div style="margin-top: 8px">Upload</div>
                          </div>
                        </a-upload>
                      </a-col>
                      <a-col :span="18">
                        <a-form layout="vertical">
                          <a-form-item label="Mô tả">
                            <a-input v-model="item.description" placeholder="Mô tả" />
                          </a-form-item>
                          <a-form-item label="URL">
                            <a-input v-model="item.url" placeholder="url" />
                          </a-form-item>
                        </a-form>
                      </a-col>
                    </a-row>
                  </a-list-item>
                </template>
              </a-list>
              
            </a-card>
          </a-col>
        </a-row>
      </div>
    </template>
  </MasterLayout>
</template>

<script setup>
import { ref } from 'vue';
import { MasterLayout, InputComponent } from '@/components/backend';
import { EFFECT } from '@/static/constants';

const slideConfig = ref({
  name: '',
  keyword: '',
  width: '1000',
  height: '2000',
  effect: 'Coverflow',
  showArrow: false,
  navigation: 'dots',
  autoPlay: false,
  pauseOnHover: true,
  transitionSpeed: '1000',
  effectSpeed: '1000',
  shortcode: ''
});

const effectOptions = ref([{ label: 'Coverflow', value: 'Coverflow' }]);

const slides = ref([
  { id: 1, description: 'Tai nghe chụp tai mới lạ', url: '' },
  { id: 2, description: 'Đồng hồ thông minh mockup', url: '' },
  { id: 3, description: 'Đồng hồ thông minh cảm ứng', url: '' }
]);

const addSlide = () => {
    console.log(slides.value);
    
  slides.value.push({
    id: slides.value.length + 1,
    description: '',
    url: ''
  });
};
</script>

<style scoped>
.content-wrapper {
  padding: 16px;
}

.status-text {
  font-size: 14px; /* Thay đổi kích thước chữ */
  margin-left: 10px; /* Tạo khoảng cách giữa checkbox và text */
}

/* Nếu bạn muốn làm cho chữ nhỏ hơn một chút so với tiêu đề */
.status-text strong {
  font-size: 14px; /* Chữ "Tắt" sẽ nhỏ hơn */
}

.switch label {
  font-weight: bold; /* Đặt chữ đậm */
  font-size: 16px; /* Tùy chỉnh kích thước chữ nếu cần */
}
</style>
