<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
// Import the refactored types from index.ts, including TaskPerformedEnum
import { type BreadcrumbItem, type Project, type User, type TaskPerformedEnum, Auth } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { toast } from 'vue-sonner';

// Props definition - Projects are passed from the controller
const props = defineProps<{
  projects: Project[]; // Uses the refactored Project type
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];
// Ensure the user object structure matches the updated User type
const user = (usePage().props.auth as Auth).user as User;

// Define the possible task options based on the enum
const taskOptions: TaskPerformedEnum[] = ['visit', 'research', 'fieldwork', 'report'];

const form = useForm({
  task_performed: null as TaskPerformedEnum | null, // Initialize as null for Select placeholder
  worked_duration: null as number | null,
  project_id: null as string | null, // ID is string
  details: '', // Add details field
  user_id: user.id, // ID is string
});

const submit = () => {
  form.post(route('timesheets.store'), {
    onSuccess: () => {
      form.reset();
      toast.success('Timesheet entry added successfully.', { style: { background: '#6ee7b7', color: '#000' } });
    },
    onError: (errors) => {
      // Log errors for debugging if needed
      console.error('Timesheet submission error:', errors);
      toast.error('Failed to add timesheet entry. Please check the form for errors.');
    },
  });
};

</script>

<template>
  <Head title="Employee Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-4 p-8">
      <Heading title="Employee Dashboard" description="Log your work time" />

      <Card>
        <CardHeader>
          <CardTitle>Add New Timesheet Entry</CardTitle>
          <CardDescription>Fill in the details for your work session.</CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-4">
             <div>
              <Label for="task_performed">Task Performed</Label>
              <Select v-model="form.task_performed" required>
                <SelectTrigger>
                  <SelectValue placeholder="Select a task type" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectLabel>Task Types</SelectLabel>
                    <SelectItem v-for="task in taskOptions" :key="task" :value="task">
                      {{ task.charAt(0).toUpperCase() + task.slice(1) }} <!-- Capitalize for display -->
                    </SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
              <small v-if="form.errors.task_performed" class="text-red-600">{{ form.errors.task_performed }}</small>
            </div>

            <div>
              <Label for="worked_duration">Worked Duration (minutes)</Label>
              <Input
                id="worked_duration"
                type="number"
                v-model.number="form.worked_duration!"
                placeholder="e.g., 60"
                required
                min="1"
              />
              <small v-if="form.errors.worked_duration" class="text-red-600">{{ form.errors.worked_duration }}</small>
            </div>

            <div>
              <Label for="project_id">Project</Label>
              <Select v-model="form.project_id" required>
                <SelectTrigger>
                  <SelectValue placeholder="Select a project" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectLabel>Projects</SelectLabel>
                    <SelectItem v-for="project in props.projects" :key="project.id" :value="project.id">
                      {{ project.address }} <!-- Display project address instead of non-existent name -->
                    </SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
              <small v-if="form.errors.project_id" class="text-red-600">{{ form.errors.project_id }}</small>
            </div>

            <div>
              <Label for="details">Details (Optional)</Label>
              <Textarea
                id="details"
                v-model="form.details"
                placeholder="Add any relevant notes or details about the task..."
              />
              <small v-if="form.errors.details" class="text-red-600">{{ form.errors.details }}</small>
            </div>

            <Button type="submit" :disabled="form.processing">
              {{ form.processing ? 'Saving...' : 'Add Timesheet' }}
            </Button>
          </form>
        </CardContent>
      </Card>

      <div class="flex flex-col gap-2">
        <!-- Existing content can go here, e.g., list of recent timesheets -->
      </div>
    </div>
  </AppLayout>
</template>
