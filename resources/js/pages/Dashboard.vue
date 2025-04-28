<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import NewProject from '@/components/project/NewProject.vue';
import { Button } from '@/components/ui/button';
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
const getSeries = (project: Project) => {
  // console.log(project);
  return [70];
};

const options = {
  series: [
    {
      data: [
        {
          x: 'Analysis',
          y: [new Date('2019-02-27').getTime(), new Date('2019-03-04').getTime()],
          fillColor: '#008FFB',
        },
        {
          x: 'Design',
          y: [new Date('2019-03-04').getTime(), new Date('2019-03-08').getTime()],
          fillColor: '#00E396',
        },
        {
          x: 'Coding',
          y: [new Date('2019-03-07').getTime(), new Date('2019-03-10').getTime()],
          fillColor: '#775DD0',
        },
        {
          x: 'Testing',
          y: [new Date('2019-03-08').getTime(), new Date('2019-03-12').getTime()],
          fillColor: '#FEB019',
        },
        {
          x: 'Deployment',
          y: [new Date('2019-03-12').getTime(), new Date('2019-03-17').getTime()],
          fillColor: '#FF4560',
        },
      ],
    },
  ],
  chart: {
    height: 350,
    type: 'rangeBar',
  },
  plotOptions: {
    bar: {
      horizontal: true,
      distributed: true,
      dataLabels: {
        hideOverflowingLabels: false,
      },
    },
  },
  dataLabels: {
    enabled: true,
    formatter: function (val, opts) {
      // console.log(val)
      const label = opts.w.globals.labels[opts.dataPointIndex];
      // const a = new Date(val[0])
      // const b = new Date(val[1])
      // const diff = b.diff(a, 'days');
      const diff = (val[1] - val[0]) / (1000 * 3600 * 24);
      return label + ': ' + diff + (diff > 1 ? ' days' : ' day');
    },
    style: {
      colors: ['#f3f4f5', '#fff'],
    },
  },
  xaxis: {
    type: 'datetime',
  },
  yaxis: {
    show: false,
  },
  grid: {
    row: {
      colors: ['#f3f4f5', '#fff'],
      opacity: 1,
    },
  },
};
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div v-if="projects.length > 1" class="flex flex-col gap-4 p-8">
      <Heading title="Projects" description="These are your latest projects" />
          <div class="chart-container">
            <apexchart :options="options" :series="options.series"></apexchart>
          </div>
      <div class="grid grid-cols-3 gap-2">
        <div
          v-for="project in projects"
          :key="project.id"
          class="flex flex-col justify-between rounded-lg bg-neutral-100 px-1 py-4 dark:bg-neutral-800"
        >
          <div class="chart-container">
            <apexchart :options="chartOptions(project)" :series="getSeries(project)"></apexchart>
          </div>
          <div class="flex justify-center gap-2">
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
