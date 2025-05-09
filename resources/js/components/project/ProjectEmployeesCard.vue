<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader } from '@/components/ui/card';
import type { User } from '@/types';
import type { InertiaForm } from '@inertiajs/vue3';

defineProps<{
  employees: User[];
  projectEmployeeForm: InertiaForm<{ employeesIds: string[] }>;
  removeEmployee: (id: string) => void;
}>();
</script>

<template>
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
</template>
