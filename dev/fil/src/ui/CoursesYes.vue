<template>
  <CoursesControl
      :selectedCategory="selectedCategoryYes"
      :allCategories="allCategories"
      :selectedCourses="selectedCoursesYes"
      :allCourses="allCourses"
  />
</template>

<script>

import CoursesControl from "./CoursesControl.vue";

export default {
  components: {CoursesControl},
  props: {
    selectedCategoryYes: String,
    allCategories: Array,
    selectedCoursesYes: Array,
    allCourses: Array,
  },
  setup(props, {emit}) {

  }
}
</script>

<style scoped>

</style>
