<script setup lang="ts">
import FilesList from '@/components/dropzone/FilesList.vue';
import { useDropZone } from '@vueuse/core';
import { Upload } from 'lucide-vue-next';
import { shallowRef, useTemplateRef } from 'vue';

defineProps<{
  errorMessage: string;
}>();

const emit = defineEmits(['submit:files']);

const filesData = shallowRef<{ name: string; size: number; mime_type: string; lastModified: number }[]>([]);
const maxSize = 1024 * 1024 * 10;

function onDrop(files: File[] | null) {
  filesData.value = [];
  if (files) {
    filesData.value = files.map((file) => ({
      name: file.name,
      size: file.size,
      mime_type: file.type,
      lastModified: file.lastModified,
    }));
  }
  emit('submit:files', files);
}

const dropZoneRef = useTemplateRef<HTMLElement>('dropZoneRef');

const { isOverDropZone } = useDropZone(dropZoneRef, {
  dataTypes: [
    'image/png',
    'image/jpeg',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'text/plain',
    'application/rtf',
  ],
  onDrop,
});
</script>

<template>
  <div class="flex flex-col gap-2">
    <div
      ref="dropZoneRef"
      class="flex h-32 w-full cursor-pointer select-none items-center justify-center rounded-lg border-2 border-dashed border-gray-200 transition-all hover:bg-accent hover:text-accent-foreground"
    >
      <div class="flex flex-col items-center gap-1.5">
        <div class="flex flex-row items-center gap-0.5 text-sm font-medium"><Upload class="mr-2 h-4 w-4" /> Upload files</div>
        <div class="text-xs font-medium text-gray-400">Max. file size: {{ (maxSize / (1024 * 1024)).toFixed(2) }} MB</div>
      </div>
    </div>
    <div v-if="errorMessage" class="mt-3 text-xs text-red-600">{{ errorMessage }}</div>
    <FilesList :filesData="filesData" />
  </div>
</template>
