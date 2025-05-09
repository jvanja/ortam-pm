<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ProjectPipeline, ProjectDetailsCard, ProjectEmployeesCard } from '@/components/project';
import type { BreadcrumbItem, Client, Project, ProjectPipelineStage, User } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const props = defineProps<{
  project: Project;
  client: Client;
  employees: User[];
  pipelineStages: ProjectPipelineStage[];
  currentPipelineStage: ProjectPipelineStage | null;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Projects', href: '/projects' },
  { title: props.project.type!, href: '' },
];

// Keep the form logic and functions in the parent component
const form = useForm({ ...props.project });

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

const employeesIds = props.employees.map((employee) => employee.id);
const projectEmployeeForm = useForm({ employeesIds });
const removeEmployee = (id: string) => {
  projectEmployeeForm.delete(route('projects.employee.remove', [props.project, id]), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Employee has been removed successfully!', { style: { background: '#6ee7b7', color: '#000' } });
    },
    onError: (errors) => {
      console.error('Remove employee error:', errors);
      toast.error('Failed to remove employee.');
    },
  });
};

</script>
<template>
  <Head title="Project" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <!-- Pipeline Stages Section -->
      <ProjectPipeline :project="project" :pipelineStages="pipelineStages" :currentPipelineStage />

      <!-- Project Details Section (Moved to Component) -->
      <ProjectDetailsCard :project="project" :client="client" :form="form" :submit="submit" />

      <!-- Employees Section (Moved to Component) -->
      <ProjectEmployeesCard :employees="employees" :projectEmployeeForm="projectEmployeeForm" :removeEmployee="removeEmployee" />
    </div>
  </AppLayout>
</template>
