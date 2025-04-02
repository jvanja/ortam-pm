<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import ObjectList from '@/components/ObjectList.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, PCA_Report } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{ pca_reports: PCA_Report[] }>();
const breadcrumbs: BreadcrumbItem[] = [{ title: 'PCA Reports', href: '/pca-reports' }];

const objects = props.pca_reports.map((pca_report) => {
  return { id: pca_report.id!, name: pca_report.name };
});
</script>

<template>
  <Head title="PCA Reports" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Heading title="PCA Reports" description="These are your latest PCA Reports" />
        <ObjectList :objects="objects" type="pca-reports" :can-delete="true" />
        <div class="grid gap-1">
          <Label for="name">Search PCA reports</Label>
          <Input id="search" class="mt-1 block w-full" placeholder="PCA Report name" />
        </div>
        <Button>Add new</Button>
      </div>
    </div>
  </AppLayout>
</template>
