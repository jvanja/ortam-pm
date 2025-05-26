<script setup lang="ts">
import  Dropzone from '@/components/dropzone/Dropzone.vue';
const errorMessage = '';
</script>

<template>
  <Dropzone :errorMessage="errorMessage" />
</template>
