<template>
  <div>
    <el-radio-group :model-value="datePeriod" @change="$emit('update:datePeriod', $event)">
      <div class="radio">
        <ElRadio label="before">До выбранной даты</ElRadio>
      </div>
      <div class="radio">
        <ElRadio label="after">После выбранной даты</ElRadio>
      </div>
    </el-radio-group>
    <el-radio-group style="margin-left: 15px" :model-value="wpmLevel" @change="$emit('update:wpmLevel', $event)">
      <div class="radio">
        <ElRadio label="date_start">Дата начала доступа</ElRadio>
      </div>
      <div class="radio">
        <ElRadio label="date_end">Дата окончания доступа </ElRadio>
      </div>
    </el-radio-group>
  </div>
  <div class="date">
    <ElDatePicker format="YYYY-MM-DD" :model-value="date" @change="$emit('update:date', $event)"/>
  </div>
</template>

<script>
import { ref, watch } from "vue";

export default {
  name: "DateSelect",
  props: ['date', 'datePeriod', 'wpmLevel'],
  emits: ['update:date', 'update:datePeriod', 'update:wpmLevel'],
  // setup(props, {emit}) {
  //   watch(() => props.date, () => {
  //
  //   })
  //   watch(() => props.datePeriod, () => {
  //
  //   })
  // }
}
</script>

<style scoped>
.date {
  margin-top: 20px;
}
.radio {
  margin-bottom: 5px;
}
</style>
