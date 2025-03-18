<script setup lang="ts">
import { type BreadcrumbItem } from '@/types';
import { type p_c_a_reportsEntity } from '@/types/DatabaseModels';

import { Summary, Interior } from '@/components/PCAReport';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Toaster } from '@/components/ui/sonner';
import { Tabs, TabsContent, TabsTrigger, TabsList } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps<{ pca_report: p_c_a_reportsEntity }>();
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'PCA Reports', href: '/pca-reports' },
  { title: props.pca_report.name, href: props.pca_report.id! },
];
const currentTab = ref('Summary');
const form = useForm({ ...props.pca_report });
const handleFormUpdate = (newForm: p_c_a_reportsEntity) => Object.assign(form, newForm);
const submit = () => {
  /* @ts-expect-error This is fine */
  form.patch(route('pca_reports.update', props.pca_report.id), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Report has been updated successfully!', { style: { background: '#6ee7b7', color: '#000' } });
    },
  });
};

const tabsMenu = [
  { title: 'Summary', to: 'Summary', component: Summary},
  { title: 'Introduction', to: 'Introduction' },
  { title: 'Structure', to: 'Structure' },
  { title: 'Exterior', to: 'Exterior' },
  { title: 'Roofing', to: 'Roofing' },
  { title: 'Electrical', to: 'Electrical' },
  { title: 'Mechanical Systems', to: 'MechanicalSystems' },
  { title: 'Plumbing', to: 'Plumbing' },
  { title: 'Interior', to: 'Interior', component: Interior },
  { title: 'Conclusion', to: 'Conclusion' },
];

</script>

<template>
  <Head title="PCA Report" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Card>
          <CardHeader>
            <CardTitle>{{ pca_report.name }}</CardTitle>
            <CardDescription>{{ pca_report.year_of_construction }}</CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
              <Tabs default-value="Summary" v-model="currentTab">
                <TabsList aria-label="tabs example">
                  <TabsTrigger v-for="tab in tabsMenu" :value="tab.to" :key="tab.to">{{tab.title}}</TabsTrigger>
                </TabsList>

                <TabsContent v-for="tab in tabsMenu" :value="tab.to" :key="tab.to">
                  <component :is="tab.component" :form="pca_report" @update:form="handleFormUpdate" />
                </TabsContent>
              </Tabs>
              <div class="flex w-full justify-end">
                <Button :disabled="form.processing">Save</Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
  <Toaster />
</template>
