<template>
	<div class="p-4 max-w-3xl">
		<h1 class="text-xl font-semibold mb-4">Assign Salary Structure</h1>
		<form @submit.prevent="submit">
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div class="md:col-span-2">
					<label class="block text-sm font-medium">Select Template</label>
					<select v-model="form.salary_structure_id" class="border rounded px-3 py-2 w-full">
						<option :value="null">Select</option>
						<option v-for="s in templates" :key="s.id" :value="s.id">{{ s.name }}</option>
					</select>
					<p v-if="errors.salary_structure_id" class="text-red-600 text-sm mt-1">{{ errors.salary_structure_id[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Effective Date</label>
					<input v-model="form.effective_date" type="date" class="border rounded px-3 py-2 w-full" />
					<p v-if="errors.effective_date" class="text-red-600 text-sm mt-1">{{ errors.effective_date[0] }}</p>
				</div>
			</div>

			<div class="mt-6">
				<h2 class="font-semibold mb-2">Custom Overrides (optional)</h2>
				<textarea v-model="customText" placeholder='{"extra_allowance": 1000}' class="border rounded px-3 py-2 w-full" rows="3"></textarea>
			</div>

			<div class="flex justify-end gap-2 mt-6">
				<button type="button" @click="$router.back()" class="px-4 py-2 border rounded">Cancel</button>
				<button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Assign</button>
			</div>
		</form>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const employeeId = Number(route.params.id)
const templates = ref([])
const customText = ref('')
const errors = ref({})

const form = ref({ employee_id: employeeId, salary_structure_id: null, effective_date: new Date().toISOString().slice(0,10) })

const loadTemplates = async () => {
	const { data } = await axios.get('/employment/salary-structures', { params: { is_template: true } })
	templates.value = data.data || data
}

const submit = async () => {
	errors.value = {}
	try {
		const payload = { ...form.value }
		if (customText.value) {
			try { payload.custom_json = JSON.parse(customText.value) } catch (_) {}
		}
		await axios.post('/employment/employee-salary-structures', payload)
		router.back()
	} catch (e) {
		if (e.response && e.response.status === 422) { errors.value = e.response.data.errors || {} }
	}
}

onMounted(loadTemplates)
</script>


