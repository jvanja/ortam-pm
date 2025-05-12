<script setup lang="ts">
import LatestProjectsChart from '@/components/dashboard/LatestProjectsChart.vue';
import Heading from '@/components/Heading.vue';
import NewProject from '@/components/project/NewProject.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Project } from '@/types';
import { Head } from '@inertiajs/vue3';

defineProps<{ projects: Project[] }>();
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
        <Heading title="Actions" description="" />
        <div class="grid grid-cols-3 gap-2">
          <div>
            <Button variant="outline" class="cursor-pointer"><a :href="`/projects/new`" class="text-sm hover:text-primary">New Project</a></Button>
          </div>
          <div>
            <Button variant="outline" class="cursor-pointer"><a :href="`/new-project/`" class="text-sm hover:text-primary">New proposal</a></Button>
          </div>
          <div>
            <Button variant="outline" class="cursor-pointer"><a :href="`/new-project/`" class="text-sm hover:text-primary">New Invoice</a></Button>
          </div>
        </div>
      </section>
    </div>
    <div v-else class="flex flex-col gap-4 p-8">
      <Heading title="Projects" description="You currently have no projects" />
      Let's create some
      <NewProject></NewProject>
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
