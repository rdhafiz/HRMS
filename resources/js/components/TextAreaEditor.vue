<template>
  <div class="textarea-editor">
    <div class="toolbar" v-if="showToolbar">
      <div class="toolbar-group">
        <button
          @click="insertFormat('**', '**')"
          type="button"
          class="toolbar-btn"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M5 4a1 1 0 011-1h5.5a2.5 2.5 0 011.5 4.5A2.5 2.5 0 0111.5 12H6a1 1 0 01-1-1V4zM6 3a2 2 0 00-2 2v6a2 2 0 002 2h5.5a3.5 3.5 0 002.5-5.5A3.5 3.5 0 0011.5 3H6z"/>
          </svg>
        </button>
        <button
          @click="insertFormat('*', '*')"
          type="button"
          class="toolbar-btn"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M8 3a1 1 0 000 2h1.5l-2 8H6a1 1 0 100 2h4a1 1 0 100-2h-1.5l2-8H12a1 1 0 100-2H8z"/>
          </svg>
        </button>
        <button
          @click="insertFormat('~~', '~~')"
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
          @click="insertFormat('# ', '')"
          type="button"
          class="toolbar-btn"
        >
          H1
        </button>
        <button
          @click="insertFormat('## ', '')"
          type="button"
          class="toolbar-btn"
        >
          H2
        </button>
        <button
          @click="insertFormat('### ', '')"
          type="button"
          class="toolbar-btn"
        >
          H3
        </button>
      </div>

      <div class="toolbar-group">
        <button
          @click="insertList('• ')"
          type="button"
          class="toolbar-btn"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 4a1 1 0 000 2h1a1 1 0 000-2H3zM3 8a1 1 0 000 2h1a1 1 0 000-2H3zM3 12a1 1 0 000 2h1a1 1 0 000-2H3z"/>
          </svg>
        </button>
        <button
          @click="insertList('1. ')"
          type="button"
          class="toolbar-btn"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 4a1 1 0 000 2h1a1 1 0 000-2H3zM3 8a1 1 0 000 2h1a1 1 0 000-2H3zM3 12a1 1 0 000 2h1a1 1 0 000-2H3z"/>
          </svg>
        </button>
      </div>
    </div>

    <div class="editor-content">
      <textarea
        ref="textarea"
        v-model="content"
        :placeholder="placeholder"
        class="w-full min-h-[200px] p-4 border-0 resize-none focus:outline-none"
        @input="handleInput"
      ></textarea>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue'

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

const textarea = ref(null)
const content = ref(props.modelValue)

const handleInput = () => {
  emit('update:modelValue', content.value)
}

const insertFormat = (before, after) => {
  const textareaEl = textarea.value
  const start = textareaEl.selectionStart
  const end = textareaEl.selectionEnd
  const selectedText = content.value.substring(start, end)
  
  const newText = before + selectedText + after
  const newContent = content.value.substring(0, start) + newText + content.value.substring(end)
  
  content.value = newContent
  
  nextTick(() => {
    textareaEl.focus()
    textareaEl.setSelectionRange(start + before.length, start + before.length + selectedText.length)
  })
}

const insertList = (prefix) => {
  const textareaEl = textarea.value
  const start = textareaEl.selectionStart
  const end = textareaEl.selectionEnd
  const selectedText = content.value.substring(start, end)
  
  const lines = selectedText.split('\n')
  const formattedLines = lines.map(line => line.trim() ? prefix + line : line)
  const newText = formattedLines.join('\n')
  
  const newContent = content.value.substring(0, start) + newText + content.value.substring(end)
  content.value = newContent
  
  nextTick(() => {
    textareaEl.focus()
    textareaEl.setSelectionRange(start, start + newText.length)
  })
}

watch(() => props.modelValue, (newValue) => {
  if (content.value !== newValue) {
    content.value = newValue
  }
})
</script>

<style scoped>
.textarea-editor {
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

.editor-content {
  @apply min-h-[200px];
}
</style>
