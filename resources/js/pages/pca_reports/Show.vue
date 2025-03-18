<script setup lang="ts">
import { type BreadcrumbItem } from '@/types';
import { type p_c_a_reportsEntity } from '@/types/DatabaseModels';

import Interior from '@/components/PCAReport/Interior.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Toaster } from '@/components/ui/sonner';
import { Tabs, TabsContent } from '@/components/ui/tabs';
import useSidebar from '@/composables/useSidebar';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps<{ pca_report: p_c_a_reportsEntity }>();
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'PCA Reports', href: '/pca-reports' },
  { title: props.pca_report.name, href: props.pca_report.id! },
];
const { setSidebarMenu } = useSidebar();
const currentTab = ref('summary');
const form = useForm({ ...props.pca_report });

const handleFormUpdate = (newForm: p_c_a_reportsEntity) => {
  Object.assign(form, newForm);
};
const submit = () => {
  /* @ts-expect-error This is fine */
  form.patch(route('pca_reports.update', props.pca_report.id), {
    preserveScroll: true,
    onSuccess: () => {
      console.log(form);
      toast.success('Report has been updated successfully!', {
        style: { background: '#6ee7b7', color: '#000' },
      });
    },
  });
};

const mainNavItems = [
  { title: 'Summary', href: '#summary' },
  { title: 'Introduction', href: '#introduction' },
  { title: 'Structure', href: '#structure' },
  { title: 'Exterior', href: '#exterior' },
  { title: 'Roofing', href: '#roofing' },
  { title: 'Electrical', href: '#electrical' },
  { title: 'Mechanical Systems', href: '#mechanical-systems' },
  { title: 'Plumbing', href: '#plumbing' },
  { title: 'Interior', href: '#interior' },
  { title: 'Conclusion', href: '#conclusion' },
];

const changeTab = (newTab: string) => {
  newTab = newTab === '' ? 'summary' : newTab;
  currentTab.value = newTab;
};

onMounted(() => {
  setSidebarMenu(mainNavItems);
  changeTab(location.hash.replace(/#/g, ''));
});

onUnmounted(() => {
  setSidebarMenu();
});
</script>
<template>
  <Head title="PCA Report" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Card>
          <CardHeader>
            <CardTitle>{{ props.pca_report.name }}</CardTitle>
            <CardDescription>{{ props.pca_report.year_of_construction }}</CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
              <Tabs :default-value="currentTab" v-model="currentTab">
                <TabsContent value="summary">
                  <div class="grid w-full items-center gap-4">
                    <div class="flex flex-col space-y-1.5">
                      <Label htmlFor="name">Occupation of the property</Label>
                      <Input v-model="form.occupation_of_the_property" id="name" :placeholder="props.pca_report.occupation_of_the_property" />
                    </div>
                  </div>
                </TabsContent>
                <TabsContent value="interior"><Interior :form="form" @update:form="handleFormUpdate" /></TabsContent>
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
