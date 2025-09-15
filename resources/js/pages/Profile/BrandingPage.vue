<template>
  <div class="max-w-3xl p-6 space-y-6">
    <div class="text-xl font-semibold">Branding & SEO</div>

    <form @submit.prevent="submit" class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Website Logo</label>
          <div class="flex items-center gap-4">
            <img :src="logoPreview || branding.site_logo_url" alt="logo" class="w-24 h-24 object-contain bg-white border rounded" />
            <input type="file" accept="image/*" @change="onLogo" />
          </div>
          <div v-if="errors.site_logo" class="text-sm text-red-600 mt-1">{{ errors.site_logo[0] }}</div>
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Website Favicon</label>
          <div class="flex items-center gap-4">
            <img :src="faviconPreview || branding.site_favicon_url" alt="favicon" class="w-12 h-12 object-contain bg-white border rounded" />
            <input type="file" accept="image/*,.ico" @change="onFavicon" />
          </div>
          <div v-if="errors.site_favicon" class="text-sm text-red-600 mt-1">{{ errors.site_favicon[0] }}</div>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-4">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Site Title</label>
          <input v-model="form.meta_title" type="text" class="w-full border rounded px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Meta Description</label>
          <textarea v-model="form.meta_description" rows="3" class="w-full border rounded px-3 py-2"></textarea>
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Meta Keywords</label>
          <input v-model="form.meta_keywords" type="text" class="w-full border rounded px-3 py-2" placeholder="e.g. hrm, payroll, attendance" />
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded" :disabled="loading">
          <span v-if="!loading">Save Changes</span>
          <span v-else>Saving...</span>
        </button>
        <router-link class="px-4 py-2 border rounded" :to="{ name: 'dashboard' }">Cancel</router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import axios from 'axios'

const branding = reactive({ site_logo_url: '', site_favicon_url: '' })
const form = reactive({ meta_title: '', meta_description: '', meta_keywords: '' })
const errors = reactive({})
const loading = ref(false)

const logoFile = ref(null)
const faviconFile = ref(null)
const logoPreview = ref('')
const faviconPreview = ref('')

function onLogo(e) {
  const file = e.target.files[0]
  logoFile.value = file || null
  if (file) logoPreview.value = URL.createObjectURL(file)
}

function onFavicon(e) {
  const file = e.target.files[0]
  faviconFile.value = file || null
  if (file) faviconPreview.value = URL.createObjectURL(file)
}

async function load() {
  const { data } = await axios.get('/branding')
  Object.assign(branding, data)
  form.meta_title = data.meta_title || ''
  form.meta_description = data.meta_description || ''
  form.meta_keywords = data.meta_keywords || ''
}

async function submit() {
  loading.value = true
  errors.site_logo = null
  errors.site_favicon = null
  try {
    const payload = new FormData()
    payload.append('meta_title', form.meta_title)
    payload.append('meta_description', form.meta_description)
    payload.append('meta_keywords', form.meta_keywords)
    if (logoFile.value) payload.append('site_logo', logoFile.value)
    if (faviconFile.value) payload.append('site_favicon', faviconFile.value)
    const { data } = await axios.post('/branding', payload, { headers: { 'Content-Type': 'multipart/form-data' } })
    Object.assign(branding, data)
    window.toast && window.toast.success('Branding updated')
  } catch (e) {
    if (e.response?.status === 422) Object.assign(errors, e.response.data.errors || {})
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<style scoped>
</style>


