<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import type { Project, ProjectPipelineStage } from '@/types';
import { router, useForm } from '@inertiajs/vue3'; // Import router
import { CheckCircle2, CirclePlus, GripVertical, Trash2 } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import Draggable from 'vuedraggable';
import PipelineStageTasks from './PipelineStageTasks.vue';

const props = defineProps<{
  project: Project;
  pipelineStages: ProjectPipelineStage[];
  currentPipelineStage: ProjectPipelineStage | null;
}>();
console.log(props)

const stages = ref([...props.pipelineStages]);
const currentStageId = ref(props.currentPipelineStage ? props.currentPipelineStage.id : stages.value.length > 0 ? stages.value[0].id : null);
const isCurrentStage = (stage: ProjectPipelineStage) => stage.id === currentStageId.value;
const isDragging = ref(false);

/* ==========================================================================
 Pipeline stage actions
 ========================================================================== */
// Dragging
const updateStageOrderForm = useForm({ stage_ids: stages.value.map((stage) => stage.id) });
const handleDragEnd = (event: {newTarget: HTMLElement, newIndex: number, item: HTMLElement}) => {
  let dragginCurrent = false;
  if (event.item.getAttribute('data-current') === 'true') {
    dragginCurrent = true;
  }
  isDragging.value = false;
  // Update the form data with the new order
  updateStageOrderForm.stage_ids = stages.value.map((stage) => stage.id);
  // Send the new order to the backend
  updateStageOrderForm.patch(route('projects.pipeline-stages.updateOrder', { project: props.project.id }), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Stage order updated successfully!');
      if (dragginCurrent) {
        const stage = stages.value[event.newIndex - 1];
        const successHandler = () => {
          console.log('Current stage updated successfully!');
          currentStageId.value = stage.id;
        };
        const errorHandler = (errors: object) => {
          console.error('Set current stage error:', errors);
        };
        apiSetCurrentStage(stage, successHandler, errorHandler);
      }
      router.reload({ only: ['pipelineStages', 'currentPipelineStage'] });
    },
    onError: (errors) => {
      console.error('Update stage order error:', errors);
      toast.error('Failed to update stage order.');
      router.reload({ only: ['pipelineStages', 'currentPipelineStage'] });
    },
  });
};

// Deleting
const deleteStage = (stageId: string) => {
  if (!confirm('Are you sure you want to delete this stage?')) return;

  router.delete(route('projects.pipeline-stages.destroy', { project: props.project.id, stage: stageId }), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Stage deleted successfully!');
      // Optimistically remove from UI or rely on Inertia reload
      stages.value = stages.value.filter((stage) => stage.id !== stageId);
      router.reload({ only: ['pipelineStages', 'currentPipelineStage'] });
    },
    onError: (errors) => {
      console.error('Delete stage error:', errors);
      toast.error('Failed to delete stage.');
      router.reload({ only: ['pipelineStages', 'currentPipelineStage'] });
    },
  });
};

// Setting current stage
const setCurrentStage = (stage: ProjectPipelineStage) => {
  if (isCurrentStage(stage)) return;

  const successHandler = () => {
    toast.success('Current stage updated successfully!');
    currentStageId.value = stage.id;
  };
  const errorHandler = (errors: object) => {
    console.error('Set current stage error:', errors);
    toast.error('Failed to set current stage.');
  };
  apiSetCurrentStage(stage, successHandler, errorHandler);
  router.reload({ only: ['pipelineStages', 'currentPipelineStage'] });
};

const apiSetCurrentStage = (stage: ProjectPipelineStage, successHandler: () => void, errorHandler: (errors: object) => void) => {
  router.patch(
    route('projects.pipeline-stages.setCurrent', { project: props.project.id, stage: stage.id }),
    {},
    {
      preserveScroll: true,
      onSuccess: successHandler,
      onError: errorHandler,
    },
  );
};

// Adding new stage
const addStageForm = useForm({ name: '' });
const addStage = () => {
  const stageName = prompt('Enter the name for the new pipeline stage:');
  if (!stageName) return;

  addStageForm.name = stageName;
  addStageForm.post(route('projects.pipeline-stages.store', { project: props.project.id }), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('New stage added successfully!');
      addStageForm.reset();
      router.reload({ only: ['pipelineStages', 'currentPipelineStage'] });
    },
    onError: (errors) => {
      console.error('Add stage error:', errors);
      toast.error('Failed to add new stage.');
    },
  });
};

const renameStageForm = useForm({ name: '' });
const stageRename = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const newName = target.value;
  renameStageForm.name = newName;
  const stageId = target.getAttribute('stage-id');
  renameStageForm.patch(route('projects.pipeline-stages.update', [stageId]), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Stage name updated successfully!');
      router.reload({ only: ['pipelineStages', 'currentPipelineStage'] });
    },
    onError: (errors) => {
      console.error('Update stage name error:', errors);
      toast.error('Failed to update stage name.');
      router.reload({ only: ['pipelineStages', 'currentPipelineStage'] });
    },
  });
};

/* ==========================================================================
 Watchers
 ========================================================================== */
watch(
  () => props.pipelineStages,
  (newStages) => (stages.value = newStages),
);
watch(
  () => props.currentPipelineStage,
  (newCurrentStage) => (currentStageId.value = newCurrentStage ? newCurrentStage.id : null),
);

/* ==========================================================================
 Lifecycle hooks
 ========================================================================== */
onMounted(() => {
  document.querySelector('.pipeline-step[data-current=true]')!.scrollIntoView();
});
</script>
<template>
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
              'pipeline-step group flex min-w-[150px] flex-col items-center justify-between rounded-lg border p-4 text-center',
              isCurrentStage(stage) ? 'border-primary bg-lime-50 dark:bg-lime-900/50' : 'border-gray-200 dark:border-gray-700',
            ]"
            :data-current="isCurrentStage(stage) ? 'true' : 'false'"
          >
            <div class="flex max-w-full flex-grow flex-col items-center justify-center">
              <CheckCircle2 v-if="isCurrentStage(stage)" class="mb-2 h-6 w-6 text-primary" />
              <CheckCircle2 v-else @click="setCurrentStage(stage)" class="invisible mb-2 h-6 w-6 cursor-pointer text-gray-300 group-hover:visible">
                <title>Set as current pipeline stage</title>
              </CheckCircle2>
              <input
                :value="stage.name"
                class="max-w-full bg-transparent py-2 text-center text-lg font-semibold"
                @change="stageRename"
                :stage-id="stage.id"
              />
            </div>
            <div class="mt-4 flex w-full justify-center gap-2">
              <!-- Drag handle -->
              <Button variant="ghost" size="icon" class="drag-handle cursor-grab">
                <GripVertical class="h-4 w-4" />
              </Button>
              <!-- Delete button -->
              <Button
                variant="ghost"
                size="icon"
                class="text-destructive hover:text-destructive"
                @click="deleteStage(stage.id!)"
                :disabled="isCurrentStage(stage)"
              >
                <Trash2 class="h-4 w-4" />
              </Button>
            </div>
          </div>
        </template>
        <template #header>
          <!-- Add new stage button -->
          <div class="flex min-w-[150px] cursor-pointer flex-col items-center justify-center" @click="addStage">
            <CirclePlus class="mb-2 h-6 w-6 text-gray-300" />
            <button type="button">Add new stage</button>
          </div>
        </template>
      </Draggable>
    </CardContent>
    <CardContent>
      <div class="flex gap-4">
        <div v-if="currentPipelineStage && currentPipelineStage.tasks" class="flex-1 border">
          <PipelineStageTasks :tasks="currentPipelineStage.tasks"></PipelineStageTasks>
        </div>
        <div v-if="currentPipelineStage && currentPipelineStage.notes" class="flex-1 border p-2">
          Notes:
          {{ currentPipelineStage.notes}}
        </div>
      </div>
    </CardContent>
  </Card>
</template>
<style scoped lang="postcss">
.dragging > div {
  opacity: 0.7;
}
</style>
