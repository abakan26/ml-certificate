<template>
  <ElTable
      border
      v-loading="loading"
      ref="multipleTable"
      :data="tableData"
      style="width: 100%"
      @selection-change="handleSelectionChange">
    <ElTableColumn
        type="selection"
        width="55">
    </ElTableColumn>
    <ElTableColumn
        label="ID"
        property="ID"
        width="90">
    </ElTableColumn>
    <ElTableColumn label="Логин">
      <template #default="scope">
        <ElLink target="_blank" :href="scope.row.userLink">{{ scope.row.userName }}</ElLink>
      </template>
    </ElTableColumn>
    <ElTableColumn
        property="lastName"
        label="Фамилия"
        show-overflow-tooltip>
    </ElTableColumn>
    <ElTableColumn
        property="firstName"
        label="Имя"
        show-overflow-tooltip>
    </ElTableColumn>
    <ElTableColumn
        property="surname"
        label="Отчество"
        show-overflow-tooltip>
    </ElTableColumn>
    <ElTableColumn
        property="email"
        label="Email"
        show-overflow-tooltip>
    </ElTableColumn>
  </ElTable>
</template>

<script>
import {ref} from "vue";

export default {
  props: ['loading', 'tableData', 'multipleSelection'],
  emits: ['update:multipleSelection'],
  setup(props, {emit}) {
    let multipleTable = ref(null);
    function handleSelectionChange(val) {
      emit('update:multipleSelection', val)
    }

    return {multipleTable, handleSelectionChange}
  }
}
</script>

<style scoped>

</style>
