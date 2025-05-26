<script setup lang="ts">
import { useDropZone } from '@vueuse/core';
import { FileImage, FileText, Upload } from 'lucide-vue-next';
import { shallowRef, useTemplateRef } from 'vue';

defineProps<{
  errorMessage: string;
}>();

const PDF = `<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' strokeWidth='2' strokeLinecap='round' strokeLinejoin='round'>
    <path d='M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z'/>
    <path d='M14 2v4a2 2 0 0 0 2 2h4'/>
    <path d='M10 9H8'/>
    <path d='M16 13H8'/>
    <path d='M16 17H8'/>
  </svg>`;

const filesData = shallowRef<{ name: string; size: number; type: string; lastModified: number }[]>([]);
const maxSize = 1024 * 1024 * 10;

function onDrop(files: File[] | null) {
  filesData.value = [];
  if (files) {
    filesData.value = files.map((file) => ({
      name: file.name,
      size: file.size,
      type: file.type,
      lastModified: file.lastModified,
    }));
  }
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
    <div v-if="filesData.length > 0" class="mt-3 flex flex-col gap-2">
      <div class="w-full">
        <div
          v-for="(file, index) in filesData"
          :key="index"
          class="mt-2 flex h-16 w-full flex-row items-center justify-between rounded-lg border-2 border-solid border-gray-200 px-4 shadow-sm"
        >
          <div class="flex h-full flex-row items-center gap-4">
            <div v-if="file.type === 'application/pdf'" class="h-6 w-6 text-rose-700" v-html="PDF" />
            <FileImage v-else-if="file.type.includes('image')" class="h-6 w-6 text-rose-700" />
            <FileText v-else-if="file.type.includes('text')" class="h-6 w-6 text-rose-700" />
            <div class="flex flex-col gap-0">
              <div class="text-[0.85rem] font-medium leading-snug">{{ file.name.split('.').slice(0, -1).join('.') }}</div>
              <div class="text-[0.7rem] leading-tight text-gray-500">
                .{{ file.name.split('.').pop() }} • {{ (file.size / (1024 * 1024)).toFixed(2) }} MB
              </div>
            </div>
            <p>Name: {{ file.name }}</p>
            <p>Size: {{ file.size }}</p>
            <p>Type: {{ file.type }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
