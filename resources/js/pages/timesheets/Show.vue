<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type TimeSheet, type Project, type User } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { formatDate } from '@/lib/utils';

const props = defineProps<{
  timesheet: TimeSheet & { project: Project; user: User };
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Timesheets', href: '/timesheets' },
  { title: `Entry ${props.timesheet.id}`, href: `/timesheets/${props.timesheet.id}` }, // Dynamic breadcrumb
];
</script>

<template>
  <Head :title="`Timesheet Entry - ${timesheet.id}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 md:p-8">
      <Card>
        <CardHeader>
          <CardTitle>Timesheet Entry Details</CardTitle>
          <CardDescription>
            Timesheet recorded by <strong><i>{{ timesheet.user?.name }}</i></strong> on {{ formatDate(timesheet.created_at) }}
          </CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="space-y-1.5">
              <Label>Project</Label>
              <Input :model-value="timesheet.project?.address" readonly class="bg-muted/40" />
            </div>
            <div class="space-y-1.5">
              <Label>Task Performed</Label>
              <Input :model-value="timesheet.task_performed.charAt(0).toUpperCase() + timesheet.task_performed.slice(1)" readonly class="bg-muted/40" />
            </div>
            <div class="space-y-1.5">
              <Label>Worked Duration</Label>
              <Input :model-value="timesheet.worked_duration + 'h'" readonly class="bg-muted/40" />
            </div>
             <div class="space-y-1.5">
              <Label>Recorded By</Label>
              <Input :model-value="timesheet.user?.name" readonly class="bg-muted/40" />
            </div>
          </div>
           <div class="space-y-1.5">
              <Label>Details</Label>
              <Textarea :model-value="timesheet.details || 'No details provided.'" readonly rows="4" class="bg-muted/40" />
            </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
