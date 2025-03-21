<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Toaster } from '@/components/ui/sonner';
import { toast } from 'vue-sonner';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';

const form = useForm({
  project_type: '',
  department: '',
  project_manager: '',
  project_language: '',
  project_address: '',
  project_status: '',
  project_opening_date: '',
  budget: 0,
  sales_representative_name: '',
  deadline: '',
  client_id: null,
});

const submit = () => {
  form.post(route('projects.store'), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Project created successfully!', {
        style: { background: '#6ee7b7', color: '#000' }
      });
    },
  });
};
</script>
<template>
  <div>
    <Head title="New project" />
    <form @submit.prevent="submit" class="space-y-6">
      <div class="grid grid-cols-2 gap-6">
        <div class="space-y-2">
          <Label for="project_type">Project Type</Label>
          <Input v-model="form.project_type" id="project_type" placeholder="Enter project type" />
          <div class="text-sm text-red-500" v-if="form.errors.project_type">{{ form.errors.project_type }}</div>
        </div>

        <div class="space-y-2">
          <Label for="department">Department</Label>
          <Input v-model="form.department" id="department" placeholder="Enter department" />
          <div class="text-sm text-red-500" v-if="form.errors.department">{{ form.errors.department }}</div>
        </div>

        <div class="space-y-2">
          <Label for="project_manager">Project Manager</Label>
          <Input v-model="form.project_manager" id="project_manager" placeholder="Enter project manager" />
          <div class="text-sm text-red-500" v-if="form.errors.project_manager">{{ form.errors.project_manager }}</div>
        </div>

        <div class="space-y-2">
          <Label for="project_language">Project Language</Label>
          <Input v-model="form.project_language" id="project_language" placeholder="Enter project language" />
          <div class="text-sm text-red-500" v-if="form.errors.project_language">{{ form.errors.project_language }}</div>
        </div>

        <div class="space-y-2">
          <Label for="project_address">Project Address</Label>
          <Input v-model="form.project_address" id="project_address" placeholder="Enter project address" />
          <div class="text-sm text-red-500" v-if="form.errors.project_address">{{ form.errors.project_address }}</div>
        </div>

        <div class="space-y-2">
          <Label for="project_status">Project Status</Label>
          <Input v-model="form.project_status" id="project_status" placeholder="Enter project status" />
          <div class="text-sm text-red-500" v-if="form.errors.project_status">{{ form.errors.project_status }}</div>
        </div>

        <div class="space-y-2">
          <Label for="project_opening_date">Opening Date</Label>
          <Input v-model="form.project_opening_date" type="date" id="project_opening_date" />
          <div class="text-sm text-red-500" v-if="form.errors.project_opening_date">{{ form.errors.project_opening_date }}</div>
        </div>

        <div class="space-y-2">
          <Label for="budget">Budget</Label>
          <Input v-model="form.budget" type="number" id="budget" placeholder="Enter budget" />
          <div class="text-sm text-red-500" v-if="form.errors.budget">{{ form.errors.budget }}</div>
        </div>

        <div class="space-y-2">
          <Label for="sales_representative_name">Sales Representative</Label>
          <Input v-model="form.sales_representative_name" id="sales_representative_name" placeholder="Enter sales representative name" />
          <div class="text-sm text-red-500" v-if="form.errors.sales_representative_name">{{ form.errors.sales_representative_name }}</div>
        </div>

        <div class="space-y-2">
          <Label for="deadline">Deadline</Label>
          <Input v-model="form.deadline" type="date" id="deadline" />
          <div class="text-sm text-red-500" v-if="form.errors.deadline">{{ form.errors.deadline }}</div>
        </div>

        <div class="space-y-2">
          <Label for="client_id">Client ID</Label>
          <Input v-model="form.client_id" type="number" id="client_id" placeholder="Enter client ID" />
          <div class="text-sm text-red-500" v-if="form.errors.client_id">{{ form.errors.client_id }}</div>
        </div>
      </div>

      <div class="flex w-full justify-end">
        <Button type="submit" :disabled="form.processing">Save</Button>
      </div>
    </form>
  </div>
  <Toaster />
</template>
