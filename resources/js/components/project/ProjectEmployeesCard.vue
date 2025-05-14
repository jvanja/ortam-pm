<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardTitle, CardHeader } from '@/components/ui/card';
import type { User } from '@/types';
import type { InertiaForm } from '@inertiajs/vue3';
import { PlusCircle } from 'lucide-vue-next';

defineProps<{
  employees: User[];
  projectEmployeeForm: InertiaForm<{ employeesIds: string[] }>;
  removeEmployee: (id: string) => void;
}>();
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Employees</CardTitle>
    </CardHeader>
    <CardContent>
      <div class="flex flex-col gap-2">
        <div v-if="employees.length === 0" class="text-muted-foreground">You currently have no employees on this project.</div>
        <div v-else v-for="employee in employees" :key="employee.id" class="flex justify-between rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800">
          <div class="px-4 py-2 text-sm font-medium">{{ employee.name }}</div>
          <div class="flex gap-2">
            <Button variant="default"><a :href="`/users/${employee.id}`">View employee</a></Button>
            <Button variant="destructive" @click="removeEmployee(employee.id!)">Remove</Button>
          </div>
        </div>
        <div class="rounded-lg border-2 border-dashed p-1 px-4 py-2 bg-teal-50 dark:bg-neutral-800">
          <a href="/employees/create/" class="flex flex-1 items-center gap-2 text-sm text-green-700">
            <PlusCircle class="text-green-700" />
            Add employee</a>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
