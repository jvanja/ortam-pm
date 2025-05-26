<script setup lang="ts">
import { FileImage, FileText } from 'lucide-vue-next';

defineProps<{
  filesData: { name: string; size: number; mime_type: string; }[]
}>();

const PDF = `<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' strokeWidth='2' strokeLinecap='round' strokeLinejoin='round'>
    <path d='M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z'/>
    <path d='M14 2v4a2 2 0 0 0 2 2h4'/>
    <path d='M10 9H8'/>
    <path d='M16 13H8'/>
    <path d='M16 17H8'/>
  </svg>`;

const formatSize = (fileSize: number) => {
  return (fileSize / (1024 * 1024)).toFixed(2) + ' MB';
}
</script>
<template>
  <div v-if="filesData.length > 0" class="mt-3 flex flex-col gap-2">
    <div class="w-full">
      <div
        v-for="(file, index) in filesData"
        :key="index"
        class="mt-2 flex h-16 w-full flex-row items-center justify-between rounded-lg border-2 border-solid border-gray-200 px-4 shadow-sm"
      >
        <div class="flex h-full flex-row items-center gap-4">
          <div v-if="file.mime_type === 'application/pdf'" class="h-6 w-6 text-rose-700" v-html="PDF" />
          <FileImage v-else-if="file.mime_type.includes('image')" class="h-6 w-6 text-rose-700" />
          <FileText v-else-if="file.mime_type.includes('text')" class="h-6 w-6 text-rose-700" />
          <div class="flex flex-col gap-0">
            <div class="text-[0.85rem] font-medium leading-snug">{{ file.name.split('.').slice(0, -1).join('.') }}</div>
            <div class="text-[0.7rem] leading-tight text-gray-500">
              .{{ file.name.split('.').pop() }} • {{ formatSize(file.size) }}
            </div>
          </div>
          <div>
            <a :href="'/' + file.path" target="_blank" class="text-blue-500 hover:underline">Download</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
