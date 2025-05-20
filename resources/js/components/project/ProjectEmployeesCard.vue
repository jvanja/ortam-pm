<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
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

        <Table v-else class="min-w-full divide-y divide-gray-200">
          <TableHeader>
            <TableRow>
              <TableHead scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</TableHead>
              <TableHead scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Role</TableHead>
              <TableHead scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"></TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="employee in projectEmployees" :key="employee.id">
              <TableCell class="whitespace-nowrap px-6 py-4">
                <div class="text-sm">{{ employee.name }}</div>
              </TableCell>
              <TableCell class="whitespace-nowrap px-6 py-4">
                <div class="flex gap-2">
                  <span v-for="role in employee.roles" :key="role.id" class="badge badge-ghost badge-sm">{{ role.name }}</span>
                </div>
              </TableCell>
              <TableCell class="whitespace-nowrap px-6 py-4 text-right">
                <div class="flex gap-2 justify-end">
                  <Button variant="default"><a :href="`/users/${employee.id}`">View employee</a></Button>
                  <Button variant="destructive" @click="removeEmployee(employee.id!)">Remove</Button>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
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
            <Button variant="ghost" class="hover:bg-transparent" size="icon" :disabled="newEmployeeId === ''" @click="assignEmployee(newEmployeeId)"
              ><PlusCircle class="text-green-700"
            /></Button>
          </div>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
