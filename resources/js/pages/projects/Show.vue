<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ProjectPipeline, ProjectDetailsCard, ProjectEmployeesCard, ProjectInvoicesCard } from '@/components/project';
import type { BreadcrumbItem, Client, Project, ProjectPipelineStage, User, Invoice } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

const props = defineProps<{
  project: Project;
  client: Client;
  employees: User[];
  pipelineStages: ProjectPipelineStage[];
  currentPipelineStage: ProjectPipelineStage | null;
  invoices: Invoice[];
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
    <div class="flex h-full flex-col rounded-xl p-4">

      <Tabs default-value="pipeline" class="w-full h-full flex flex-col">
        <!-- Tab Navigation List -->
        <TabsList class="justify-start rounded-none bg-transparent gap-4"> <!-- Updated grid-cols to 4 -->
          <TabsTrigger value="pipeline" class="relative h-9 rounded-none border-b-2 border-b-transparent bg-transparent px-4 pb-3 pt-2 font-semibold text-muted-foreground shadow-none transition-none data-[state=active]:border-b-primary data-[state=active]:text-foreground data-[state=active]:shadow-none">
            Pipeline
          </TabsTrigger>
          <TabsTrigger value="details" class="relative h-9 rounded-none border-b-2 border-b-transparent bg-transparent px-4 pb-3 pt-2 font-semibold text-muted-foreground shadow-none transition-none data-[state=active]:border-b-primary data-[state=active]:text-foreground data-[state=active]:shadow-none">
            Details
          </TabsTrigger>
          <TabsTrigger value="employees" class="relative h-9 rounded-none border-b-2 border-b-transparent bg-transparent px-4 pb-3 pt-2 font-semibold text-muted-foreground shadow-none transition-none data-[state=active]:border-b-primary data-[state=active]:text-foreground data-[state=active]:shadow-none">
            Employees
          </TabsTrigger>
           <TabsTrigger value="invoices" class="relative h-9 rounded-none border-b-2 border-b-transparent bg-transparent px-4 pb-3 pt-2 font-semibold text-muted-foreground shadow-none transition-none data-[state=active]:border-b-primary data-[state=active]:text-foreground data-[state=active]:shadow-none">
            Invoices
          </TabsTrigger>
        </TabsList>

        <!-- Tab Content -->
        <div class="flex-1 flex flex-col gap-4 mt-4">
          <TabsContent value="pipeline" class="flex-1">
            <ProjectPipeline :project="project" :pipelineStages="pipelineStages" :currentPipelineStage />
          </TabsContent>
          <TabsContent value="details" class="flex-1">
            <ProjectDetailsCard :project="project" :client="client" :form="form" :submit="submit" />
          </TabsContent>
          <TabsContent value="employees" class="flex-1">
            <ProjectEmployeesCard :employees="employees" :projectEmployeeForm="projectEmployeeForm" :removeEmployee="removeEmployee" />
          </TabsContent>
           <TabsContent value="invoices" class="flex-1">
            <ProjectInvoicesCard :invoices="invoices" />
          </TabsContent>
        </div>
      </Tabs>

    </div>
  </AppLayout>
</template>
