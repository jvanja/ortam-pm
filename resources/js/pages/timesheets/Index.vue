<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import ObjectList from '@/components/ObjectList.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Project, TimeSheet } from '@/types';
import { Head } from '@inertiajs/vue3';
type TimesheetWithProject = TimeSheet & { project: Project };

const props = defineProps<{ timesheets: TimesheetWithProject[] }>();
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Timesheets', href: '/timesheets' }];

// console.log(props.timesheets);
const timesheets = props.timesheets;
const ts_created = timesheets.map((ts) => new Date(ts.created_at!).toLocaleDateString());
const ts_hours = timesheets.map((ts) => ts.worked_duration);
const ts_task = timesheets.map((ts) => ts.task_performed);
const ts_client = timesheets.map((ts) => ts.project.type);
const objects = props.timesheets.map((timesheet) => {
  return { id: timesheet.id!, name: `<strong>${timesheet.project.type}</strong> - ${timesheet.task_performed} - ${timesheet.worked_duration} hours` };
});

const chartOptions = {
  chart: {
    id: 'timesheets',
    type: 'bar',
    width: '100%',
    height: 380,
    events: {
      click: (_event: string, _chartContext: unknown, opts: {dataPointIndex: number}) => console.log(opts.dataPointIndex),
    },
  },
  xaxis: {
    categories: ts_created,
  },
  tooltip: {
    enabled: true,
    x: {
      formatter: function (_value: string, { dataPointIndex }: { dataPointIndex: number }) {
        return ts_client[dataPointIndex];
      },
    },
    y: {
      formatter: function (_value: string, { dataPointIndex }: { dataPointIndex: number }) {
        return ts_task[dataPointIndex];
      },
    },
  },
};
const series = [{ name: 'Task', data: ts_hours }];
</script>

<template>
  <Head title="Timesheets" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Heading title="Timesheets" description="These are your latest timesheets" />
        <div id="chart">
          <apexchart :options="chartOptions" :series="series"></apexchart>
        </div>
        <ObjectList :objects="objects" type="timesheets" />
        <div class="grid gap-1">
          <Label for="name">Search projects</Label>
          <Input id="search" class="mt-1 block w-full" placeholder="timesheet name" />
        </div>
        <Button>Add new</Button>
      </div>
    </div>
  </AppLayout>
</template>
<style scoped>
#chart {
  width: 760px;
  max-width: 100%;
  margin: 0 auto 3rem;
}
</style>
