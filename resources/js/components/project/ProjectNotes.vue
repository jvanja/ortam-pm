<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { Project } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const props = defineProps<{
  project: Project;
}>();

// Keep the form logic and functions in the parent component
const form = useForm({
  notes: props.project.notes || '',
});
const submit = () => {
  form.patch(route('projects.update', [props.project.id]), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Project has been updated successfully!', { style: { background: '#6ee7b7', color: '#000' } });
    },
    onError: (errors) => {
      console.error('Project udpate error:', errors);
      toast.error('Failed to update project. Please check the form for errors.');
    },
  });
};
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>{{ project.type }}</CardTitle>
    </CardHeader>
    <CardContent>
      <form @submit.prevent="submit" class="space-y-6">
        <div class="flex flex-col space-y-1.5">
          <Label htmlFor="type">Project Notes</Label>
          <Textarea id="type" :placeholder="form.notes" v-model="form.notes" class="border-1 w-wull min-h-40 border" />
        </div>
        <div class="flex w-full justify-end">
          <Button :disabled="form.processing">Save</Button>
        </div>
      </form>
    </CardContent>
  </Card>
</template>
