<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { type projectsEntity } from '@/types/DatabaseModels';
import { Head } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
// import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

const props = defineProps<{
    project: projectsEntity;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Projects',
        href: '/projects',
    },
    {
        title: props.project.project_type,
        href: props.project.id,
    },
];

console.log(props.project);
</script>
<template>
    <Head title="Project" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-col gap-4 p-4">
                <Heading :title="project.project_type" :description="project.project_address" />
                <Card>
                    <CardHeader>
                        <CardTitle>Create project</CardTitle>
                        <CardDescription>Deploy your new project in one-click.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form>
                            <div class="grid w-full items-center gap-4">
                                <div class="flex flex-col space-y-1.5">
                                    <Label htmlFor="name">Name</Label>
                                    <Input id="name" placeholder="Name of your project" />
                                </div>
                                <div class="flex flex-col space-y-1.5">
                                    <Label htmlFor="framework">Framework</Label>
                                    <!-- <Select> -->
                                    <!--     <SelectTrigger id="framework"> -->
                                    <!--         <SelectValue placeholder="Select" /> -->
                                    <!--     </SelectTrigger> -->
                                    <!--     <SelectContent position="popper"> -->
                                    <!--         <SelectItem value="next">Next.js</SelectItem> -->
                                    <!--         <SelectItem value="sveltekit">SvelteKit</SelectItem> -->
                                    <!--         <SelectItem value="astro">Astro</SelectItem> -->
                                    <!--         <SelectItem value="nuxt">Nuxt.js</SelectItem> -->
                                    <!--     </SelectContent> -->
                                    <!-- </Select> -->
                                </div>
                            </div>
                        </form>
                    </CardContent>
                    <CardFooter class="flex justify-between">
                        <Button variant="outline">Cancel</Button>
                        <Button>Deploy</Button>
                    </CardFooter>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
