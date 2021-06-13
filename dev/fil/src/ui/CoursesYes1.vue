<template>
  <ElSelect
      :model-value="value"
      @change="$emit('update:value', $event)"
      placeholder="Выбрать категорию"
      class="select">
    <ElOption
        v-for="option in options"
        :key="option.value"
        :label="option.label"
        :value="option.value"
    />
  </ElSelect>
  <ElCollapse model-value="1">
    <ElCollapseItem title="Скрыть выбор курсов" name="1">
      <ElCard class="card">
        <ElCheckbox
            :indeterminate="isIndeterminate"
            v-model="checkAll"
            @change="handleCheckAllChange">
          Выбрать все
        </ElCheckbox>
        <ElCheckboxGroup :model-value="selectedCourses" @change="handleCheckCourse">
          <div v-for="course in courses" :key="course.id">
            <ElCheckbox :label="course.id">{{ course.title }}</ElCheckbox>
          </div>
        </ElCheckboxGroup>
      </ElCard>

      <div>
        <ElRadio v-model="datePeriod" label="before">До выбранной даты</ElRadio>
      </div>
      <div>
        <ElRadio v-model="datePeriod" label="after">После выбранной даты</ElRadio>
      </div>
      <ElDatePicker v-model="date"/>
    </ElCollapseItem>
  </ElCollapse>
</template>

<script>
import {ref} from "vue";

export default {
  props: {
    value: String,
    options: Array,
    courses: Array,
    selectedCourses: Array,
  },
  emits: ['update:value', 'update:selectedCourses'],
  setup(props, {emit}) {
    const datePeriod = ref('before');
    const date = ref('');
    const checkAll = ref(false);
    const isIndeterminate = ref(false);
    const handleCheckAllChange = (val) => {
      emit('update:selectedCourses', val ? props.courses.map(course => course.id) : []);
      isIndeterminate.value = false;
    }
    const handleCheckCourse = (val) => {
      emit('update:selectedCourses', val);
      let checkedCount = val.length;
      checkAll.value = checkedCount === props.courses.length;
      isIndeterminate.value = checkedCount > 0 && checkedCount < props.courses.length;
    }
    return {checkAll, handleCheckAllChange, handleCheckCourse, isIndeterminate, date, datePeriod}
  }
}
</script>

<style scoped>

</style>
