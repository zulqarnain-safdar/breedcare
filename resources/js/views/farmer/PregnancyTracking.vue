<template>
    <div class="min-h-screen bg-gradient-to-br from-green-50 via-emerald-50 to-teal-100">
      <!-- Header -->
      <div class="bg-white/80 backdrop-blur-md shadow-lg border-b border-white/20 px-6 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <button @click="$router.go(-1)" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            <h1 class="text-2xl font-bold text-gray-900">Pregnancy Tracking</h1>
          </div>
          <div class="text-sm text-gray-600">
            Track pregnancy outcomes and deliveries
          </div>
        </div>
      </div>
  
      <div class="p-6">
        <div class="max-w-6xl mx-auto">
          <!-- Filter Tabs -->
          <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6 mb-6">
            <div class="flex space-x-1">
              <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="activeTab = tab.id"
                :class="[
                  'px-4 py-2 rounded-lg font-medium transition-colors',
                  activeTab === tab.id
                    ? 'bg-green-600 text-white'
                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
                ]"
              >
                {{ tab.name }}
              </button>
            </div>
          </div>
  
          <!-- Pregnant Cows Tab -->
          <div v-if="activeTab === 'pregnant'" class="space-y-6">
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-6">Pregnant Cows</h2>
              
              <div v-if="pregnantJobs.length > 0" class="space-y-4">
                <div
                  v-for="job in pregnantJobs"
                  :key="job.id"
                  class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200"
                >
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="flex items-center space-x-4 mb-4">
                        <div>
                          <h3 class="text-lg font-semibold text-gray-900">{{ job.order.order_number }}</h3>
                          <p class="text-sm text-gray-600">{{ job.order.cattle_farm.name }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                          <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded-full">
                            Pregnant
                          </span>
                          <span class="text-sm text-gray-500">
                            Since {{ formatDate(job.actual_visit_at) }}
                          </span>
                        </div>
                      </div>
                      
                      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                          <label class="text-sm font-semibold text-gray-500">Technician</label>
                          <p class="text-sm font-medium">{{ job.technician.user.name }}</p>
                        </div>
                        <div>
                          <label class="text-sm font-semibold text-gray-500">Days Pregnant</label>
                          <p class="text-sm font-medium">{{ getDaysPregnant(job.actual_visit_at) }} days</p>
                        </div>
                        <div>
                          <label class="text-sm font-semibold text-gray-500">Expected Delivery</label>
                          <p class="text-sm font-medium">{{ getExpectedDelivery(job.actual_visit_at) }}</p>
                        </div>
                      </div>
  
                      <!-- Action Buttons -->
                      <div class="flex space-x-3">
                        <button
                          @click="markMiscarried(job)"
                          class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors font-medium"
                        >
                          Mark as Miscarried
                        </button>
                        <button
                          @click="markDelivered(job)"
                          :disabled="getDaysPregnant(job.actual_visit_at) < 270"
                          class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors font-medium"
                        >
                          Mark as Delivered
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-else class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No pregnant cows</h3>
                <p class="mt-1 text-sm text-gray-500">No cows are currently marked as pregnant.</p>
              </div>
            </div>
          </div>
  
          <!-- Delivered Cows Tab -->
          <div v-if="activeTab === 'delivered'" class="space-y-6">
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-6">Delivered Cows</h2>
              
              <div v-if="deliveredJobs.length > 0" class="space-y-4">
                <div
                  v-for="job in deliveredJobs"
                  :key="job.id"
                  class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200"
                >
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="flex items-center space-x-4 mb-4">
                        <div>
                          <h3 class="text-lg font-semibold text-gray-900">{{ job.order.order_number }}</h3>
                          <p class="text-sm text-gray-600">{{ job.order.cattle_farm.name }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                          <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded-full">
                            Delivered
                          </span>
                          <span class="text-sm text-gray-500">
                            {{ formatDate(job.updated_at) }}
                          </span>
                        </div>
                      </div>
                      
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                          <label class="text-sm font-semibold text-gray-500">Technician</label>
                          <p class="text-sm font-medium">{{ job.technician.user.name }}</p>
                        </div>
                        <div>
                          <label class="text-sm font-semibold text-gray-500">Delivery Notes</label>
                          <p class="text-sm font-medium">{{ job.delivery_notes || 'No notes' }}</p>
                        </div>
                      </div>
  
                      <!-- Calf Photos -->
                      <div v-if="job.calf_image" class="mb-4">
                        <label class="text-sm font-semibold text-gray-500">Calf Photos</label>
                        <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-4">
                          <img
                            v-for="(image, index) in job.calf_image.split(',')"
                            :key="index"
                            :src="`/storage/${image}`"
                            class="w-full h-24 object-cover rounded-lg"
                            @click="viewImage(`/storage/${image}`)"
                          />
                        </div>
                      </div>
  
                      <!-- Add Calf Photos Button -->
                      <button
                        @click="addCalfPhotos(job)"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                      >
                        Add Calf Photos
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-else class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No delivered cows</h3>
                <p class="mt-1 text-sm text-gray-500">No cows have been marked as delivered yet.</p>
              </div>
            </div>
          </div>
  
          <!-- Failed Pregnancies Tab -->
          <div v-if="activeTab === 'failed'" class="space-y-6">
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-6">Failed Pregnancies</h2>
              
              <div v-if="failedJobs.length > 0" class="space-y-4">
                <div
                  v-for="job in failedJobs"
                  :key="job.id"
                  class="p-6 bg-gradient-to-r from-red-50 to-rose-50 rounded-xl border border-red-200"
                >
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="flex items-center space-x-4 mb-4">
                        <div>
                          <h3 class="text-lg font-semibold text-gray-900">{{ job.order.order_number }}</h3>
                          <p class="text-sm text-gray-600">{{ job.order.cattle_farm.name }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                          <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded-full">
                            {{ job.cow_status === 'miscarried' ? 'Miscarried' : 'Failed' }}
                          </span>
                          <span class="text-sm text-gray-500">
                            {{ formatDate(job.updated_at) }}
                          </span>
                        </div>
                      </div>
                      
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label class="text-sm font-semibold text-gray-500">Technician</label>
                          <p class="text-sm font-medium">{{ job.technician.user.name }}</p>
                        </div>
                        <div>
                          <label class="text-sm font-semibold text-gray-500">Reason</label>
                          <p class="text-sm font-medium">{{ job.delivery_notes || 'No reason provided' }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-else class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No failed pregnancies</h3>
                <p class="mt-1 text-sm text-gray-500">No pregnancies have been marked as failed.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Modals -->
      <!-- Miscarriage Modal -->
      <div v-if="showMiscarriageModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
          <div class="p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Mark as Miscarried</h3>
            <form @submit.prevent="submitMiscarriage">
              <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                <textarea
                  v-model="miscarriageForm.notes"
                  rows="3"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                  placeholder="Any additional notes about the miscarriage..."
                ></textarea>
              </div>
              <div class="flex justify-end space-x-3">
                <button
                  type="button"
                  @click="showMiscarriageModal = false"
                  class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                >
                  Mark as Miscarried
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
  
      <!-- Delivery Modal -->
      <div v-if="showDeliveryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
          <div class="p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Mark as Delivered</h3>
            <form @submit.prevent="submitDelivery">
              <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Delivery Notes</label>
                <textarea
                  v-model="deliveryForm.notes"
                  rows="3"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  placeholder="Any notes about the delivery..."
                ></textarea>
              </div>
              <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Calf Photos (Optional)</label>
                <input
                  type="file"
                  multiple
                  accept="image/*"
                  @change="handleCalfPhotoUpload"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                />
              </div>
              <div class="flex justify-end space-x-3">
                <button
                  type="button"
                  @click="showDeliveryModal = false"
                  class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                >
                  Mark as Delivered
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
  
      <!-- Image Viewer Modal -->
      <div v-if="showImageViewer" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50">
        <div class="max-w-4xl max-h-full">
          <img :src="selectedImage" class="max-w-full max-h-full object-contain rounded-lg" />
          <button
            @click="showImageViewer = false"
            class="absolute top-4 right-4 bg-white bg-opacity-75 hover:bg-opacity-100 rounded-full p-2 transition-colors"
          >
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { ref, computed, onMounted } from 'vue'
  
  export default {
    name: 'PregnancyTracking',
    setup() {
      const activeTab = ref('pregnant')
      const jobs = ref([])
      const showMiscarriageModal = ref(false)
      const showDeliveryModal = ref(false)
      const showImageViewer = ref(false)
      const selectedJob = ref(null)
      const selectedImage = ref('')
      
      const miscarriageForm = ref({
        notes: ''
      })
      
      const deliveryForm = ref({
        notes: '',
        photos: []
      })
  
      const tabs = [
        { id: 'pregnant', name: 'Pregnant' },
        { id: 'delivered', name: 'Delivered' },
        { id: 'failed', name: 'Failed' }
      ]
  
      const pregnantJobs = computed(() => 
        jobs.value.filter(job => job.cow_status === 'pregnant')
      )
  
      const deliveredJobs = computed(() => 
        jobs.value.filter(job => job.cow_status === 'delivered')
      )
  
      const failedJobs = computed(() => 
        jobs.value.filter(job => job.cow_status === 'miscarried' || job.progress_status === 'failed')
      )
  
      const loadJobs = async () => {
        try {
          const response = await window.axios.get('/farmer/jobs')
          jobs.value = response.data.jobs
        } catch (error) {
          console.error('Error loading jobs:', error)
        }
      }
  
      const getDaysPregnant = (visitDate) => {
        const visit = new Date(visitDate)
        const now = new Date()
        return Math.floor((now - visit) / (1000 * 60 * 60 * 24))
      }
  
      const getExpectedDelivery = (visitDate) => {
        const visit = new Date(visitDate)
        const expected = new Date(visit.getTime() + (280 * 24 * 60 * 60 * 1000)) // 280 days
        return expected.toLocaleDateString()
      }
  
      const markMiscarried = (job) => {
        selectedJob.value = job
        showMiscarriageModal.value = true
      }
  
      const markDelivered = (job) => {
        selectedJob.value = job
        showDeliveryModal.value = true
      }
  
      const addCalfPhotos = (job) => {
        selectedJob.value = job
        showDeliveryModal.value = true
      }
  
      const submitMiscarriage = async () => {
        try {
          await window.axios.put(`/farmer/jobs/${selectedJob.value.id}/miscarriage`, {
            notes: miscarriageForm.value.notes
          })
          
          showMiscarriageModal.value = false
          miscarriageForm.value.notes = ''
          loadJobs()
          alert('Marked as miscarried successfully!')
        } catch (error) {
          console.error('Error marking miscarriage:', error)
          alert('Failed to mark as miscarried. Please try again.')
        }
      }
  
      const submitDelivery = async () => {
        try {
          const formData = new FormData()
          formData.append('notes', deliveryForm.value.notes)
          
          deliveryForm.value.photos.forEach((photo, index) => {
            formData.append(`photos[${index}]`, photo)
          })
  
          await window.axios.put(`/farmer/jobs/${selectedJob.value.id}/delivery`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          })
          
          showDeliveryModal.value = false
          deliveryForm.value.notes = ''
          deliveryForm.value.photos = []
          loadJobs()
          alert('Marked as delivered successfully!')
        } catch (error) {
          console.error('Error marking delivery:', error)
          alert('Failed to mark as delivered. Please try again.')
        }
      }
  
      const handleCalfPhotoUpload = (event) => {
        const files = Array.from(event.target.files)
        deliveryForm.value.photos = files
      }
  
      const viewImage = (imageUrl) => {
        selectedImage.value = imageUrl
        showImageViewer.value = true
      }
  
      const formatDate = (date) => {
        return new Date(date).toLocaleDateString()
      }
  
      onMounted(() => {
        loadJobs()
      })
  
      return {
        activeTab,
        jobs,
        showMiscarriageModal,
        showDeliveryModal,
        showImageViewer,
        selectedJob,
        selectedImage,
        miscarriageForm,
        deliveryForm,
        tabs,
        pregnantJobs,
        deliveredJobs,
        failedJobs,
        loadJobs,
        getDaysPregnant,
        getExpectedDelivery,
        markMiscarried,
        markDelivered,
        addCalfPhotos,
        submitMiscarriage,
        submitDelivery,
        handleCalfPhotoUpload,
        viewImage,
        formatDate
      }
    }
  }
  </script>
  