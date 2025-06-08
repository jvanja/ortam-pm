<template>
  <input class="border p-2 rounded-lg" ref="inputRef" type="text" />
</template>

<script setup lang="ts">
import { useCurrencyInput, CurrencyInputOptions } from 'vue-currency-input';

const props = defineProps<{
  modelValue: number,
  options: CurrencyInputOptions,
}>();
const { inputRef } = useCurrencyInput({ ...props.options });
</script>
