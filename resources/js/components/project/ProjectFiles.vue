<script setup lang="ts">
import Dropzone from '@/components/dropzone/Dropzone.vue';
import FilesList from '@/components/dropzone/FilesList.vue';
import { UploadService, message, progress, uploading } from '@/services/UploadService';
import { ProjectFile, Project } from '@/types';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
  project: Project & {
    files: ProjectFile[];
  };
}>();

console.log(props.project.files);
const form = useForm({
  name: null,
  avatar: null,
});

function upload(idx: number, file: File) {
  // @ts-expect-error It does
  const csrfToken = window.Laravel.csrfToken;

  UploadService.uploadFile(props.project.id, file, csrfToken);
}

function submit(selectedFiles: File[]) {
  // form.post('/files/project/123/store')
  console.log(selectedFiles);
  for (let i = 0; i < selectedFiles.length; i++) {
    upload(i, selectedFiles[i]);
  }
}
const errorMessage = '';
</script>

<template>
  <Dropzone :errorMessage="errorMessage" @submit:files="submit" />
  <FilesList v-if="project.files.length > 0" :filesData="project.files" />
  Progress: {{ progress }} <br />
  Message: {{ message }} <br />
  Uploading: {{ uploading }} <br />
</template>
