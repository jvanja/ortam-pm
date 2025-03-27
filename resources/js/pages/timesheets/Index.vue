<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import ObjectList from '@/components/ObjectList.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import type { time_sheetsEntity, projectsEntity } from '@/types/DatabaseModels';
import { Head } from '@inertiajs/vue3';
type timesheetWithProject = time_sheetsEntity & {project: projectsEntity}

const props = defineProps<{ timesheets: timesheetWithProject[]; }>();
const breadcrumbs: BreadcrumbItem[] = [ { title: 'Timesheets', href: '/timesheets' }];

const objects = props.timesheets.map((timesheet) => {
  return { id: timesheet.id!, name: `${timesheet.project.type} - ${timesheet.task_performed} - ${timesheet.worked_duration} hours` };
});
</script>

<template>
  <Head title="Timesheets" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Heading title="Timesheets" description="These are your latest timesheets" />
        <ObjectList :objects="objects" type="timesheets"/>
        <div class="grid gap-1">
          <Label for="name">Search projects</Label>
          <Input id="search" class="mt-1 block w-full" placeholder="timesheet name" />
        </div>
        <Button>Add new</Button>
      </div>
    </div>
  </AppLayout>
</template>
