<script setup lang="ts">
import type { Project } from '@/types';
import { Button } from '@/components/ui/button';

defineProps<{ projects: Project[] }>();

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
const getSeries = (project: Project) => {
  console.log(project);
  return [70];
};
</script>

<template>
  <div class="mb-4 grid grid-cols-3 gap-2">
    <div v-for="project in projects" :key="project.id" class="flex flex-col justify-between rounded-lg bg-neutral-100 px-1 py-4 dark:bg-neutral-800">
      <div class="chart-container">
        <apexchart :options="chartOptions(project)" :series="getSeries(project)"></apexchart>
      </div>
      <div class="flex justify-center gap-2">
        <Button variant="default"><a :href="`/projects/${project.id}`">Edit</a></Button>
        <Button variant="destructive">Delete</Button>
      </div>
    </div>
  </div>
</template>
<style scoped>
.chart-container {
  width: 760px;
  max-width: 100%;
  margin: 0 auto 3rem;
}
</style>
