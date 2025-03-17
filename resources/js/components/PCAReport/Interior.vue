<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { computed } from 'vue';

const props = defineProps<{
  form: {
    name: string;
  };
}>();
const emit = defineEmits<{
  (e: 'update:form', newForm: { name: string }): void;
}>();

const localName = computed({
  get: () => props.form.name,
  set: (newValue: string) => {
    emit('update:form', { ...props.form, name: newValue });
  },
});
</script>
<template>
  <div>
    <Label htmlFor="name">Name of the report</Label>
    <Input v-model="localName" :placeholder="form.name"></Input>
  </div>
</template>
