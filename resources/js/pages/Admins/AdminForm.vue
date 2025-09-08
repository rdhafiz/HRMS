<template>
	<div class="p-4 max-w-2xl">
		<h1 class="text-xl font-semibold mb-4">{{ isEdit ? 'Edit Admin' : 'New Admin' }}</h1>
		<form @submit.prevent="submit" enctype="multipart/form-data">
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div class="md:col-span-2">
					<label class="block text-sm font-medium">Name</label>
					<input v-model="form.name" type="text" class="border rounded px-3 py-2 w-full" />
					<p v-if="errors.name" class="text-red-600 text-sm mt-1">{{ errors.name[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Email</label>
					<input v-model="form.email" type="email" class="border rounded px-3 py-2 w-full" />
					<p v-if="errors.email" class="text-red-600 text-sm mt-1">{{ errors.email[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Password</label>
					<input v-model="form.password" :type="showPassword ? 'text' : 'password'" class="border rounded px-3 py-2 w-full" />
					<p v-if="errors.password" class="text-red-600 text-sm mt-1">{{ errors.password[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Avatar</label>
					<input @change="onFile" type="file" accept="image/*" class="border rounded px-3 py-2 w-full" />
					<p v-if="errors.avatar" class="text-red-600 text-sm mt-1">{{ errors.avatar[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Admin Type</label>
					<select v-model.number="form.admin_type" class="border rounded px-3 py-2 w-full">
						<option :value="1">System Admin</option>
						<option :value="2">HR Manager</option>
						<option :value="3">Supervisor</option>
					</select>
					<p v-if="errors.admin_type" class="text-red-600 text-sm mt-1">{{ errors.admin_type[0] }}</p>
				</div>
			</div>
			<div class="flex justify-end gap-2 mt-6">
				<router-link :to="{ name: 'admins' }" class="px-4 py-2 border rounded">Cancel</router-link>
				<button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">{{ isEdit ? 'Update' : 'Create' }}</button>
			</div>
		</form>
	</div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const isEdit = computed(() => !!route.params.id)
const showPassword = ref(false)

const form = ref({ name: '', email: '', password: '', avatar: null, admin_type: 3 })
const errors = ref({})

const load = async () => {
  if (!isEdit.value) return
  const { data } = await axios.get(`/admins/${route.params.id}`)
  form.value = { name: data.name, email: data.email, password: '', avatar: null, admin_type: data.admin_type }
}

function onFile(e) { form.value.avatar = e.target.files[0] }

const submit = async () => {
  errors.value = {}
  try {
    const fd = new FormData()
    Object.entries(form.value).forEach(([k, v]) => { if (v !== null && v !== '') fd.append(k, v) })
    if (isEdit.value) {
      await axios.post(`/admins/${route.params.id}`, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
    } else {
      await axios.post('/admins', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
    }
    router.push({ name: 'admins' })
  } catch (e) {
    if (e.response && e.response.status === 422) {
      errors.value = e.response.data.errors || {}
    }
  }
}

onMounted(load)
</script>

