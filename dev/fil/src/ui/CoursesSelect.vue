<template>
  <ElCheckbox
      :indeterminate="isIndeterminate"
      v-model="checkAll"
      @change="handleCheckAllChange">
    Выбрать все
  </ElCheckbox>
  <ElCheckboxGroup :model-value="selectedCourses" @change="handleCheckCourse">
    <div v-for="course in allCourses" :key="course.id">
      <ElCheckbox :label="course.id">{{ course.title }}</ElCheckbox>
    </div>
  </ElCheckboxGroup>
</template>

<script>
import {ref} from "vue";

export default {
  name: "CoursesSelect",
  props: ['selectedCourses', 'allCourses'],
  emits: ['change'],
  setup(props, {emit}) {
    const checkAll = ref(false);
    const isIndeterminate = ref(false);
    const handleCheckAllChange = (val) => {
      emit('change', val ? props.allCourses.map(course => course.id) : []);
      isIndeterminate.value = false;
    }
    const handleCheckCourse = (val) => {
      emit('change', val);
      let checkedCount = val.length;
      checkAll.value = checkedCount === props.allCourses.length;
      isIndeterminate.value = checkedCount > 0 && checkedCount < props.allCourses.length;
    }
    return {isIndeterminate, checkAll, handleCheckAllChange, handleCheckCourse}
  }
}
</script>

<style scoped>

</style>
