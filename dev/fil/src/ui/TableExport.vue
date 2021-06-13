<template>
  <div class="btn-import">
    <div><span v-if="count !== 0">Всего: {{ count }}</span></div>
    <div class="wrap">
      <ElButton @click="$emit('click')" type="primary" icon="el-icon-download">Экспорт</ElButton>
    </div>
  </div>
</template>

<script>
export default {
  name: "TableExport",
  emits: ['click'],
  props: ['count']
}
</script>

<style scoped>
.btn-import {
  margin: 16px 0;
  display: flex;
  justify-content: space-between;
}

.wrap {
  display: flex;
  flex-direction: column;
}

.link {
  margin-top: 10px;
}
</style>
