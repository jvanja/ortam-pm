<template>
  <input class="border p-2 rounded-lg" ref="inputRef" type="text" />
</template>

<script setup lang="ts">
import { useCurrencyInput } from 'vue-currency-input';

const props = defineProps({
  modelValue: Number,
  options: Object,
});
const { inputRef } = useCurrencyInput({ ...props.options });
</script>
<style lang="css" scoped>
</style>
