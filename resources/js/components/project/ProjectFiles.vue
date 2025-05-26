<script setup lang="ts">
import Dropzone from '@/components/dropzone/Dropzone.vue';
import FilesList from '@/components/dropzone/FilesList.vue';
import { UploadService, message, progress, uploading } from '@/services/UploadService';
import { Project, ProjectFile } from '@/types';

const props = defineProps<{
  project: Project & {
    files: ProjectFile[];
  };
}>();

console.log(props.project.files);

function upload(idx: number, file: File) {
  // @ts-expect-error It does
  const csrfToken = window.Laravel.csrfToken;

  UploadService.uploadFile(props.project.id, file, csrfToken);
}

function submit(selectedFiles: File[]) {
  for (let i = 0; i < selectedFiles.length; i++) {
    upload(i, selectedFiles[i]);
  }
}
const errorMessage = '';
</script>

<template>
  <Dropzone :errorMessage="errorMessage" @submit:files="submit" />
  <FilesList v-if="project.files.length > 0" :filesData="project.files" />
  <div v-if="uploading">
    Progress: {{ progress }} <br />
    Message: {{ message }} <br />
    Uploading: {{ uploading }} <br />
  </div>
</template>
