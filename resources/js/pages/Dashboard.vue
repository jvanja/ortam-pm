<script setup lang="ts">
import LatestProjectsChart from '@/components/dashboard/LatestProjectsChart.vue';
import Heading from '@/components/Heading.vue';
import NewProject from '@/components/project/NewProject.vue';
import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Project, ProjectPipelineStage } from '@/types';
import { Head } from '@inertiajs/vue3';
import { FolderPlus, UserRoundPlus, ReceiptText } from 'lucide-vue-next';
type ProjectWithPipelineStages = Project & { pipeline_stages: ProjectPipelineStage[]; current_pipeline_stage: ProjectPipelineStage };

defineProps<{ projects: ProjectWithPipelineStages[] }>();
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div v-if="projects.length > 1" class="flex flex-col gap-16 p-8">
      <section>
        <Heading title="Projects" description="These are your latest projects" />
        <LatestProjectsChart :projects="projects" />
      </section>
      <section>
        <Heading title="Actions" description="Set up a new project, client or a report" />
        <div class="grid grid-cols-3 gap-2">
          <Card class="flex flex-col space-y-4 bg-gray-50 p-4 dark:bg-neutral-800 text-sm">
            <ReceiptText color="#2339c3" />
            <a :href="`/proposals/create`" class="hover:text-primary">new proposal ></a>
            <p class="text-muted-foreground">Create a new proposal</p>
          </Card>
          <Card class="flex flex-col space-y-4 bg-gray-50 p-4 dark:bg-neutral-800 text-sm">
            <FolderPlus color="#3e9392" />
            <a :href="`/projects/create`" class="hover:text-primary">new project ></a>
            <p class="text-muted-foreground">Create a new project by filling up a basic new project form</p>
          </Card>
          <Card class="flex flex-col space-y-4 bg-gray-50 p-4 dark:bg-neutral-800 text-sm">
            <UserRoundPlus color="#e32884" />
            <a :href="`/clients/create`" class="hover:text-primary">new client ></a>
            <p class="text-muted-foreground">Create a new client and associate it to a project</p>
          </Card>
        </div>
      </section>
    </div>
    <div v-else class="flex flex-col gap-4 p-8">
      <Heading title="Projects" description="You currently have no projects" />
      Let's create some
      <!-- TODO: get the clients and languages here -->
      <NewProject :clients="[]" :languages="[]" />
    </div>
  </AppLayout>
</template>
<style scoped>
.chart-container {
  width: 760px;
  max-width: 100%;
  margin: 0 auto 3rem;
}
</style>
