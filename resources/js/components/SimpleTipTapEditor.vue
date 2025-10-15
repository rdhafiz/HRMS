<template>
  <div class="tiptap-editor">
    <div class="toolbar" v-if="showToolbar">
      <div class="toolbar-group">
        <button
          @click="editor.chain().focus().toggleBold().run()"
          :class="{ 'is-active': editor?.isActive('bold') }"
          type="button"
          class="toolbar-btn"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M5 4a1 1 0 011-1h5.5a2.5 2.5 0 011.5 4.5A2.5 2.5 0 0111.5 12H6a1 1 0 01-1-1V4zM6 3a2 2 0 00-2 2v6a2 2 0 002 2h5.5a3.5 3.5 0 002.5-5.5A3.5 3.5 0 0011.5 3H6z"/>
          </svg>
        </button>
        <button
          @click="editor.chain().focus().toggleItalic().run()"
          :class="{ 'is-active': editor?.isActive('italic') }"
          type="button"
          class="toolbar-btn"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M8 3a1 1 0 000 2h1.5l-2 8H6a1 1 0 100 2h4a1 1 0 100-2h-1.5l2-8H12a1 1 0 100-2H8z"/>
          </svg>
        </button>
        <button
          @click="editor.chain().focus().toggleStrike().run()"
          :class="{ 'is-active': editor?.isActive('strike') }"
          type="button"
          class="toolbar-btn"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
          </svg>
        </button>
      </div>

      <div class="toolbar-group">
        <button
          @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
          :class="{ 'is-active': editor?.isActive('heading', { level: 1 }) }"
          type="button"
          class="toolbar-btn"
        >
          H1
        </button>
        <button
          @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
          :class="{ 'is-active': editor?.isActive('heading', { level: 2 }) }"
          type="button"
          class="toolbar-btn"
        >
          H2
        </button>
        <button
          @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
          :class="{ 'is-active': editor?.isActive('heading', { level: 3 }) }"
          type="button"
          class="toolbar-btn"
        >
          H3
        </button>
      </div>

      <div class="toolbar-group">
        <button
          @click="editor.chain().focus().toggleBulletList().run()"
          :class="{ 'is-active': editor?.isActive('bulletList') }"
          type="button"
          class="toolbar-btn"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 4a1 1 0 000 2h1a1 1 0 000-2H3zM3 8a1 1 0 000 2h1a1 1 0 000-2H3zM3 12a1 1 0 000 2h1a1 1 0 000-2H3z"/>
          </svg>
        </button>
        <button
          @click="editor.chain().focus().toggleOrderedList().run()"
          :class="{ 'is-active': editor?.isActive('orderedList') }"
          type="button"
          class="toolbar-btn"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 4a1 1 0 000 2h1a1 1 0 000-2H3zM3 8a1 1 0 000 2h1a1 1 0 000-2H3zM3 12a1 1 0 000 2h1a1 1 0 000-2H3z"/>
          </svg>
        </button>
      </div>

      <div class="toolbar-group">
        <button
          @click="editor.chain().focus().toggleCodeBlock().run()"
          :class="{ 'is-active': editor?.isActive('codeBlock') }"
          type="button"
          class="toolbar-btn"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>
    </div>

    <div class="editor-content">
      <editor-content :editor="editor" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Start typing...'
  },
  showToolbar: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
  content: props.modelValue,
  extensions: [
    StarterKit,
  ],
  onUpdate: ({ editor }) => {
    emit('update:modelValue', editor.getHTML())
  },
  editorProps: {
    attributes: {
      class: 'prose prose-sm sm:prose lg:prose-lg xl:prose-2xl mx-auto focus:outline-none min-h-[200px] p-4',
    },
  },
})

watch(() => props.modelValue, (newValue) => {
  if (editor.value && editor.value.getHTML() !== newValue) {
    editor.value.commands.setContent(newValue, false)
  }
})

onBeforeUnmount(() => {
  editor.value?.destroy()
})
</script>

<style scoped>
.tiptap-editor {
  @apply border border-gray-300 rounded-lg overflow-hidden;
}

.toolbar {
  @apply flex flex-wrap gap-1 p-2 bg-gray-50 border-b border-gray-300;
}

.toolbar-group {
  @apply flex gap-1 mr-4;
}

.toolbar-btn {
  @apply px-2 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500;
}

.toolbar-btn.is-active {
  @apply bg-blue-100 text-blue-700 border-blue-300;
}

.editor-content {
  @apply min-h-[200px];
}

:deep(.ProseMirror) {
  @apply outline-none p-4 min-h-[200px];
}

:deep(.ProseMirror p.is-editor-empty:first-child::before) {
  @apply text-gray-400 float-left h-0 pointer-events-none;
  content: attr(data-placeholder);
}

:deep(.ProseMirror h1) {
  @apply text-2xl font-bold mb-4;
}

:deep(.ProseMirror h2) {
  @apply text-xl font-bold mb-3;
}

:deep(.ProseMirror h3) {
  @apply text-lg font-bold mb-2;
}

:deep(.ProseMirror ul) {
  @apply list-disc list-inside mb-4;
}

:deep(.ProseMirror ol) {
  @apply list-decimal list-inside mb-4;
}

:deep(.ProseMirror li) {
  @apply mb-1;
}

:deep(.ProseMirror a) {
  @apply text-blue-600 underline;
}

:deep(.ProseMirror strong) {
  @apply font-bold;
}

:deep(.ProseMirror em) {
  @apply italic;
}

:deep(.ProseMirror s) {
  @apply line-through;
}

:deep(.ProseMirror code) {
  @apply bg-gray-100 px-1 py-0.5 rounded text-sm font-mono;
}

:deep(.ProseMirror pre) {
  @apply bg-gray-100 p-4 rounded-lg overflow-x-auto;
}

:deep(.ProseMirror pre code) {
  @apply bg-transparent p-0;
}
</style>
