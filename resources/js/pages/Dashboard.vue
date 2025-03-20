<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import NewProject from '@/components/project/NewProject.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { type projectsEntity } from '@/types/DatabaseModels';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
  projects: projectsEntity[];
}>();
console.log(props.projects);

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div v-if="projects.length > 10" class="flex flex-col gap-4 p-8">
      <Heading title="Projects" description="These are your latest projects" />
      <div class="flex flex-col gap-2">
        <div v-for="project in projects" :key="project.id" class="flex justify-between rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800">
          <div class="px-4 py-2 text-sm font-medium">{{ project.project_type }}</div>
          <div class="flex gap-2">
            <Button variant="default"><a :href="`/projects/${project.id}`">Edit</a></Button>
            <Button variant="destructive">Delete</Button>
          </div>
        </div>
      </div>
      <div class="grid gap-1">
        <Label for="name">Search projects</Label>
        <Input id="search" class="mt-1 block w-full" placeholder="Project name" />
      </div>
      <Button>Add new</Button>
    </div>
    <div v-else class="flex flex-col gap-4 p-8">
      Let's create some
      <NewProject></NewProject>
    </div>
  </AppLayout>
</template>
