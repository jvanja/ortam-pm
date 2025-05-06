<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Client, Project, ProjectPipelineStage, User } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3'; // Import router
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
// Initialize currentStageId based on prop, default to first stage if available
const currentStageId = ref(props.currentPipelineStage ? props.currentPipelineStage.id : (stages.value.length > 0 ? stages.value[0].id : null));
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
    onError: (errors) => {
      console.error('Remove employee error:', errors);
      toast.error('Failed to remove employee.');
    },
  });
};

// Pipeline stage actions
const updateStageOrderForm = useForm({
  stage_ids: stages.value.map((stage) => stage.id),
});

const handleDragEnd = () => {
  isDragging.value = false;
  // Update the form data with the new order
  updateStageOrderForm.stage_ids = stages.value.map((stage) => stage.id);

  // Send the new order to the backend
  updateStageOrderForm.patch(route('projects.pipeline-stages.updateOrder', { project: props.project.id }), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Stage order updated successfully!');
      // Re-fetch project data to ensure UI is in sync, including current stage if order changed
      // router.reload({ only: ['pipelineStages', 'currentPipelineStage'] }); // Reloading might be heavy, patch response is enough
    },
    onError: (errors) => {
      console.error('Update stage order error:', errors);
      toast.error('Failed to update stage order.');
      // Revert stages to original order on error? Or reload page? Reloading is simpler.
      router.reload({ only: ['pipelineStages', 'currentPipelineStage'] });
    },
  });
};

const deleteStage = (stageId: string) => {
  if (!confirm('Are you sure you want to delete this stage?')) {
    return;
  }

  router.delete(route('projects.pipeline-stages.destroy', { project: props.project.id, stage: stageId }), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Stage deleted successfully!');
      // Optimistically remove from UI or rely on Inertia reload
      stages.value = stages.value.filter((stage) => stage.id !== stageId);
      // Re-fetch project data to ensure UI is in sync, especially if current stage was deleted
      router.reload({ only: ['pipelineStages', 'currentPipelineStage'] });
    },
    onError: (errors) => {
      console.error('Delete stage error:', errors);
      toast.error('Failed to delete stage.');
      // Reload to revert optimistic removal on error
      router.reload({ only: ['pipelineStages', 'currentPipelineStage'] });
    },
  });
};

const setCurrentStage = (stage: ProjectPipelineStage) => {
  if (isCurrentStage(stage)) {
    // Already the current stage, maybe allow unsetting? Or do nothing.
    // Let's do nothing for now.
    return;
  }

  router.patch(route('projects.pipeline-stages.setCurrent', { project: props.project.id, stage: stage.id }), {}, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Current stage updated successfully!');
      currentStageId.value = stage.id;
      // Re-fetch project data to ensure UI is in sync
      router.reload({ only: ['pipelineStages', 'currentPipelineStage'] });
    },
    onError: (errors) => {
      console.error('Set current stage error:', errors);
      toast.error('Failed to set current stage.');
      // Reload to revert optimistic update on error
      router.reload({ only: ['pipelineStages', 'currentPipelineStage'] });
    },
  });
};

const addStageForm = useForm({
  name: '',
});

const addStage = () => {
  const stageName = prompt('Enter the name for the new pipeline stage:');
  if (!stageName) {
    return; // User cancelled or entered empty name
  }

  addStageForm.name = stageName;

  addStageForm.post(route('projects.pipeline-stages.store', { project: props.project.id }), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('New stage added successfully!');
      addStageForm.reset();
      // Re-fetch project data to include the new stage
      router.reload({ only: ['pipelineStages', 'currentPipelineStage'] });
    },
    onError: (errors) => {
      console.error('Add stage error:', errors);
      toast.error('Failed to add new stage.');
    },
  });
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
                  isCurrentStage(stage) ? 'border-primary bg-lime-50 dark:bg-lime-900/50' : 'border-gray-200 dark:border-gray-700',
                ]"
              >
                <div class="flex flex-grow flex-col items-center justify-center">
                  <!-- Check icon for current stage -->
                  <CheckCircle2 v-if="isCurrentStage(stage)" class="mb-2 h-6 w-6 text-primary" />
                  <!-- Clickable check icon to set as current stage -->
                  <CheckCircle2
                    v-else
                    @click="setCurrentStage(stage)"
                    class="invisible mb-2 h-6 w-6 text-gray-300 group-hover:visible cursor-pointer"
                  >
                    <title>Set as current pipeline stage</title>
                  </CheckCircle2>
                  <div class="font-semibold">{{ stage.name }}</div>
                </div>
                <div class="mt-4 flex w-full justify-center gap-2">
                  <!-- Drag handle -->
                  <Button variant="ghost" size="icon" class="drag-handle cursor-grab">
                    <GripVertical class="h-4 w-4" />
                  </Button>
                  <!-- Delete button -->
                  <Button variant="ghost" size="icon" class="text-destructive hover:text-destructive" @click="deleteStage(stage.id!)" :disabled="isCurrentStage(stage)">
                    <Trash2 class="h-4 w-4" />
                  </Button>
                </div>
              </div>
            </template>
            <template #header>
              <!-- Add new stage button -->
              <div class="flex flex-col items-center justify-center min-w-[150px] cursor-pointer" @click="addStage">
                <CirclePlus class="mb-2 h-6 w-6 text-gray-300" />
                <button type="button">Add new stage</button>
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
