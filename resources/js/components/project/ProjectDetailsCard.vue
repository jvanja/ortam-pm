<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Client, Project, User } from '@/types';
import { useForm } from '@inertiajs/vue3';
import currencies from 'currency-codes';
import { ChevronRight } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const props = defineProps<{
  project: Project;
  client: Client;
  employees: User[];
}>();

const form = useForm({ ...props.project });
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
</script>

<template>
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
            <Input id="department" :placeholder="form.department" v-model="form.department!" />
          </div>
          <div class="flex flex-col space-y-1.5">
            <Label htmlFor="rep">Representative Name</Label>
            <Input id="rep" placeholder="Representative name" v-model="form.sales_representative_name!" />
          </div>
          <div class="flex flex-col space-y-1.5">
            <Label htmlFor="pm">Project Manager</Label>
            <Select id="pm" :defaultValue="project.manager_id" v-model="form.manager_id">
              <SelectTrigger>
                <SelectValue />
              </SelectTrigger>
              <SelectContent position="popper">
                <SelectItem v-for="user in employees" :key="user.id" :value="user.id">{{ user.name }}</SelectItem>
              </SelectContent>
            </Select>
          </div>
          <div class="flex flex-col space-y-1.5">
            <Label htmlFor="status">Status</Label>
            <Select id="status" :defaultValue="project.status" v-model="form.status">
              <SelectTrigger>
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
              <Input id="budget" v-model="form.budget!" />
              <Select id="currency" :defaultValue="project.currency" v-model="form.currency">
                <SelectTrigger class="mt-0">
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
</template>
