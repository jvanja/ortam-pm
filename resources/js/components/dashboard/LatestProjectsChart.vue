<script setup lang="ts">
import { Button } from '@/components/ui/button';
import type { Project, ProjectPipelineStage } from '@/types';
type ProjectWithPipelineStages = Project & { pipeline_stages: ProjectPipelineStage[]; current_pipeline_stage: ProjectPipelineStage };

const { projects } = defineProps<{ projects: ProjectWithPipelineStages[] }>();
const chartOptions = (project: ProjectWithPipelineStages) => {
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
    labels: [project.current_pipeline_stage.name],
  };
};
const getSeries = (project: ProjectWithPipelineStages) => {
  const projectStagesLength = project.pipeline_stages.length;
  const currentStageIndex =
    project.pipeline_stages.findIndex((stage: ProjectPipelineStage) => stage.id === project.current_project_pipeline_stage_id) + 1;

  const percentage = Math.round((100 / projectStagesLength) * currentStageIndex);
  return [percentage];
};
</script>

<template>
  <div class="mb-4 grid grid-cols-3 gap-2">
    <div v-for="project in projects" :key="project.id" class="flex flex-col space-y-4 rounded-lg bg-neutral-100 px-1 py-4 dark:bg-neutral-800">
      <div class="chart-container">
        <apexchart :options="chartOptions(project)" :series="getSeries(project)"></apexchart>
      </div>
      <div class="text-sm text-center"><span class="font-semibold">{{ project.type }}</span></div>
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
}
</style>
