<template>
	<div class="p-4 max-w-3xl">
		<h1 class="text-xl font-semibold mb-4">{{ isEdit ? 'Edit Salary Structure' : 'New Salary Structure' }}</h1>
		<form @submit.prevent="submit">
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div class="md:col-span-2">
					<label class="block text-sm font-medium">Name</label>
					<input v-model="form.name" type="text" class="border rounded px-3 py-2 w-full" />
					<p v-if="errors.name" class="text-red-600 text-sm mt-1">{{ errors.name[0] }}</p>
				</div>
				<div class="md:col-span-2">
					<label class="block text-sm font-medium">Description</label>
					<textarea v-model="form.description" class="border rounded px-3 py-2 w-full" rows="2"></textarea>
				</div>
				<div>
					<label class="inline-flex items-center gap-2"><input type="checkbox" v-model="form.is_template" /> <span>Save as Template</span></label>
				</div>
			</div>

			<div class="mt-6">
				<h2 class="font-semibold mb-2">Components</h2>
				<div class="space-y-2">
					<div v-for="(c, idx) in form.components" :key="idx" class="grid grid-cols-1 md:grid-cols-6 gap-2 items-end">
						<div class="md:col-span-2">
							<label class="block text-sm font-medium">Type</label>
							<select v-model="c.type" class="border rounded px-3 py-2 w-full">
								<option value="basic">Basic</option>
								<option value="allowance">Allowance</option>
								<option value="deduction">Deduction</option>
							</select>
						</div>
						<div class="md:col-span-3">
							<label class="block text-sm font-medium">Name</label>
							<input v-model="c.name" type="text" class="border rounded px-3 py-2 w-full" />
						</div>
						<div>
							<label class="block text-sm font-medium">Amount</label>
							<input v-model.number="c.amount" type="number" step="0.01" min="0" class="border rounded px-3 py-2 w-full" />
						</div>
						<div class="md:col-span-6">
							<button type="button" @click="removeComponent(idx)" class="text-red-600 text-sm">Remove</button>
						</div>
					</div>
				</div>
				<div class="mt-3 flex gap-2">
					<button type="button" @click="addComponent('allowance')" class="px-3 py-1 border rounded">Add Allowance</button>
					<button type="button" @click="addComponent('deduction')" class="px-3 py-1 border rounded">Add Deduction</button>
				</div>
				<p v-if="errors.components" class="text-red-600 text-sm mt-1">{{ errors.components[0] }}</p>
			</div>

			<div class="flex justify-end gap-2 mt-6">
				<router-link :to="{ name: 'payroll.structures' }" class="px-4 py-2 border rounded">Cancel</router-link>
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

const form = ref({ name: '', description: '', is_template: true, components: [{ type: 'basic', name: 'Basic Pay', amount: 0 }] })
const errors = ref({})

function addComponent(type) { form.value.components.push({ type, name: '', amount: 0 }) }
function removeComponent(idx) { form.value.components.splice(idx, 1) }

const load = async () => {
	if (!isEdit.value) return
	const { data } = await axios.get(`/employment/salary-structures/${route.params.id}`)
	form.value = { name: data.name, description: data.description, is_template: data.is_template, components: data.components.map(c => ({ type: c.type, name: c.name, amount: Number(c.amount) })) }
}

const submit = async () => {
	errors.value = {}
	try {
		if (isEdit.value) {
			await axios.put(`/employment/salary-structures/${route.params.id}`, form.value)
		} else {
			await axios.post('/employment/salary-structures', form.value)
		}
		router.push({ name: 'payroll.structures' })
	} catch (e) {
		if (e.response && e.response.status === 422) { errors.value = e.response.data.errors || {} }
	}
}

onMounted(load)
</script>


