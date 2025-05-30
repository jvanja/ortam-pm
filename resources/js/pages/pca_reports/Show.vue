<script setup lang="ts">
import { Interior, Summary } from '@/components/pca-report';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, PCA_Report } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
type ReportWithProject = PCA_Report & { project: { type: string; id: number } };

const props = defineProps<{ pca_report: ReportWithProject }>();
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'PCA Reports', href: '/pca-reports' },
  { title: props.pca_report.name, href: props.pca_report.id! },
];
const currentTab = ref('Summary');
const form = useForm({ ...props.pca_report });
const handleFormUpdate = (newForm: PCA_Report) => Object.assign(form, newForm);
const submit = () => {
  form.patch(route('pca-reports.update', [props.pca_report.id]), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Report has been updated successfully!', { style: { background: '#6ee7b7', color: '#000' } });
    },
  });
};

const tabsMenu = [
  { title: 'Summary', to: 'Summary', component: Summary },
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
              <CardDescription>
                <strong>
                  <a :href="'/projects/' + pca_report.project.id" class="inline-flex items-center justify-center gap-2">
                    {{ pca_report.project.type }} <ChevronRight class="h-4 w-4" />
                  </a>
                </strong>
              </CardDescription>
            <CardDescription>Location: {{ pca_report.address }}</CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
              <Tabs default-value="Summary" v-model="currentTab">
                <TabsList aria-label="tabs example">
                  <TabsTrigger v-for="tab in tabsMenu" :value="tab.to" :key="tab.to">{{ tab.title }}</TabsTrigger>
                </TabsList>

                <TabsContent v-for="tab in tabsMenu" :value="tab.to" :key="tab.to" class="py-8">
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
</template>
