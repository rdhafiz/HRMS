<template>
	<div class="p-4 max-w-4xl">
		<div class="flex items-center justify-between mb-4">
			<h1 class="text-xl font-semibold">Employee Profile</h1>
			<router-link :to="{ name: 'employees' }" class="px-4 py-2 border rounded">Back</router-link>
		</div>

		<div v-if="emp" class="grid grid-cols-1 md:grid-cols-2 gap-4">
			<div class="border rounded p-4">
				<h2 class="font-semibold mb-2">Personal Information</h2>
				<p><span class="text-gray-600">Name:</span> {{ emp.first_name }} {{ emp.last_name }}</p>
				<p><span class="text-gray-600">Email:</span> {{ emp.email }}</p>
				<p><span class="text-gray-600">Phone:</span> {{ emp.phone || '-' }}</p>
				<p><span class="text-gray-600">DOB:</span> {{ emp.date_of_birth || '-' }}</p>
				<p><span class="text-gray-600">Gender:</span> {{ emp.gender || '-' }}</p>
				<p><span class="text-gray-600">Address:</span> {{ emp.address || '-' }}</p>
			</div>
			<div class="border rounded p-4">
				<h2 class="font-semibold mb-2">Professional Information</h2>
				<p><span class="text-gray-600">Employee Code:</span> {{ emp.employee_code }}</p>
				<p><span class="text-gray-600">Join Date:</span> {{ emp.join_date }}</p>
				<p><span class="text-gray-600">Department:</span> {{ emp.department?.name }}</p>
				<p><span class="text-gray-600">Designation:</span> {{ emp.designation?.name }}</p>
				<p><span class="text-gray-600">Status:</span> {{ emp.status }}</p>
				<p><span class="text-gray-600">Salary:</span> {{ emp.salary ?? '-' }}</p>
			</div>
		</div>

		<div v-else class="text-gray-600">Loading...</div>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const emp = ref(null)

const load = async () => {
	const { data } = await axios.get(`/employment/employees/${route.params.id}`)
	emp.value = data
}

onMounted(load)
</script>