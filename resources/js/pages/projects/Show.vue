<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { clientsEntity, projectsEntity } from '@/types/DatabaseModels';
type projectWithClient = projectsEntity & { client: clientsEntity };

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const props = defineProps<{ project: projectWithClient }>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Projects', href: '/projects' },
  { title: props.project.type, href: '' },
];

const form = useForm({ ...props.project });
const submit = () => {
  console.log(form)
  // /* @ts-expect-error This is fine */
  /* form.patch(route('projects.update', props.project.id), { */
  /*   preserveScroll: true, */
  /*   onSuccess: () => { */
  /*     toast.success('Report has been updated successfully!', { style: { background: '#6ee7b7', color: '#000' } }); */
  /*   }, */
  /* }); */
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
            <h2>{{ project.client.company_name }}</h2>
            <CardDescription>{{ project.address }}</CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
              <div class="grid w-full items-center gap-4">
                <div class="flex flex-col space-y-1.5">
                  <Label htmlFor="rep">Representative Name</Label>
                  <Input id="rep" :placeholder="project.sales_representative_name" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label htmlFor="pm">Project Manager</Label>
                  <Input id="pm" :placeholder="project.manager" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label htmlFor="status">Status</Label>
                  <Select :defaultValue="project.status">
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
            </form>
          </CardContent>
          <CardFooter class="flex justify-between">
            <Button variant="outline">Cancel</Button>
            <Button>Update</Button>
          </CardFooter>
        </Card>
      </div>
    </div>
    <Toaster />
  </AppLayout>
</template>
