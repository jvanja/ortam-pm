<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

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
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="p-4 flex flex-col gap-4">
                <Heading title="Projects" description="These are your latest projects" />
                <div class="flex flex-col gap-2">
                    <div v-for="index in [...Array(3).keys()]" :key="index" class="flex justify-between  rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800">
                        <div class="text-sm font-medium px-4 py-2">Project name</div>
                        <div class="flex gap-2">
                            <Button variant="default">Edit</Button>
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
        </div>
    </AppLayout>
</template>
