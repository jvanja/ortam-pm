<script setup lang="ts">
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const page = usePage()
const organization = computed(() => page.props.organization )
</script>

<template>
  <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
    <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
  </div>
  <div class="ml-1 grid flex-1 text-left text-sm">
    <span v-if="organization" class="mb-0.5 truncate font-semibold leading-none">{{ organization }}</span>
  </div>
</template>
