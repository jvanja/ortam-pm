<script setup lang="ts">
import { Checkbox } from '@/components/ui/checkbox';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { TimeSheet } from '@/types';
import { PlusCircle } from 'lucide-vue-next';
defineProps<{
  tasks: TimeSheet[]
}>();

const addTask = () => {
  console.log('add new');
};
</script>
<template>
  <Table class="min-w-full divide-y divide-gray-200">
    <TableHeader>
      <TableRow class="text-gray-500">
        <TableHead class="border-b p-2 text-left text-xs font-normal"><Checkbox /></TableHead>
        <TableHead class="border-b p-2 text-left text-xs font-normal">Task</TableHead>
        <TableHead class="border-b p-2 text-left text-xs font-normal">User</TableHead>
      </TableRow>
    </TableHeader>
    <TableBody>
      <TableRow v-for="(task, index) in tasks" :key="index">
        <TableCell class="px-2 py-4 text-sm">
          <Checkbox v-model="task.id" />
        </TableCell>
        <TableCell class="px-2 py-4 text-sm">
          {{ task.task_performed }}
        </TableCell>
        <TableCell class="px-2 py-4 text-sm">
          {{ task.user_id }}
        </TableCell>
      </TableRow>
      <TableRow>
        <TableCell colspan="2" class="px-2 py-4">
          <div class="rounded-lg border-2 border-dashed border-teal-400 bg-teal-50 p-1 px-4 py-2 dark:bg-neutral-800">
            <button type="button" @click="addTask" class="flex w-full flex-1 items-center gap-2 text-sm text-green-700">
              <PlusCircle class="text-green-700" />
              Add new item
            </button>
          </div>
        </TableCell>
      </TableRow>
    </TableBody>
  </Table>
</template>
