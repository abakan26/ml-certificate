<template>
  <ElSelect
      :model-value="selectedCategory"
      @change="$emit('change', $event)"
      placeholder="Выбрать категорию"
      class="select">
    <ElOption
        v-for="option in allCategories"
        :key="option.value"
        :label="option.label"
        :value="option.value"
    />
  </ElSelect>
</template>

<script>
export default {
  name: "CategorySelect",
  props: ['allCategories', 'selectedCategory']
}
</script>

<style scoped>
.select {
  margin-bottom: 16px;
}
</style>
