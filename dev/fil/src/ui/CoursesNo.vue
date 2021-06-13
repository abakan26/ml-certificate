<template>
  <ElSelect v-model="value" placeholder="Выбрать категорию" class="select">
    <ElOption
        v-for="option in options"
        :key="option.value"
        :label="option.label"
        :value="option.value"
    />
  </ElSelect>
  <ElCollapse model-value="0">
    <ElCollapseItem title="Скрыть выбор курсов" name="1">
      <ElCard class="card">
        <div>
          <ElCheckbox>Выбрать все</ElCheckbox>
        </div>
        <div>
          <ElCheckbox>Курс 1</ElCheckbox>
        </div>
        <div>
          <ElCheckbox>Курс 2</ElCheckbox>
        </div>
        <div>
          <ElCheckbox>Курс 3</ElCheckbox>
        </div>
        <div>
          <ElCheckbox>Курс 4</ElCheckbox>
        </div>
        <div>
          <ElCheckbox>Курс 5</ElCheckbox>
        </div>
      </ElCard>
    </ElCollapseItem>
  </ElCollapse>
</template>

<script setup>

</script>

<style scoped>

</style>
