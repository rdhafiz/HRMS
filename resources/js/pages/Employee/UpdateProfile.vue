<template>
	<div class="min-h-screen bg-gray-50 py-8">
		<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
			<!-- Header -->
			<div class="mb-8">
				<h1 class="text-3xl font-bold text-gray-900">Update Profile</h1>
				<p class="mt-2 text-gray-600">Update your personal information</p>
			</div>

			<!-- Loading State -->
			<div v-if="loading" class="flex justify-center items-center py-12">
				<div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
			</div>

			<!-- Profile Update Form -->
			<div v-else-if="employee" class="bg-white shadow rounded-lg overflow-hidden">
				<div class="px-6 py-4 border-b border-gray-200">
					<h2 class="text-lg font-semibold text-gray-900 flex items-center">
						<UserIcon class="h-5 w-5 mr-2 text-blue-600" />
						Personal Information
					</h2>
				</div>

				<form @submit.prevent="submitForm" class="p-6">
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
						<!-- First Name -->
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">
								First Name <span class="text-red-500">*</span>
							</label>
							<input
								v-model="form.first_name"
								type="text"
								class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
								:class="{ 'border-red-300': errors.first_name }"
								placeholder="Enter first name"
							/>
							<p v-if="errors.first_name" class="mt-1 text-sm text-red-600">{{ errors.first_name[0] }}</p>
						</div>

						<!-- Last Name -->
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">
								Last Name <span class="text-red-500">*</span>
							</label>
							<input
								v-model="form.last_name"
								type="text"
								class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
								:class="{ 'border-red-300': errors.last_name }"
								placeholder="Enter last name"
							/>
							<p v-if="errors.last_name" class="mt-1 text-sm text-red-600">{{ errors.last_name[0] }}</p>
						</div>

						<!-- Email (Read-only) -->
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
							<input
								v-model="form.email"
								type="email"
								disabled
								class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 sm:text-sm"
								placeholder="Email address"
							/>
							<p class="mt-1 text-sm text-gray-500">Email cannot be changed</p>
						</div>

						<!-- Phone -->
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
							<input
								v-model="form.phone"
								type="text"
								class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
								:class="{ 'border-red-300': errors.phone }"
								placeholder="Enter phone number"
							/>
							<p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone[0] }}</p>
						</div>

						<!-- Date of Birth -->
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
							<flat-pickr
								v-model="form.date_of_birth"
								:config="{ ...dateConfig, maxDate: new Date() }"
								placeholder="Select date of birth"
								class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
								:class="{ 'border-red-300': errors.date_of_birth }"
							/>
							<p v-if="errors.date_of_birth" class="mt-1 text-sm text-red-600">{{ errors.date_of_birth[0] }}</p>
						</div>

						<!-- Gender -->
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
							<select
								v-model="form.gender"
								class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
								:class="{ 'border-red-300': errors.gender }"
							>
								<option :value="null">Select Gender</option>
								<option value="male">Male</option>
								<option value="female">Female</option>
								<option value="other">Other</option>
							</select>
							<p v-if="errors.gender" class="mt-1 text-sm text-red-600">{{ errors.gender[0] }}</p>
						</div>
					</div>

					<!-- Address -->
					<div class="mt-6">
						<label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
						<textarea
							v-model="form.address"
							rows="3"
							class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
							:class="{ 'border-red-300': errors.address }"
							placeholder="Enter your address"
						></textarea>
						<p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address[0] }}</p>
					</div>

					<!-- Action Buttons -->
					<div class="flex justify-end gap-3 mt-8">
						<router-link
							to="/employee/profile"
							class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
						>
							Cancel
						</router-link>
						<button
							type="submit"
							:disabled="submitting"
							class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
						>
							<span v-if="submitting" class="flex items-center">
								<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
									<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
									<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
								</svg>
								Updating...
							</span>
							<span v-else>Update Profile</span>
						</button>
					</div>
				</form>
			</div>

			<!-- Error State -->
			<div v-else-if="error" class="text-center py-12">
				<ExclamationTriangleIcon class="mx-auto h-12 w-12 text-red-400" />
				<h3 class="mt-2 text-sm font-medium text-gray-900">Error loading profile</h3>
				<p class="mt-1 text-sm text-gray-500">{{ error }}</p>
				<div class="mt-6">
					<button
						@click="loadProfile"
						class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
					>
						Try Again
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import { UserIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline'

const router = useRouter()

// Reusable date configuration
const dateConfig = {
	dateFormat: 'Y-m-d',
	altFormat: 'M j, Y',
	altInput: true,
	allowInput: true,
	clickOpens: true,
	defaultDate: null
}

const loading = ref(true)
const submitting = ref(false)
const error = ref(null)
const employee = ref(null)
const errors = ref({})

const form = ref({
	first_name: '',
	last_name: '',
	email: '',
	phone: '',
	date_of_birth: null,
	gender: null,
	address: '',
})

const loadProfile = async () => {
	try {
		loading.value = true
		error.value = null
		
		const { data } = await axios.get('/employee/profile')
		employee.value = data
		
		// Populate form with current data
		form.value = {
			first_name: data.first_name,
			last_name: data.last_name,
			email: data.email,
			phone: data.phone || '',
			date_of_birth: data.date_of_birth || null,
			gender: data.gender,
			address: data.address || '',
		}
	} catch (err) {
		error.value = err.response?.data?.message || 'Failed to load profile'
		console.error('Error loading profile:', err)
	} finally {
		loading.value = false
	}
}

const submitForm = async () => {
	submitting.value = true
	errors.value = {}
	
	try {
		await axios.post('/employee/profile', form.value)
		
		// Show success message and redirect
		router.push({ name: 'employee.profile' })
	} catch (err) {
		if (err.response && err.response.status === 422) {
			errors.value = err.response.data.errors || {}
		} else {
			console.error('Error updating profile:', err)
		}
	} finally {
		submitting.value = false
	}
}

onMounted(() => {
	loadProfile()
})
</script>
