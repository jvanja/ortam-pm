<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type p_c_a_reportsEntity } from '@/types/DatabaseModels';
import { reactive, watch } from 'vue';

const props = defineProps<{ form: p_c_a_reportsEntity; }>();

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
    <Input v-model="localForm.name"></Input>
  </div>
</template>
