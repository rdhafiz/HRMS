<template>
  <div class="file-upload">
    <div class="upload-area" 
         :class="{ 'drag-over': isDragOver, 'has-files': files.length > 0 }"
         @drop="handleDrop"
         @dragover="handleDragOver"
         @dragleave="handleDragLeave"
         @click="triggerFileInput">
      <input
        ref="fileInput"
        type="file"
        multiple
        :accept="acceptedTypes"
        @change="handleFileSelect"
        class="hidden"
      />
      
      <div v-if="files.length === 0" class="upload-placeholder">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
        </svg>
        <p class="text-lg font-medium text-gray-900 mb-2">Upload files</p>
        <p class="text-sm text-gray-500">Drag and drop files here, or click to select</p>
        <p class="text-xs text-gray-400 mt-2">Supported formats: PDF, DOC, DOCX, JPG, PNG, GIF (Max 10MB each)</p>
      </div>

      <div v-else class="file-list">
        <div v-for="(file, index) in files" :key="index" class="file-item">
          <div class="file-info">
            <div class="file-icon">
              <svg v-if="isImage(file)" class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
              </svg>
              <svg v-else-if="isPdf(file)" class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
              </svg>
              <svg v-else class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div class="file-details">
              <p class="file-name">{{ file.name }}</p>
              <p class="file-size">{{ formatFileSize(file.size) }}</p>
            </div>
          </div>
          <button
            @click.stop="removeFile(index)"
            type="button"
            class="remove-btn"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <div v-if="error" class="error-message">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  maxFiles: {
    type: Number,
    default: 5
  },
  maxSize: {
    type: Number,
    default: 10 * 1024 * 1024 // 10MB
  },
  acceptedTypes: {
    type: String,
    default: '.pdf,.doc,.docx,.jpg,.jpeg,.png,.gif'
  }
})

const emit = defineEmits(['update:modelValue'])

const fileInput = ref(null)
const isDragOver = ref(false)
const error = ref('')

const files = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const triggerFileInput = () => {
  fileInput.value?.click()
}

const handleFileSelect = (event) => {
  const selectedFiles = Array.from(event.target.files)
  addFiles(selectedFiles)
  // Reset input
  event.target.value = ''
}

const handleDrop = (event) => {
  event.preventDefault()
  isDragOver.value = false
  
  const droppedFiles = Array.from(event.dataTransfer.files)
  addFiles(droppedFiles)
}

const handleDragOver = (event) => {
  event.preventDefault()
  isDragOver.value = true
}

const handleDragLeave = (event) => {
  event.preventDefault()
  isDragOver.value = false
}

const addFiles = (newFiles) => {
  error.value = ''
  
  // Check file count limit
  if (files.value.length + newFiles.length > props.maxFiles) {
    error.value = `Maximum ${props.maxFiles} files allowed`
    return
  }

  const validFiles = []
  
  for (const file of newFiles) {
    // Check file size
    if (file.size > props.maxSize) {
      error.value = `File "${file.name}" is too large. Maximum size is ${formatFileSize(props.maxSize)}`
      continue
    }

    // Check file type
    const fileExtension = '.' + file.name.split('.').pop().toLowerCase()
    const acceptedExtensions = props.acceptedTypes.split(',').map(ext => ext.trim())
    
    if (!acceptedExtensions.includes(fileExtension)) {
      error.value = `File "${file.name}" has an unsupported format`
      continue
    }

    validFiles.push(file)
  }

  if (validFiles.length > 0) {
    files.value = [...files.value, ...validFiles]
  }
}

const removeFile = (index) => {
  files.value = files.value.filter((_, i) => i !== index)
  error.value = ''
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const isImage = (file) => {
  return file.type.startsWith('image/')
}

const isPdf = (file) => {
  return file.type === 'application/pdf'
}
</script>

<style scoped>
.file-upload {
  @apply w-full;
}

.upload-area {
  @apply border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer transition-colors duration-200;
}

.upload-area:hover {
  @apply border-gray-400;
}

.upload-area.drag-over {
  @apply border-blue-500 bg-blue-50;
}

.upload-area.has-files {
  @apply border-solid border-gray-300 p-4;
}

.upload-placeholder {
  @apply flex flex-col items-center justify-center;
}

.file-list {
  @apply space-y-2;
}

.file-item {
  @apply flex items-center justify-between p-3 bg-gray-50 rounded-lg border;
}

.file-info {
  @apply flex items-center space-x-3 flex-1;
}

.file-icon {
  @apply flex-shrink-0;
}

.file-details {
  @apply flex-1 min-w-0;
}

.file-name {
  @apply text-sm font-medium text-gray-900 truncate;
}

.file-size {
  @apply text-xs text-gray-500;
}

.remove-btn {
  @apply p-1 text-gray-400 hover:text-red-500 transition-colors duration-200;
}

.error-message {
  @apply mt-2 text-sm text-red-600;
}
</style>
