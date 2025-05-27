<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ProjectPipeline, ProjectDetailsCard, ProjectEmployeesCard, ProjectNotes, ProjectFiles } from '@/components/project';
import InvoicesList from '@/components/invoice/List.vue';
import type { BreadcrumbItem, Client, Project, ProjectPipelineStage, User, Invoice, ProjectFile } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

const props = defineProps<{
  project: Project & { files: ProjectFile[] };
  client: Client;
  employees: [User & { roles: [{ id: string; name: string }] }];
  pipelineStages: ProjectPipelineStage[];
  currentPipelineStage: ProjectPipelineStage | null;
  invoices: Invoice[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Projects', href: '/projects' },
  { title: props.project.type!, href: '' },
];

// set hash to tab value
const tabChange = (tab: string | number) => {
  window.location.hash = String(tab);
}

const defaultTab = window.location.hash.slice(1) || 'pipeline';

</script>
<template>
  <Head title="Project" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-col rounded-xl p-4">

      <Tabs :default-value="defaultTab" class="w-full h-full flex flex-col" @update:model-value="tabChange">
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
           <TabsTrigger value="notes" class="relative h-9 rounded-none border-b-2 border-b-transparent bg-transparent px-4 pb-3 pt-2 font-semibold text-muted-foreground shadow-none transition-none data-[state=active]:border-b-primary data-[state=active]:text-foreground data-[state=active]:shadow-none">
            Notes
          </TabsTrigger>
           <TabsTrigger value="files" class="relative h-9 rounded-none border-b-2 border-b-transparent bg-transparent px-4 pb-3 pt-2 font-semibold text-muted-foreground shadow-none transition-none data-[state=active]:border-b-primary data-[state=active]:text-foreground data-[state=active]:shadow-none">
            Files
          </TabsTrigger>
        </TabsList>

        <!-- Tab Content -->
        <div class="flex-1 flex flex-col gap-4 mt-4">
          <TabsContent value="pipeline" class="flex-1">
            <ProjectPipeline :project :pipelineStages :currentPipelineStage />
          </TabsContent>
          <TabsContent value="details" class="flex-1">
            <ProjectDetailsCard :project :client :employees />
          </TabsContent>
          <TabsContent value="employees" class="flex-1">
            <ProjectEmployeesCard :projectId="project.id" :employees />
          </TabsContent>
           <TabsContent value="invoices" class="flex-1">
            <InvoicesList :invoices :currency="project.currency || 'USD'" :client-id="client.id" :project-id="project.id" />
          </TabsContent>
           <TabsContent value="notes" class="flex-1">
            <ProjectNotes :project />
          </TabsContent>
           <TabsContent value="files" class="flex-1">
            <ProjectFiles :project />
          </TabsContent>
        </div>
      </Tabs>

    </div>
  </AppLayout>
</template>
