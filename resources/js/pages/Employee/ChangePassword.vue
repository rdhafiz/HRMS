<template>
	<div class="min-h-screen bg-gray-50 py-8">
		<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
			<!-- Header -->
			<div class="mb-8">
				<h1 class="text-3xl font-bold text-gray-900">Change Password</h1>
				<p class="mt-2 text-gray-600">Update your account password for security</p>
			</div>

			<!-- Change Password Form -->
			<div class="bg-white shadow rounded-lg overflow-hidden">
				<div class="px-6 py-4 border-b border-gray-200">
					<h2 class="text-lg font-semibold text-gray-900 flex items-center">
						<LockClosedIcon class="h-5 w-5 mr-2 text-green-600" />
						Password Security
					</h2>
				</div>

				<form @submit.prevent="submitForm" class="p-6">
					<div class="space-y-6">
						<!-- Current Password -->
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">
								Current Password <span class="text-red-500">*</span>
							</label>
							<div class="relative">
								<input
									v-model="form.current_password"
									:type="showCurrentPassword ? 'text' : 'password'"
									class="mt-1 block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
									:class="{ 'border-red-300': errors.current_password }"
									placeholder="Enter current password"
								/>
								<button
									type="button"
									@click="showCurrentPassword = !showCurrentPassword"
									class="absolute inset-y-0 right-0 pr-3 flex items-center"
								>
									<EyeIcon v-if="!showCurrentPassword" class="h-5 w-5 text-gray-400" />
									<EyeSlashIcon v-else class="h-5 w-5 text-gray-400" />
								</button>
							</div>
							<p v-if="errors.current_password" class="mt-1 text-sm text-red-600">{{ errors.current_password[0] }}</p>
						</div>

						<!-- New Password -->
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">
								New Password <span class="text-red-500">*</span>
							</label>
							<div class="relative">
								<input
									v-model="form.password"
									:type="showNewPassword ? 'text' : 'password'"
									class="mt-1 block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
									:class="{ 'border-red-300': errors.password }"
									placeholder="Enter new password (min 8 characters)"
								/>
								<button
									type="button"
									@click="showNewPassword = !showNewPassword"
									class="absolute inset-y-0 right-0 pr-3 flex items-center"
								>
									<EyeIcon v-if="!showNewPassword" class="h-5 w-5 text-gray-400" />
									<EyeSlashIcon v-else class="h-5 w-5 text-gray-400" />
								</button>
							</div>
							<p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password[0] }}</p>
							<div class="mt-2">
								<div class="text-sm text-gray-600">
									<p>Password requirements:</p>
									<ul class="list-disc list-inside mt-1 space-y-1">
										<li :class="passwordRequirements.length ? 'text-green-600' : 'text-gray-500'">At least 8 characters long</li>
										<li :class="passwordRequirements.uppercase ? 'text-green-600' : 'text-gray-500'">Contains uppercase letter</li>
										<li :class="passwordRequirements.lowercase ? 'text-green-600' : 'text-gray-500'">Contains lowercase letter</li>
										<li :class="passwordRequirements.number ? 'text-green-600' : 'text-gray-500'">Contains number</li>
									</ul>
								</div>
							</div>
						</div>

						<!-- Confirm New Password -->
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">
								Confirm New Password <span class="text-red-500">*</span>
							</label>
							<div class="relative">
								<input
									v-model="form.password_confirmation"
									:type="showConfirmPassword ? 'text' : 'password'"
									class="mt-1 block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
									:class="{ 'border-red-300': errors.password_confirmation }"
									placeholder="Confirm new password"
								/>
								<button
									type="button"
									@click="showConfirmPassword = !showConfirmPassword"
									class="absolute inset-y-0 right-0 pr-3 flex items-center"
								>
									<EyeIcon v-if="!showConfirmPassword" class="h-5 w-5 text-gray-400" />
									<EyeSlashIcon v-else class="h-5 w-5 text-gray-400" />
								</button>
							</div>
							<p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ errors.password_confirmation[0] }}</p>
							<p v-if="form.password && form.password_confirmation && form.password !== form.password_confirmation" class="mt-1 text-sm text-red-600">
								Passwords do not match
							</p>
						</div>
					</div>

					<!-- Security Notice -->
					<div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
						<div class="flex">
							<div class="flex-shrink-0">
								<ExclamationTriangleIcon class="h-5 w-5 text-blue-400" />
							</div>
							<div class="ml-3">
								<h3 class="text-sm font-medium text-blue-800">Security Notice</h3>
								<div class="mt-2 text-sm text-blue-700">
									<p>For your security, please choose a strong password that you haven't used before. After changing your password, you'll need to log in again with your new password.</p>
								</div>
							</div>
						</div>
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
							:disabled="submitting || !isFormValid"
							class="px-4 py-2 bg-green-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
						>
							<span v-if="submitting" class="flex items-center">
								<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
									<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
									<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
								</svg>
								Changing...
							</span>
							<span v-else>Change Password</span>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import {
	LockClosedIcon,
	EyeIcon,
	EyeSlashIcon,
	ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()

// Check if user is Microsoft user and redirect if so
const checkMicrosoftUser = async () => {
	try {
		const { data } = await axios.get('/employee/profile')
		if (data.user?.account_source === 'microsoft_login' && data.user?.microsoft_id !== null) {
			// Redirect Microsoft users to profile page
			router.push({ name: 'employee.profile' })
			return
		}
	} catch (error) {
		console.error('Error checking user type:', error)
		// If there's an error, redirect to profile as well
		router.push({ name: 'employee.profile' })
	}
}

// Check on component mount
onMounted(() => {
	checkMicrosoftUser()
})

const submitting = ref(false)
const errors = ref({})

const form = ref({
	current_password: '',
	password: '',
	password_confirmation: '',
})

const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

// Password requirements validation
const passwordRequirements = computed(() => {
	const password = form.value.password
	return {
		length: password.length >= 8,
		uppercase: /[A-Z]/.test(password),
		lowercase: /[a-z]/.test(password),
		number: /\d/.test(password),
	}
})

// Form validation
const isFormValid = computed(() => {
	return form.value.current_password &&
		   form.value.password &&
		   form.value.password_confirmation &&
		   form.value.password === form.value.password_confirmation &&
		   passwordRequirements.value.length &&
		   passwordRequirements.value.uppercase &&
		   passwordRequirements.value.lowercase &&
		   passwordRequirements.value.number
})

const submitForm = async () => {
	submitting.value = true
	errors.value = {}
	
	try {
		await axios.post('/employee/change-password', form.value)
		
		// Show success message and redirect
		router.push({ name: 'employee.profile' })
	} catch (err) {
		if (err.response && err.response.status === 422) {
			errors.value = err.response.data.errors || {}
		} else {
			console.error('Error changing password:', err)
		}
	} finally {
		submitting.value = false
	}
}

// Clear form when component is mounted
const clearForm = () => {
	form.value = {
		current_password: '',
		password: '',
		password_confirmation: '',
	}
	errors.value = {}
}
</script>
