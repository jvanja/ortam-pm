<script lang="ts" setup>
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { watch, onBeforeUnmount } from 'vue';

const props = defineProps<{
  content: string;
}>();
const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void;
}>();

const editor = useEditor({
  content: props.content,
  extensions: [StarterKit],
  onUpdate: () => {
    emit('update:modelValue', editor.value!.getHTML());
  },
});
onBeforeUnmount(() => {
  editor.value!.destroy();
});

/* ==========================================================================
 Watch for content changes
 ========================================================================== */
watch(() => props.content, (newContent) => (editor.value!.commands.setContent(newContent)));

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
.tiptap {
  font-size: 14px;
}
.tiptap :first-child {
  margin-top: 0;
}
.tiptap h1,
.tiptap h2,
.tiptap h3,
.tiptap h4,
.tiptap h5,
.tiptap h6 {
  line-height: 1.1;
  margin-top: 2.5rem;
  text-wrap: pretty;
  font-weight: bold;
}

.tiptap h1 {
  font-size: 1.4em;
}

.tiptap h2 {
  font-size: 1.2em;
}

.tiptap h3 {
  font-size: 1.1em;
}

.tiptap p {
  margin: 0.5em 0;
}
.tiptap ol,
.tiptap ul,
.tiptap menu {
  padding-left: 1.4rem;
  list-style: auto;
}
</style>
