<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Training & Policies</h2>
      <div class="flex items-center gap-3">
        <router-link 
          to="/training-policy-categories"
          class="flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
          </svg>
          Categories
        </router-link>
        <button 
          v-if="canManage" 
          @click="showCreateModal = true"
          class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Add New
        </button>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="mb-6">
      <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
          <button
            @click="activeTab = 'training'"
            :class="[
              'py-2 px-1 border-b-2 font-medium text-sm transition-colors',
              activeTab === 'training'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
              Trainings
            </div>
          </button>
          <button
            @click="activeTab = 'policy'"
            :class="[
              'py-2 px-1 border-b-2 font-medium text-sm transition-colors',
              activeTab === 'policy'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              Policies
            </div>
          </button>
        </nav>
      </div>
    </div>

    <!-- Content Area -->
    <div class="bg-white rounded-lg shadow">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">
          {{ activeTab === 'training' ? 'Training' : 'Policy' }} Items
        </h3>
      </div>

      <!-- Items List -->
      <div class="p-6">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        </div>

        <!-- Empty State -->
        <div v-else-if="hierarchicalData.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No {{ activeTab === 'training' ? 'trainings' : 'policies' }} found</h3>
          <p class="text-gray-500">Get started by creating your first {{ activeTab === 'training' ? 'training' : 'policy' }}.</p>
        </div>

        <!-- Hierarchical Data -->
        <div v-else class="space-y-4">
          <div 
            v-for="parentCategory in hierarchicalData" 
            :key="parentCategory.id"
            class="border border-gray-200 rounded-lg overflow-hidden"
          >
            <!-- Parent Category Header -->
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-4 py-4 flex items-center justify-between hover:from-indigo-100 hover:to-blue-100 transition-colors">
              <div class="flex items-center gap-3">
                <button
                  @click="toggleCategory(parentCategory.id)"
                  class="p-1 hover:bg-white/50 rounded transition-colors"
                >
                  <svg 
                    class="w-5 h-5 text-indigo-600 transition-transform duration-200"
                    :class="{ 'rotate-90': expandedCategories.includes(parentCategory.id) }"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </button>
                <div class="flex items-center gap-3">
                  <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                  </svg>
                  <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ parentCategory.title }}</h3>
                    <p class="text-sm text-gray-600">
                      {{ getTotalItemsCount(parentCategory) }} {{ getTotalItemsCount(parentCategory) === 1 ? 'item' : 'items' }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Parent Category Content -->
            <Transition
              enter-active-class="transition-all duration-300 ease-out"
              enter-from-class="opacity-0 max-h-0"
              enter-to-class="opacity-100 max-h-screen"
              leave-active-class="transition-all duration-300 ease-in"
              leave-from-class="opacity-100 max-h-screen"
              leave-to-class="opacity-0 max-h-0"
            >
              <div v-show="expandedCategories.includes(parentCategory.id)" class="border-t border-gray-200">
                <!-- Parent Category Items -->
                <div v-if="parentCategory.items && parentCategory.items.length > 0" class="bg-gray-50">
                  <div 
                    v-for="item in parentCategory.items" 
                    :key="`parent-${item.id}`"
                    class="px-8 py-4 flex items-center justify-between hover:bg-gray-100 transition-colors border-b border-gray-200 last:border-b-0"
                  >
                    <div class="flex-1">
                      <div class="flex items-center gap-3">
                        <div class="w-6 h-6 flex items-center justify-center">
                          <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                          </svg>
                        </div>
                        <div class="flex-1">
                          <h4 class="text-sm font-medium text-gray-900">{{ item.title }}</h4>
                          <p v-if="item.description" class="text-sm text-gray-600 mt-1">{{ item.description }}</p>
                          <div class="flex items-center gap-2 mt-2">
                            <span :class="[
                              'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                              item.type === 'training' 
                                ? 'bg-blue-100 text-blue-800' 
                                : 'bg-green-100 text-green-800'
                            ]">
                              {{ item.type === 'training' ? 'Training' : 'Policy' }}
                            </span>
                            <span class="text-xs text-gray-500">{{ formatDate(item.created_at) }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div v-if="canManage" class="flex items-center gap-2 ml-4">
                      <button
                        @click="editItem(item)"
                        class="p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded transition-colors"
                        title="Edit"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                      </button>
                      <button
                        @click="deleteItem(item)"
                        class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded transition-colors"
                        title="Delete"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Child Categories -->
                <div v-if="parentCategory.children && parentCategory.children.length > 0" class="space-y-2">
                  <div 
                    v-for="childCategory in parentCategory.children" 
                    :key="childCategory.id"
                    class="bg-blue-50 border-l-4 border-blue-200"
                  >
                    <!-- Child Category Header -->
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-blue-100 transition-colors">
                      <div class="flex items-center gap-3">
                        <button
                          @click="toggleCategory(childCategory.id)"
                          class="p-1 hover:bg-white/50 rounded transition-colors"
                        >
                          <svg 
                            class="w-4 h-4 text-blue-600 transition-transform duration-200"
                            :class="{ 'rotate-90': expandedCategories.includes(childCategory.id) }"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                          >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                          </svg>
                        </button>
                        <div class="flex items-center gap-2">
                          <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                          </svg>
                          <div>
                            <h4 class="text-sm font-semibold text-gray-800">{{ childCategory.title }}</h4>
                            <p class="text-xs text-gray-600">
                              {{ childCategory.items ? childCategory.items.length : 0 }} {{ (childCategory.items ? childCategory.items.length : 0) === 1 ? 'item' : 'items' }}
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Child Category Items -->
                    <Transition
                      enter-active-class="transition-all duration-300 ease-out"
                      enter-from-class="opacity-0 max-h-0"
                      enter-to-class="opacity-100 max-h-96"
                      leave-active-class="transition-all duration-300 ease-in"
                      leave-from-class="opacity-100 max-h-96"
                      leave-to-class="opacity-0 max-h-0"
                    >
                      <div v-show="expandedCategories.includes(childCategory.id)" class="border-t border-blue-200">
                        <div 
                          v-for="item in childCategory.items" 
                          :key="`child-${item.id}`"
                          class="px-12 py-3 flex items-center justify-between hover:bg-blue-100 transition-colors border-b border-blue-100 last:border-b-0"
                        >
                          <div class="flex-1">
                            <div class="flex items-center gap-3">
                              <div class="w-4 h-4 flex items-center justify-center">
                                <svg class="w-2 h-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                                  <circle cx="4" cy="4" r="2"></circle>
                                </svg>
                              </div>
                              <div class="flex-1">
                                <h5 class="text-sm font-medium text-gray-800">{{ item.title }}</h5>
                                <p v-if="item.description" class="text-xs text-gray-600 mt-1">{{ item.description }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                  <span :class="[
                                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                    item.type === 'training' 
                                      ? 'bg-blue-100 text-blue-800' 
                                      : 'bg-green-100 text-green-800'
                                  ]">
                                    {{ item.type === 'training' ? 'Training' : 'Policy' }}
                                  </span>
                                  <span class="text-xs text-gray-500">{{ formatDate(item.created_at) }}</span>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div v-if="canManage" class="flex items-center gap-2 ml-4">
                            <button
                              @click="editItem(item)"
                              class="p-1.5 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded transition-colors"
                              title="Edit"
                            >
                              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                              </svg>
                            </button>
                            <button
                              @click="deleteItem(item)"
                              class="p-1.5 text-red-600 hover:text-red-900 hover:bg-red-50 rounded transition-colors"
                              title="Delete"
                            >
                              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                              </svg>
                            </button>
                          </div>
                        </div>
                      </div>
                    </Transition>
                  </div>
                </div>
              </div>
            </Transition>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeModal">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ showEditModal ? 'Edit' : 'Create New' }} {{ activeTab === 'training' ? 'Training' : 'Policy' }}
          </h3>
          <form @submit.prevent="saveItem" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
              <input
                v-model="form.title"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Enter title..."
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
              <input
                :value="activeTab === 'training' ? 'Training' : 'Policy'"
                disabled
                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
              <select
                v-model="form.category_id"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
              >
                <option value="">Select a category</option>
                <optgroup v-for="parentCategory in availableCategories" :key="parentCategory.id" :label="parentCategory.title">
                  <option :value="parentCategory.id">{{ parentCategory.title }}</option>
                  <option 
                    v-for="childCategory in parentCategory.children" 
                    :key="childCategory.id" 
                    :value="childCategory.id"
                    class="ml-4"
                  >
                    └─ {{ childCategory.title }}
                  </option>
                </optgroup>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
              <textarea
                v-model="form.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Enter description..."
              ></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-4">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 disabled:opacity-50"
              >
                {{ saving ? 'Saving...' : 'Save' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeDeleteModal">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
        <div class="mt-3">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Delete {{ itemToDelete?.type === 'training' ? 'Training' : 'Policy' }}</h3>
          <p class="text-sm text-gray-500 text-center mb-6">
            Are you sure you want to delete "{{ itemToDelete?.title }}"? This action cannot be undone.
          </p>
          <div class="flex justify-center gap-3">
            <button
              @click="closeDeleteModal"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
            >
              Cancel
            </button>
            <button
              @click="confirmDelete"
              :disabled="deleting"
              class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 disabled:opacity-50"
            >
              {{ deleting ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'

// Reactive data
const items = ref([])
const categories = ref([])
const loading = ref(false)
const saving = ref(false)
const deleting = ref(false)
const activeTab = ref('training')
const me = ref(null)
const expandedCategories = ref([])

// Modal states
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const itemToDelete = ref(null)

// Form data
const form = ref({
  id: null,
  title: '',
  type: 'training',
  category_id: '',
  description: ''
})

// Computed properties
const canManage = computed(() => me.value?.admin_type === 1 || me.value?.admin_type === 2)

const filteredItems = computed(() => {
  return items.value.filter(item => item.type === activeTab.value)
})

const filteredCategories = computed(() => {
  return categories.value.filter(category => category.type === activeTab.value)
})

const availableCategories = computed(() => {
  return hierarchicalData.value
})

const hierarchicalData = computed(() => {
  const parentCategories = filteredCategories.value.filter(category => !category.parent_id)
  
  return parentCategories.map(parentCategory => {
    const children = filteredCategories.value.filter(category => category.parent_id === parentCategory.id)
    
    return {
      ...parentCategory,
      items: filteredItems.value.filter(item => item.category_id === parentCategory.id),
      children: children.map(childCategory => ({
        ...childCategory,
        items: filteredItems.value.filter(item => item.category_id === childCategory.id)
      }))
    }
  }).filter(parentCategory => 
    parentCategory.items.length > 0 || 
    parentCategory.children.some(child => child.items.length > 0)
  )
})

// Methods
async function fetch() {
  loading.value = true
  try {
    // Fetch both items and categories
    const [itemsResponse, categoriesResponse] = await Promise.all([
      axios.get('/employment/trainings-policies', {
        params: { type: activeTab.value }
      }),
      axios.get('/employment/training-policy-categories', {
        params: { type: activeTab.value }
      })
    ])
    
    items.value = itemsResponse.data.data
    categories.value = categoriesResponse.data.data
    
    // Auto-expand all parent categories by default
    expandedCategories.value = hierarchicalData.value.map(cat => cat.id)
  } catch (error) {
    console.error('Error fetching data:', error)
  } finally {
    loading.value = false
  }
}

function toggleCategory(categoryId) {
  const index = expandedCategories.value.indexOf(categoryId)
  if (index > -1) {
    expandedCategories.value.splice(index, 1)
  } else {
    expandedCategories.value.push(categoryId)
  }
}

function getTotalItemsCount(parentCategory) {
  const parentItems = parentCategory.items ? parentCategory.items.length : 0
  const childItems = parentCategory.children ? 
    parentCategory.children.reduce((total, child) => total + (child.items ? child.items.length : 0), 0) : 0
  return parentItems + childItems
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

function editItem(item) {
  form.value = {
    id: item.id,
    title: item.title,
    type: item.type,
    category_id: item.category_id || '',
    description: item.description || ''
  }
  showEditModal.value = true
}

function deleteItem(item) {
  itemToDelete.value = item
  showDeleteModal.value = true
}

async function saveItem() {
  saving.value = true
  try {
    const payload = {
      title: form.value.title,
      type: activeTab.value,
      category_id: form.value.category_id,
      description: form.value.description
    }

    if (form.value.id) {
      // Update existing item
      await axios.put(`/employment/trainings-policies/${form.value.id}`, payload)
    } else {
      // Create new item
      await axios.post('/employment/trainings-policies', payload)
    }
    closeModal()
    fetch()
  } catch (error) {
    console.error('Error saving item:', error)
  } finally {
    saving.value = false
  }
}

async function confirmDelete() {
  deleting.value = true
  try {
    await axios.delete(`/employment/trainings-policies/${itemToDelete.value.id}`)
    closeDeleteModal()
    fetch()
  } catch (error) {
    console.error('Error deleting item:', error)
  } finally {
    deleting.value = false
  }
}

function closeModal() {
  showCreateModal.value = false
  showEditModal.value = false
  form.value = {
    id: null,
    title: '',
    type: 'training',
    category_id: '',
    description: ''
  }
}

function closeDeleteModal() {
  showDeleteModal.value = false
  itemToDelete.value = null
}

async function loadMe() {
  try {
    const { data } = await axios.get('/auth/user')
    me.value = data
  } catch (error) {
    console.error('Error loading user:', error)
  }
}

// Watchers
watch(activeTab, () => {
  fetch()
})

// Lifecycle
onMounted(async () => {
  await loadMe()
  fetch()
})
</script>

