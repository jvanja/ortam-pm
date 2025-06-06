<script lang="ts" setup>
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { onBeforeUnmount } from 'vue';

const props = defineProps<{
  modelValue: string;
}>();
const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void;
}>();

const editor = useEditor({
  content: props.modelValue,
  extensions: [StarterKit],
  onUpdate: () => {
    // HTML
    console.log(this);
    emit('update:modelValue', editor.value!.getHTML());

    // JSON
    // this.$emit('update:modelValue', this.editor.getJSON())
  },
});
onBeforeUnmount(() => {
  editor.value!.destroy();
});
</script>
<template>
  <div class="border-1 min-h-[200px] rounded-lg border border-gray-200 p-4">
    <editor-content :editor="editor" />
  </div>
</template>
<style lang="css">
.ProseMirror:focus,
.ProseMirror-focused {
  outline: none;
}
</style>
