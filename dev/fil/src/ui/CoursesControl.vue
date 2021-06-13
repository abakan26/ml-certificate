<template>
  <div ref="dom" class="e-loading">
    <ElCollapse model-value="1" v-show="allCourses.length !== 0">
      <ElCollapseItem title="Скрыть выбор курсов" name="1">

        <ElCard class="card">
          <CoursesSelect
              :allCourses="allCourses"
              :selectedCourses="selectedCourses"
              @change="$emit('update:selectedCourses', $event)"
          />
        </ElCard>

        <slot name="date"></slot>
      </ElCollapseItem>
    </ElCollapse>
  </div>
</template>

<script>
import CoursesSelect from "./CoursesSelect.vue";
import {ElLoading} from "element-plus";
import {onMounted, onUpdated, reactive, ref} from "vue";

export default {
  name: "CoursesControl",
  components: {CoursesSelect},
  props: {
    selectedCourses: Array,
    allCourses: Array,
    loading: Boolean,
  },
  emits: ['update:selectedCourses'],
  setup(props) {
    let dom = ref(null);
    let loadingInstance = ref(null)
    onMounted(() => {
      if (!props.loading) {
        // if (!loadingInstance.value) return;
        // loadingInstance.value.close()
        return;
      }

      loadingInstance.value = ElLoading.service({
        target: dom.value,
        spinner: 'el-icon-loading',
        fullscreen: false
      });
    });
    onUpdated(() => {
      if (!props.loading) {
        if (!loadingInstance.value) return;
        loadingInstance.value.close()
        return;
      }
      if (!dom.value) return;
      loadingInstance.value = ElLoading.service({
        target: dom.value,
        spinner: 'el-icon-loading',
        fullscreen: false
      });
    })
    return {dom}
  }
}
</script>

<style scoped>
.card {
  margin-bottom: 16px;
  max-height: 320px;
  overflow-y: auto;
}
.e-loading {
  min-height: 50px;
}
</style>
