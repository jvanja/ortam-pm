<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { User } from '@/types';
import type { InertiaForm } from '@inertiajs/vue3';
import { PlusCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
  projectId: string;
  employees: User[];
  projectEmployeeForm: InertiaForm<{ employeesIds: string[] }>;
  removeEmployee: (id: string) => void;
  assignEmployee: (id: string) => void;
}>();
// @ts-expect-error project_ids are added in the backend
const projectEmployees = computed(() => props.employees.filter((employee) => employee.project_ids.includes(props.projectId)));
const newEmployeeId = ref('');
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Employees</CardTitle>
    </CardHeader>
    <CardContent>
      <div class="flex flex-col gap-2">
        <div v-if="projectEmployees.length === 0" class="text-muted-foreground">You currently have no employees on this project.</div>
        <div
          v-else
          v-for="employee in projectEmployees"
          :key="employee.id"
          class="flex justify-between rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800"
        >
          <div class="px-4 py-2 text-sm font-medium">{{ employee.name }}</div>
          <div class="flex gap-2">
            <Button variant="default"><a :href="`/users/${employee.id}`">View employee</a></Button>
            <Button variant="destructive" @click="removeEmployee(employee.id!)">Remove</Button>
          </div>
        </div>
        <div class="flex rounded-lg border-2 border-dashed bg-teal-50 p-1 px-4 py-2 dark:bg-neutral-800">
          <div class="flex flex-1 items-center gap-2 text-sm text-green-700">
            <Select id="new-employee" v-model="newEmployeeId" required>
              <SelectTrigger>
                <SelectValue placeholder="Add new employee" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectItem v-for="employee in employees" :key="employee.id" :value="employee.id">
                    {{ employee.name }}
                  </SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
            <Button variant="ghost" class="hover:bg-transparent" size="icon" :disabled="newEmployeeId === ''" @click="assignEmployee(newEmployeeId)"><PlusCircle class="text-green-700" /></Button>
          </div>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
