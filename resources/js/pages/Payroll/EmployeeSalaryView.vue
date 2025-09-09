<template>
	<div class="p-4 max-w-3xl">
		<h1 class="text-xl font-semibold mb-4">Employee Salary</h1>
		<div v-if="item" class="space-y-4">
			<div class="border rounded p-4">
				<div class="font-semibold mb-2">Structure: {{ item.structure?.name }}</div>
				<div class="text-sm text-gray-600 mb-2">Effective: {{ item.effective_date }}</div>
				<div class="space-y-1">
					<div v-for="c in item.structure?.components || []" :key="c.id" class="flex justify-between text-sm">
						<span>{{ c.type }} - {{ c.name }}</span>
						<span>{{ c.amount }}</span>
					</div>
				</div>
			</div>
			<div v-if="item.custom_json" class="border rounded p-4">
				<div class="font-semibold mb-2">Custom Overrides</div>
				<pre class="text-xs bg-slate-50 p-2 rounded overflow-auto">{{ JSON.stringify(item.custom_json, null, 2) }}</pre>
			</div>
		</div>
		<div v-else class="text-gray-500">No salary structure assigned.</div>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const item = ref(null)

const load = async () => {
	try {
		const { data } = await axios.get(`/employment/employee-salary-structures/${route.params.id}`)
		item.value = data
	} catch (_) {
		item.value = null
	}
}

onMounted(load)
</script>


