<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { clientsEntity, projectsEntity, usersEntity } from '@/types/DatabaseModels';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Toaster } from '@/components/ui/sonner';
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const props = defineProps<{
  project: projectsEntity;
  client: clientsEntity;
  employees: usersEntity[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Projects', href: '/projects' },
  { title: props.project.type!, href: '' },
];

const project = { ...props.project };
const client = { ...props.client };
const employees = props.employees;

const form = useForm(project);
const submit = () => {
  console.log(form);
  /* @ts-expect-error This is fine */
  form.patch(route('projects.update', props.project.id), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Report has been updated successfully!', { style: { background: '#6ee7b7', color: '#000' } });
    },
  });
};

const employeesIds = employees.map((employee) => employee.id);
const projectEmployeeForm = useForm({ employeesIds });
const removeEmployee = (id: string) => {
  console.log(id);
  /* @ts-expect-error This is fine */
  projectEmployeeForm.delete(route('projects.employees.destroy', props.project.id, id), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Employee has been removed successfully!', { style: { background: '#6ee7b7', color: '#000' } });
    },
  });
};
console.log(props.project);
</script>
<template>
  <Head title="Project" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Card>
          <CardHeader>
            <CardTitle>{{ project.type }}</CardTitle>
            <h2>{{ client.company_name }}</h2>
            <CardDescription>{{ project.address }}</CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
              <div class="grid w-full items-center gap-4">
                <div class="flex flex-col space-y-1.5">
                  <Label htmlFor="budget">Budget</Label>
                  <Input id="budget" v-model="form.budget" />
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
              </div>
              <div class="flex w-full justify-end">
                <Button :disabled="form.processing">Save</Button>
              </div>
            </form>
          </CardContent>
        </Card>
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
    </div>
    <Toaster />
  </AppLayout>
</template>
