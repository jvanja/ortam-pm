<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Client, Project, ProjectPipelineStage, User } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import currencies from 'currency-codes';
import { CheckCircle2, ChevronRight, CirclePlus, GripVertical, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import Draggable from 'vuedraggable';

const props = defineProps<{
  project: Project;
  client: Client;
  employees: User[];
  pipelineStages: ProjectPipelineStage[];
  currentPipelineStage: ProjectPipelineStage | null;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Projects', href: '/projects' },
  { title: props.project.type!, href: '' },
];

const project = { ...props.project };
const client = { ...props.client };
const employees = props.employees;

// State for draggable pipeline stages
const stages = ref([...props.pipelineStages]);
const currentStageId = ref(props.currentPipelineStage ? props.currentPipelineStage.id : stages.value[0].id);
const isDragging = ref(false);

const form = useForm(project);
const submit = () => {
  form.patch(route('projects.update', [props.project.id]), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Project has been updated successfully!', { style: { background: '#6ee7b7', color: '#000' } });
    },
    onError: (errors) => {
      console.error('Project udpate error:', errors);
      toast.error('Failed to update project. Please check the form for errors.');
    },
  });
};

const employeesIds = employees.map((employee) => employee.id);
const projectEmployeeForm = useForm({ employeesIds });
const removeEmployee = (id: string) => {
  projectEmployeeForm.delete(route('projects.employee.remove', [props.project, id]), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Employee has been removed successfully!', { style: { background: '#6ee7b7', color: '#000' } });
    },
  });
};

// Placeholder methods for pipeline stage actions
const handleDragEnd = () => {
  isDragging.value = false;
  // TODO: Implement logic to update stage order on the backend
  console.log(
    'Drag ended. New order:',
    stages.value.map((stage) => stage.id),
  );
  toast.info('Stage order updated (frontend only). Backend update needed.');
};

const deleteStage = (stageId: string) => {
  // TODO: Implement logic to delete stage on the backend
  console.log('Delete stage:', stageId);
  // Optimistically remove from UI
  stages.value = stages.value.filter((stage) => stage.id !== stageId);
  toast.success('Stage deleted (frontend only). Backend deletion needed.');
};

const setCurrentStage = (stage: ProjectPipelineStage) => {
  // TODO: Implement logic to set new current stage on the backend
  currentStageId.value = stage.id;
  toast.success('New current stage set(frontend only). Backend update needed.');
};

const isCurrentStage = (stage: ProjectPipelineStage) => {
  return stage.id === currentStageId.value;
};
</script>
<template>
  <Head title="Project" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <!-- Pipeline Stages Section -->
      <Card>
        <CardHeader>
          <CardTitle>Pipeline Stages</CardTitle>
          <CardDescription>Drag and drop to reorder stages.</CardDescription>
        </CardHeader>
        <CardContent id="content">
          <Draggable
            v-model="stages"
            item-key="id"
            class="flex gap-4 overflow-x-auto pb-4"
            :class="{ dragging: isDragging }"
            @start="isDragging = true"
            @end="handleDragEnd"
            handle=".drag-handle"
          >
            <template #item="{ element: stage }">
              <div
                :class="[
                  'group flex min-w-[150px] flex-col items-center justify-between rounded-lg border p-4 text-center',
                  isCurrentStage(stage) ? 'border-primary bg-lime-50' : 'border-gray-200 dark:border-gray-700',
                ]"
              >
                <div class="flex flex-grow flex-col items-center justify-center">
                  <CheckCircle2 v-if="isCurrentStage(stage)" class="mb-2 h-6 w-6 text-primary" />
                  <CheckCircle2 @click="setCurrentStage(stage)" v-else class="invisible mb-2 h-6 w-6 text-gray-300 group-hover:visible">
                    <title>Check current pipeline step</title>
                  </CheckCircle2>
                  <div class="font-semibold">{{ stage.name }}</div>
                </div>
                <div class="mt-4 flex w-full justify-center gap-2">
                  <Button variant="ghost" size="icon" class="drag-handle cursor-grab">
                    <GripVertical class="h-4 w-4" />
                  </Button>
                  <Button variant="ghost" size="icon" class="text-destructive hover:text-destructive" @click="deleteStage(stage.id!)">
                    <Trash2 class="h-4 w-4" />
                  </Button>
                </div>
              </div>
            </template>
            <template #header>
              <div class="flex flex-col items-center justify-center min-w-[150px]">
                <CirclePlus class="mb-2 h-6 w-6 text-gray-300" />
                <button @click="console.log('add')">Add new stage</button>
              </div>
            </template>
          </Draggable>
        </CardContent>
      </Card>

      <!-- Project Details Section -->
      <Card>
        <CardHeader>
          <CardTitle>{{ project.type }}</CardTitle>
          <CardDescription>
            <strong>
              <a :href="'/clients/' + client.id" class="inline-flex items-center justify-center gap-2">
                {{ client.company_name }} <ChevronRight class="h-4 w-4" />
              </a>
            </strong>
            <div>{{ project.address }}</div>
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div class="flex flex-col space-y-1.5">
                <Label htmlFor="type">Project Type</Label>
                <Input id="type" :placeholder="form.type" v-model="form.type" />
              </div>
              <div class="flex flex-col space-y-1.5">
                <Label htmlFor="department">Departament</Label>
                <Input id="department" :placeholder="form.department" v-model="form.department" />
              </div>
              <div class="flex flex-col space-y-1.5">
                <Label htmlFor="rep">Representative Name</Label>
                <Input id="rep" placeholder="Representative name" v-model="form.sales_representative_name" />
              </div>
              <div class="flex flex-col space-y-1.5">
                <Label htmlFor="pm">Project Manager</Label>
                <Input id="pm" :placeholder="project.manager" v-model="form.manager" />
              </div>
              <div class="flex flex-col space-y-1.5">
                <Label htmlFor="status">Status</Label>
                <Select :defaultValue="project.status" v-model="form.status">
                  <SelectTrigger id="status">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent position="popper">
                    <SelectItem value="ongoing">Ongoing</SelectItem>
                    <SelectItem value="completed">Completed</SelectItem>
                    <SelectItem value="canceled">Canceled</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              <div class="flex flex-col space-y-1.5">
                <Label htmlFor="budget">Budget</Label>
                <div class="flex gap-2">
                  <Input id="budget" v-model="form.budget" />
                  <Select id="currency" :defaultValue="project.currency" v-model="form.currency">
                    <SelectTrigger id="currency" class="mt-0">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem v-for="code in currencies.codes()" :key="code" :value="code">{{ code }}</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>
            </div>
            <div class="flex w-full justify-end">
              <Button :disabled="form.processing">Save</Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <!-- Employees Section -->
      <Card>
        <CardHeader>
          <CardDescription>Employees</CardDescription>
        </CardHeader>
        <CardContent>
          <div class="flex flex-col gap-2">
            <div v-for="employee in employees" :key="employee.id" class="flex justify-between rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800">
              <div class="px-4 py-2 text-sm font-medium">{{ employee.name }}</div>
              <div class="flex gap-2">
                <Button variant="default"><a :href="`/users/${employee.id}`">View employee</a></Button>
                <Button variant="destructive" @click="removeEmployee(employee.id!)">Remove</Button>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
<style scoped lang="postcss">
.dragging > div {
  opacity: 0.5;
}
</style>
