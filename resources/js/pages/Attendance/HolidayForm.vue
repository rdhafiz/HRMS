<template>
	<div class="p-4 max-w-2xl">
		<h1 class="text-xl font-semibold mb-4">{{ isEdit ? 'Edit Holiday' : 'New Holiday' }}</h1>
		<form @submit.prevent="submit">
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div class="md:col-span-2">
					<label class="block text-sm font-medium">Name</label>
					<input v-model="form.name" type="text" class="border rounded px-3 py-2 w-full" />
					<p v-if="errors.name" class="text-red-600 text-sm mt-1">{{ errors.name[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Department (optional)</label>
					<select v-model="form.department_id" class="border rounded px-3 py-2 w-full">
						<option :value="null">All Departments</option>
						<option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
					</select>
				</div>
				<div>
					<label class="block text-sm font-medium">Start Date</label>
					<input v-model="form.start_date" type="date" class="border rounded px-3 py-2 w-full" />
					<p v-if="errors.start_date" class="text-red-600 text-sm mt-1">{{ errors.start_date[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">End Date</label>
					<input v-model="form.end_date" type="date" class="border rounded px-3 py-2 w-full" />
					<p v-if="errors.end_date" class="text-red-600 text-sm mt-1">{{ errors.end_date[0] }}</p>
				</div>
				<div class="md:col-span-2">
					<label class="block text-sm font-medium">Description</label>
					<textarea v-model="form.description" class="border rounded px-3 py-2 w-full" rows="3"></textarea>
				</div>
			</div>
			<div class="flex justify-end gap-2 mt-6">
				<router-link :to="{ name: 'attendance.holidays' }" class="px-4 py-2 border rounded">Cancel</router-link>
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

const form = ref({ name: '', department_id: null, start_date: '', end_date: '', description: '' })
const errors = ref({})
const departments = ref([])

const loadDepartments = async () => {
	const { data } = await axios.get('/employment/departments')
	departments.value = data.data || data
}

const load = async () => {
	if (!isEdit.value) return
	const { data } = await axios.get('/employment/holidays', { params: { id: route.params.id } })
	const list = data.data || []
	const item = list.find(x => x.id == route.params.id) || data
	form.value = { name: item.name, department_id: item.department_id, start_date: item.start_date, end_date: item.end_date, description: item.description }
}

const submit = async () => {
	errors.value = {}
	try {
		if (isEdit.value) {
			await axios.put(`/employment/holidays/${route.params.id}`, form.value)
		} else {
			await axios.post('/employment/holidays', form.value)
		}
		router.push({ name: 'attendance.holidays' })
	} catch (e) {
		if (e.response && e.response.status === 422) {
			errors.value = e.response.data.errors || {}
		}
	}
}

onMounted(async () => {
	await loadDepartments()
	await load()
})
</script>

