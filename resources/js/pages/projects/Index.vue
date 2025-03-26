<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import ObjectList from '@/components/ObjectList.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { type projectsEntity } from '@/types/DatabaseModels';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{ projects: projectsEntity[]; }>();
const breadcrumbs: BreadcrumbItem[] = [ { title: 'Projects', href: '/projects' }];

const objects = props.projects.map((project) => {
  return { id: project.id!, name: project.type };
});
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Heading title="Projects" description="These are your latest projects" />
        <ObjectList :objects="objects" type="projects"/>
        <div class="grid gap-1">
          <Label for="name">Search projects</Label>
          <Input id="search" class="mt-1 block w-full" placeholder="Project name" />
        </div>
        <Button>Add new</Button>
      </div>
    </div>
  </AppLayout>
</template>
