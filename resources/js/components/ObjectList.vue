<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
const props = defineProps<{
  objects: {id:string, name:string, deadline?:string}[]
  type: string
  canDelete?: boolean
}>();
const ids = props.objects.map((object) => object.id);
const form = useForm({ ids });
const deleteObject = (id: string) => {
  toast.warning(`This deletion would also delete all the records associated with it.`)
  form.delete(route(`${props.type}.destroy`, [id]), {
    preserveScroll: true,
    onSuccess: (msg) => {
      console.log(msg)
      toast.success('Employee has been removed successfully!', { style: { background: '#6ee7b7', color: '#000' } });
    },
  });
};

const isPassedDeadline = (deadline: string) => {
  const today = new Date();
  const deadlineDate = new Date(deadline);
  return today.setHours(0, 0, 0, 0) >= deadlineDate.setHours(0, 0, 0, 0)
}
</script>

<template>
  <div class="flex flex-col gap-2">
    <div v-for="object in objects" :key="object.id" class="flex justify-between rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800" :class="{ 'bg-red-100': object.deadline && isPassedDeadline(object.deadline) }">
      <div class="px-4 py-2 text-sm font-medium" v-html="object.name" />
      <div class="flex gap-2">
        <Button variant="link"><a :href="`/${type}/${object.id}`">View</a></Button>
        <Button v-if="canDelete == true" variant="destructive" @click="deleteObject(object.id)">Delete</Button>
      </div>
    </div>
  </div>
</template>
