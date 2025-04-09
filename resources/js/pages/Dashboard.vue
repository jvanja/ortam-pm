<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import NewProject from '@/components/project/NewProject.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Project } from '@/types';
import { Head } from '@inertiajs/vue3';

defineProps<{ projects: Project[] }>();
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];

const chartOptions = (project: Project) => {
  return {
    chart: {
      height: 350,
      type: 'radialBar',
    },
    plotOptions: {
      radialBar: {
        hollow: {
          size: '70%',
        },
      },
    },
    fill: {
      colors: project.status === 'canceled' ? '#ff0000' : '#0000ff',
    },
    labels: [project.type],
  };
};
const series = () => [70];
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div v-if="projects.length > 1" class="flex flex-col gap-4 p-8">
      <Heading title="Projects" description="These are your latest projects" />
      <div class="grid grid-cols-3 gap-2">
        <div v-for="project in projects" :key="project.id" class="flex flex-col justify-between rounded-lg bg-neutral-100 px-1 py-4 dark:bg-neutral-800">
          <div class="chart-container">
            <apexchart :options="chartOptions(project)" :series="series()"></apexchart>
          </div>
          <div class="flex gap-2 justify-center">
            <Button variant="default"><a :href="`/projects/${project.id}`">Edit</a></Button>
            <Button variant="destructive">Delete</Button>
          </div>
        </div>
      </div>
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
