<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Toaster } from '@/components/ui/sonner';
import { toast } from 'vue-sonner';
const form = useForm({
  email: null,
  password: null,
  remember: false,
})
const submit = () => {
  form.post(route('project.store'), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Report has been updated successfully!', { style: { background: '#6ee7b7', color: '#000' } });
    },
  });
};
</script>
<template>
  <div>
    <Head title="New project" />
    <form @submit.prevent="submit" class="space-y-6">
      <div class="flex w-full justify-end">
        <Button :disabled="form.processing">Save</Button>
      </div>
    </form>
  </div>
  <Toaster />
</template>
