<script lang="ts" setup>
import Link from '@tiptap/extension-link';
import StarterKit from '@tiptap/starter-kit';
import { BubbleMenu, EditorContent, useEditor } from '@tiptap/vue-3';
import { cn } from '@/lib/utils'

import { onBeforeUnmount } from 'vue';

const props = defineProps<{
  content: string;
  class?: string
}>();
const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void;
}>();

const editor = useEditor({
  content: props.content,
  extensions: [
    StarterKit,
    Link.configure({
      openOnClick: false,
      defaultProtocol: 'https',
    }),
  ],
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
// watch(() => props.content, (newContent) => (editor.value!.commands.setContent(newContent)));

const setLink = () => {
  if (!editor.value) return;

  const previousUrl = editor.value.getAttributes('link').href;
  const url = window.prompt('Please enter your link\'s URL', previousUrl);

  // cancelled
  if (url === null) {
    return;
  }

  // empty
  if (url === '') {
    editor.value.chain().focus().unsetLink().run();
    return;
  }

  // update link
  editor.value.chain().focus().setLink({ href: url }).run();
};
</script>
<template>
  <bubble-menu :editor="editor" :tippy-options="{ duration: 100 }" v-if="editor">
    <div class="bubble-menu bg-gray-50 flex gap-2 text-xs p-2 rounded shadow border border-gray-200">
      <button type="button" @click="editor.chain().focus().toggleBold().run()" :class="{ 'is-active': editor.isActive('bold') }">Bold</button>
      <button type="button" @click="editor.chain().focus().toggleItalic().run()" :class="{ 'is-active': editor.isActive('italic') }">Italic</button>
      <button type="button" @click="editor.chain().focus().toggleStrike().run()" :class="{ 'is-active': editor.isActive('strike') }">Strike</button>
      <button type="button" @click="setLink()" :class="{ 'is-active': editor.isActive('link') }">Link</button>
    </div>
  </bubble-menu>
  <div :class="cn('border-1 min-h-[200px] rounded-lg border border-gray-200 p-4', props.class)">
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

.tiptap a {
  text-decoration: underline;
}

.tiptap ol,
.tiptap ul,
.tiptap menu {
  padding-left: 1.4rem;
  list-style: auto;
}
</style>
