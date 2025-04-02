<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type PCA_Report } from '@/types';
import { reactive, watch } from 'vue';

const props = defineProps<{ form: PCA_Report }>();

const emit = defineEmits(['update:form']);

const localForm = reactive({ ...props.form });

// Watch for changes and emit updates
watch(localForm, (newVal) => {
    emit('update:form', { ...newVal });
  },
  { deep: true }
);
</script>

<template>
  <div>
    <Label htmlFor="name">Name of the report</Label>
    <Input id="name" v-model="localForm.name"></Input>
  </div>
</template>
