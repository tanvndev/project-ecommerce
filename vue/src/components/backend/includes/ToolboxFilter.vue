<template>
  <div class="relative">
    <div
      class="toolbox-filter"
      :class="{ active: openDatePicker }"
      @click="openDatePicker = !openDatePicker"
    >
      <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
      <span>{{ dateText }}</span>
      <i class="fal fa-angle-down ml-2 pr-1 text-gray-400" v-if="!openDatePicker"></i>
      <i class="fal fa-angle-up ml-2 pr-1 text-gray-400" v-else></i>
    </div>

    <div class="list-box-filter" v-if="openDatePicker">
      <div>
        <div class="flex flex-col items-center gap-2">
          <div
            class="item-box-filter"
            v-for="(filterDateOption, index) in filterDateOptions"
            :key="index"
          >
            <button
              class="button-filter"
              :class="{ active: optionActive == item.active }"
              v-for="item in filterDateOption"
              :key="item.value"
              @click="handleChangeDate(item)"
            >
              <span>{{ item.label }}</span>
            </button>
          </div>
          <div class="item-box-filter item-box-filter-custom">
            <button
              class="button-filter"
              :class="{ active: optionActive == 'custom' }"
              @click="handleChangeDate({ active: 'custom', value: '', label: 'Tuỳ chọn' })"
            >
              <span>Tùy chọn</span>
            </button>
            <div v-if="optionActive == 'custom'">
              <a-range-picker v-model:value="dateCustomRange" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { debounce } from '@/utils/helpers';
import dayjs from 'dayjs';
import isoWeek from 'dayjs/plugin/isoWeek';
import isSameOrAfter from 'dayjs/plugin/isSameOrAfter';
import isSameOrBefore from 'dayjs/plugin/isSameOrBefore';
import timezone from 'dayjs/plugin/timezone';
import utc from 'dayjs/plugin/utc';
import _ from 'lodash';
import { onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useStore } from 'vuex';

dayjs.extend(utc);
dayjs.extend(timezone);
dayjs.extend(isSameOrBefore);
dayjs.extend(isSameOrAfter);
dayjs.extend(isoWeek);

const store = useStore();
const router = useRouter();
const route = useRoute();
const today = dayjs().tz('Asia/Ho_Chi_Minh');
const query = route.query;
const dateCustomRange = ref(null);
const dateText = ref('');
const optionActive = ref(query.date);
const openDatePicker = ref(false);
const emits = defineEmits(['onChangeDate']);

// Tính toán các ngày cần thiết
const yesterday = today.subtract(1, 'day');
const sevenDaysAgo = today.subtract(7, 'days');
const thirtyDaysAgo = today.subtract(30, 'days');
const lastMonth = today.subtract(1, 'month');
const lastYear = today.subtract(1, 'year');

// Hàm định dạng ngày
const formatDate = (date) => date.format('DD/MM/YYYY');

// Tính toán ngày đầu và cuối của tháng
const startOfThisMonth = today.startOf('month');
const endOfThisMonth = today.endOf('month');
const startOfLastMonth = lastMonth.startOf('month');
const endOfLastMonth = lastMonth.endOf('month');

// Tính toán ngày đầu và cuối của năm
const startOfThisYear = today.startOf('year');
const endOfThisYear = today.endOf('year');
const startOfLastYear = lastYear.startOf('year');
const endOfLastYear = lastYear.endOf('year');

// Tính toán ngày đầu và cuối của tuần
const startOfLastWeek = today.subtract(1, 'week').startOf('isoWeek');
const endOfLastWeek = today.subtract(1, 'week').endOf('isoWeek');
const startOfThisWeek = today.startOf('isoWeek');
const endOfThisWeek = today.endOf('isoWeek');

const filterDateOptions = ref([
  [
    {
      label: 'Hôm nay',
      value: formatDate(today),
      active: 'today'
    },
    {
      label: 'Hôm qua',
      value: formatDate(yesterday),
      active: 'yesterday'
    }
  ],
  [
    {
      label: '7 ngày qua',
      value: `${formatDate(sevenDaysAgo)} - ${formatDate(today)}`,
      active: 'last_7_days'
    },
    {
      label: '30 ngày qua',
      value: `${formatDate(thirtyDaysAgo)} - ${formatDate(today)}`,
      active: 'last_30_days'
    }
  ],
  [
    {
      label: 'Tuần trước',
      value: `${formatDate(startOfLastWeek)} - ${formatDate(endOfLastWeek)}`,
      active: 'last_week'
    },
    {
      label: 'Tuần này',
      value: `${formatDate(startOfThisWeek)} - ${formatDate(endOfThisWeek)}`,
      active: 'this_week'
    }
  ],
  [
    {
      label: 'Tháng trước',
      value: `${formatDate(startOfLastMonth)} - ${formatDate(endOfLastMonth)}`,
      active: 'last_month'
    },
    {
      label: 'Tháng này',
      value: `${formatDate(startOfThisMonth)} - ${formatDate(endOfThisMonth)}`,
      active: 'this_month'
    }
  ],
  [
    {
      label: 'Năm trước',
      value: `${formatDate(startOfLastYear)} - ${formatDate(endOfLastYear)}`,
      active: 'last_year'
    },
    {
      label: 'Năm này',
      value: `${formatDate(startOfThisYear)} - ${formatDate(endOfThisYear)}`,
      active: 'this_year'
    }
  ]
]);

const handleChangeDate = ({ active, value, label }) => {
  if (active !== 'custom') {
    delete query.start_date;
    delete query.end_date;
  }
  optionActive.value = active;
  dateText.value = `${label} (${value})`;

  router.push({
    query: {
      ...query,
      date: active
    }
  });
};

const handleFilter = () => {
  const filterOption = filterDateOptions.value
    .flat()
    .find((option) => option.active === optionActive.value);

  let data;

  if (optionActive.value === 'custom' && !_.isEmpty(dateCustomRange.value)) {
    const startDate = dateCustomRange.value[0] || null;
    const endDate = dateCustomRange.value[1] || null;

    data = {
      active: 'custom',
      label: 'Tuỳ chọn',
      value:
        startDate && endDate
          ? `${formatDate(startDate)} - ${formatDate(endDate)}`
          : 'Không xác định'
    };
  } else {
    data = filterOption;
  }
  if (!_.isEmpty(data) && data.value) {
    const allDay = getAllDates(data.value);
    emits('onChangeDate', { data, allDay });
    store.commit('reportStore/setAllDayFormat', allDay);
  }
};

const debounceHandleFilter = debounce(handleFilter, 500);

watch(
  () => [optionActive, dateCustomRange],
  () => {
    debounceHandleFilter();
  },
  { immediate: true, deep: true }
);

watch(
  dateCustomRange,
  (newValue) => {
    if (!newValue) return;
    const startDate = formatDate(newValue[0]);
    const endDate = formatDate(newValue[1]);

    dateText.value = `Tùy chọn (${startDate} - ${endDate})`;

    router.push({
      query: {
        ...query,
        date: 'custom',
        start_date: startDate,
        end_date: endDate
      }
    });
  },
  { immediate: true }
);

const getAllDates = (range) => {
  let start, end;

  if (range.includes(' - ')) {
    const [startDate, endDate] = range.split(' - ').map((date) => date.trim());
    start = dayjs(startDate, 'DD/MM/YYYY');
    end = dayjs(endDate, 'DD/MM/YYYY');
  } else {
    start = dayjs(range.trim(), 'DD/MM/YYYY');
    end = start;
  }

  const dates = [];

  for (let date = start; date.isSameOrBefore(end); date = date.add(1, 'day')) {
    dates.push(date.format('DD/MM'));
  }

  return dates;
};

onMounted(() => {
  const { date, start_date, end_date } = query;
  optionActive.value = date || 'last_7_days';

  const filterOption = filterDateOptions.value
    .flat()
    .find((option) => option.active === optionActive.value);

  router.push({
    query: {
      ...query,
      date: optionActive.value
    }
  });

  if (date === 'custom') {
    dateText.value = `Tuỳ chọn (${start_date} - ${end_date})`;
  } else if (filterOption) {
    dateText.value = `${filterOption.label} (${filterOption.value})`;
  } else {
    handleChangeDate({
      label: '30 ngày qua',
      value: `${formatDate(thirtyDaysAgo)} - ${formatDate(today)}`,
      active: 'last_7_days'
    });
  }

  const data =
    date === 'custom'
      ? {
          active: 'custom',
          label: 'Tuỳ chọn',
          value: `${start_date} - ${end_date}`
        }
      : filterOption;

  emits('onChangeDate', { data, allDay: getAllDates(data.value) });
});
</script>
<style scoped>
.toolbox-filter {
  border-radius: 4px;
  outline: 1px solid #d3d5d7;
  background: rgb(255, 255, 255);
  display: inline-flex;
  -webkit-box-pack: justify;
  padding: 7px 8px 7px 12px;
  min-height: 36px;
  font-size: 14px;
  -webkit-box-align: center;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.15s ease-in-out;
  user-select: none;
}
.toolbox-filter.active {
  outline-color: rgb(0, 136, 255);
  color: rgb(0, 136, 255);
}
.toolbox-filter.active i {
  color: rgb(0, 136, 255);
}
.toolbox-filter:hover {
  background: rgb(242, 249, 255);
}
.list-box-filter {
  position: absolute;
  z-index: 10;
  margin-top: 5px;
  width: 320px;
  padding: 16px;
  background: rgb(255, 255, 255);
  border: 1px solid rgb(211, 213, 215);
  border-radius: 6px;
  max-height: 725px;
  max-width: 500px;
  box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
}
.button-filter {
  cursor: pointer;
  position: relative;
  -webkit-box-align: center;
  align-items: center;
  -webkit-box-pack: center;
  justify-content: center;
  margin: 0px;
  user-select: none;
  text-decoration: none;
  text-align: center;
  border-radius: 6px;
  font-size: 14px;
  display: flex;
  width: 100%;
  min-width: 36px;
  min-height: 36px;
  padding: 4px 16px;
  border: 1px solid rgb(211, 213, 215);
  background: rgb(255, 255, 255);
  color: rgb(15, 24, 36);
}
.item-box-filter {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 8px;
}
.item-box-filter-custom {
  display: block;
}
.item-box-filter-custom .button-filter {
  margin-bottom: 8px;
}
.item-box-filter .button-filter:hover {
  background: rgb(242, 249, 255);
}
.item-box-filter .button-filter.active {
  border-color: rgb(0, 136, 255);
  color: rgb(0, 136, 255);
}
</style>
