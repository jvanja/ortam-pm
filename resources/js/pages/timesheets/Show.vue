<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type TimeSheet, type Project, type User } from '@/types'; // Assuming TimeSheet includes project and user relations
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input'; // Use Input for display, make read-only
import { Textarea } from '@/components/ui/textarea'; // Use Textarea for details, make read-only

// Define the expected prop structure
// The controller needs to pass a TimeSheet object with 'project' and 'user' loaded
const props = defineProps<{
  timesheet: TimeSheet & { project: Project; user: User };
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Timesheets', href: '/timesheets' },
  { title: `Entry ${props.timesheet.id}`, href: `/timesheets/${props.timesheet.id}` }, // Dynamic breadcrumb
];

// Helper function to format date
const formatDate = (dateString: string | Date | null) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString(undefined, {
    year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
  });
};

</script>

<template>
  <Head :title="`Timesheet Entry - ${timesheet.id}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 md:p-8">
      <Card>
        <CardHeader>
          <CardTitle>Timesheet Entry Details</CardTitle>
          <CardDescription>
            Entry recorded by {{ timesheet.user?.name }} on {{ formatDate(timesheet.created_at) }}
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
