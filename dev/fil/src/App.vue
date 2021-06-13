<template>
  <div class="container">
    <CoursesFilter
        v-model:selectedCoursesYes="selectedCoursesYes"
        v-model:selectedCoursesNo="selectedCoursesNo"
        v-model:categoryYes="categoryYes"
        v-model:categoryNo="categoryNo"
        @change="tableData = []"
        v-model:date="date"
        v-model:datePeriod="datePeriod"
        @update:datePeriod="aaa"
        v-model:wpmLevel="wpmLevel"
    />
    <ElButton type="success" class="start" @click="start = true">Сделать выборку</ElButton>
    <TableExport @click="exportExcel" :count="count"/>
    <UsersTable
        :table-data="tableData"
        :loading="loading"
        v-model:multipleSelection="multipleSelection"/>
  </div>
</template>

<script>
import {computed, ref, watch} from "vue";
import CoursesFilter from "./ui/CoursesFilter.vue";
import TableExport from "./ui/TableExport.vue";
import UsersTable from "./features/table/UsersTable.vue";
import getUsers from "./api/getUsers";
import getFile from "./api/getFile";

export default {
  components: {UsersTable, TableExport, CoursesFilter},
  setup() {
    let start = ref(false);
    let selectedCoursesYes = ref([]);
    let selectedCoursesNo = ref([]);
    let categoryYes = ref("");
    let categoryNo = ref("");

    let tableData = ref([]);
    let loading = ref(false);
    let multipleSelection = ref([])

    let date = ref('')
    let datePeriod = ref('before')
    let wpmLevel = ref('date_start')

    let count = computed(() => tableData.value.length);

    const exportExcel = () => {
      let selectedUserIds = Object.values(multipleSelection.value).map(proxy => proxy.ID);
      if (selectedUserIds.length) {
        getFile(selectedUserIds);
      } else {
        window.alert('Выберите пользователей')
      }
    }
    watch(start, async (next) => {
      if (next) {
        loading.value = true;
        tableData.value = await getUsers(
            {
              catId: categoryYes.value,
              ids: Object.values(selectedCoursesYes.value)
            },
            {
              catId: categoryNo.value,
              ids: Object.values(selectedCoursesNo.value)
            },
            date.value,
            datePeriod.value,
            wpmLevel.value
        );
        loading.value = false;
        start.value = false;
      } else {

      }
    })

    const aaa = (e) => console.log(e)
    return {
      start,
      selectedCoursesYes,
      categoryYes,
      selectedCoursesNo,
      categoryNo,
      loading,
      tableData,
      multipleSelection,
      exportExcel,
      count,
      date,
      datePeriod,
      aaa,
      wpmLevel
    };
  }
}
</script>

<style>

.container {
  max-width: 1170px;
  margin: 0 auto;
  padding-bottom: 80px;
}

.start {
  margin-top: 20px !important;
  margin-bottom: 20px !important;
}
</style>
