<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { type p_c_a_reportsEntity } from '@/types/DatabaseModels';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    pca_reports: p_c_a_reportsEntity[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'pca_reports',
        href: '/pca_reports',
    },
];

console.log(props.pca_reports);
</script>

<template>
    <Head title="PCA Reports" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-col gap-4 p-4">
                <Heading title="PCA Reports" description="These are your latest PCA Reports" />
                <div class="flex flex-col gap-2">
                    <div v-for="pca_report in pca_reports" :key="pca_report.id" class="flex justify-between rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800">
                        <div class="px-4 py-2 text-sm font-medium">{{ pca_report.name }}</div>
                        <div class="flex gap-2">
                            <Button variant="default"><a :href="`/pca_reports/${pca_report.id}`">Edit</a></Button>
                            <Button variant="destructive">Delete</Button>
                        </div>
                    </div>
                </div>
                <div class="grid gap-1">
                    <Label for="name">Search PCA reports</Label>
                    <Input id="search" class="mt-1 block w-full" placeholder="PCA Report name" />
                </div>
                <Button>Add new</Button>
            </div>
        </div>
    </AppLayout>
</template>
