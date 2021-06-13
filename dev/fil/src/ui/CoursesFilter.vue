<template>
  <ElRow :gutter="30">
    <ElCol :span="12">
      <CategorySelect
          :allCategories="allCategories"
          :selectedCategory="categoryYes"
          @change="$emit('update:categoryYes', $event)"
      />
      <CoursesControl
          v-model:selectedCourses="selectedCoursesYes"
          @update:selectedCourses="$emit('update:selectedCoursesYes', $event)"
          :allCourses="allCoursesYes"
          :loading="loadingCoursesYes"
      >

        <template v-slot:date>
          <DateSelect
            :date="date"
            @update:date="$emit('update:date', $event)"
            :datePeriod="datePeriod"
            @update:datePeriod="$emit('update:datePeriod', $event)"
            :wpmLevel="wpmLevel"
            @update:wpmLevel="$emit('update:wpmLevel', $event)"
          />
        </template>
      </CoursesControl>
    </ElCol>
    <ElCol :span="12">
      <CategorySelect
          :allCategories="allCategories"
          :selectedCategory="categoryNo"
          @change="$emit('update:categoryNo', $event)"
      />
      <CoursesControl
          v-model:selectedCourses="selectedCoursesNo"
          @update:selectedCourses="$emit('update:selectedCoursesNo', $event)"
          :allCourses="allCoursesNo"
          :loading="loadingCoursesNo"
      />
    </ElCol>
  </ElRow>
</template>

<script>
import {onMounted, ref, watch} from "vue";
import getProductCategory from "../api/getProductCategory";
import getCourses from "../api/getCourses";
import CoursesControl from "./CoursesControl.vue";
import CoursesNo from "./CoursesNo.vue";
import DateSelect from "./DateSelect.vue";
import CategorySelect from "./CategorySelect.vue";

export default {
  name: "CoursesFilter",
  components: {DateSelect, CoursesNo, CoursesControl, CategorySelect},
  props: ['selectedCoursesYes', 'categoryYes', 'selectedCoursesNo', 'categoryNo', 'date', 'datePeriod', 'wpmLevel'],
  emits: [
    'update:selectedCoursesYes',
    'update:categoryYes',
    'update:selectedCoursesNo',
    'update:categoryNo',
    'change',
    'update:date',
    'update:datePeriod',
    'update:wpmLevel',
  ],
  setup: function (props, {emit}) {
    let allCategories = ref([]);
    let allCoursesYes = ref([]);
    let allCoursesNo = ref([]);

    let loadingCoursesYes = ref(false);
    let loadingCoursesNo = ref(false);

    const setCoursesYes = async (catId) => {
      loadingCoursesYes.value = true
      allCoursesYes.value = await getCourses(catId);
      loadingCoursesYes.value = false
      emit('change');
    }
    const setCoursesNo = async (catId) => {
      loadingCoursesNo.value = true
      allCoursesNo.value = await getCourses(catId);
      loadingCoursesNo.value = false
      emit('change');
    }

    onMounted(async () => {
      allCategories.value = await getProductCategory();
    });
    watch(() => props.categoryYes, (selectedCategoryYes, prevSelectedCategoryYes) => {
      emit('update:selectedCoursesYes', [])
      setCoursesYes(selectedCategoryYes);
    })
    watch(() => props.categoryNo, (selectedCategoryNo, prevSelectedCategoryNo) => {
      emit('update:selectedCoursesNo', [])
      setCoursesNo(selectedCategoryNo);
    })
    return {
      allCategories,
      allCoursesYes,
      allCoursesNo,
      loadingCoursesYes,
      loadingCoursesNo,
    };
  }
}

</script>

<style scoped>

</style>
