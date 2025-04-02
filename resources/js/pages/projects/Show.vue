<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Client, Project, User } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import currencies from 'currency-codes';
import { toast } from 'vue-sonner';

const props = defineProps<{
  project: Project;
  client: Client;
  employees: User[];
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
              <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="flex flex-col space-y-1.5">
                  <Label htmlFor="rep">Project Type</Label>
                  <Input id="rep" :placeholder="form.type" v-model="form.type" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label htmlFor="rep">Departament</Label>
                  <Input id="rep" :placeholder="form.department" v-model="form.department" />
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
                    <Select :defaultValue="project.currency" v-model="form.currency">
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
  </AppLayout>
</template>
