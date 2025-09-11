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
          <h1 class="text-2xl font-bold text-gray-900">Order Cart</h1>
        </div>
        <div class="text-sm text-gray-600">
          Review your semen selection
        </div>
      </div>
    </div>

    <div class="p-6">
      <div class="max-w-4xl mx-auto">
        <!-- Cart Items -->
        <div v-if="cartItems.length > 0" class="space-y-6">
          <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Selected Semen</h2>
            
            <div class="space-y-4">
              <div
                v-for="(item, index) in cartItems"
                :key="index"
                class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl"
              >
                <!-- Semen Info -->
                <div class="flex-1">
                  <h3 class="font-semibold text-gray-900">{{ item.semen.name }}</h3>
                  <p class="text-sm text-gray-600">{{ item.semen.breed.name }} • {{ item.semen.bull_name }}</p>
                  <p class="text-sm text-green-600 font-medium">₨{{ item.semen.price }} per straw</p>
                </div>

                <!-- Quantity Controls -->
                <div class="flex items-center space-x-2">
                  <button
                    @click="updateQuantity(index, item.quantity - 1)"
                    :disabled="item.quantity <= 1"
                    class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 disabled:bg-gray-100 disabled:cursor-not-allowed flex items-center justify-center"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                  </button>
                  <span class="w-8 text-center font-medium">{{ item.quantity }}</span>
                  <button
                    @click="updateQuantity(index, item.quantity + 1)"
                    :disabled="item.quantity >= item.semen.quantity_available"
                    class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 disabled:bg-gray-100 disabled:cursor-not-allowed flex items-center justify-center"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                  </button>
                </div>

                <!-- Total Price -->
                <div class="text-right">
                  <p class="font-semibold text-gray-900">₨{{ (item.semen.price * item.quantity).toFixed(2) }}</p>
                </div>

                <!-- Remove Button -->
                <button
                  @click="removeItem(index)"
                  class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Cow Details Form -->
          <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Cow Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div v-for="(item, index) in cartItems" :key="index" class="space-y-4">
                <h3 class="font-semibold text-gray-900">{{ item.semen.name }}</h3>
                
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cow Type</label>
                    <select
                      v-model="item.cow_type"
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    >
                      <option value="">Select cow type</option>
                      <option value="heifer">Heifer</option>
                      <option value="cow">Cow</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cow Age (months)</label>
                    <input
                      v-model.number="item.cow_age"
                      type="number"
                      min="1"
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                      placeholder="Enter cow age in months"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Heat Start Time</label>
                    <input
                      v-model="item.heat_started_at"
                      type="datetime-local"
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Farm Selection -->
          <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Select Farm</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div
                v-for="farm in farms"
                :key="farm.id"
                @click="selectedFarm = farm.id"
                :class="[
                  'p-4 border-2 rounded-xl cursor-pointer transition-all duration-200',
                  selectedFarm === farm.id
                    ? 'border-green-500 bg-green-50'
                    : 'border-gray-200 hover:border-gray-300'
                ]"
              >
                <h3 class="font-semibold text-gray-900">{{ farm.name }}</h3>
                <p class="text-sm text-gray-600">{{ farm.father_name ? `Father: ${farm.father_name}` : '' }}</p>
                <p class="text-sm text-gray-500">Cows: {{ farm.number_of_cows }}</p>
                <p class="text-sm text-gray-500">Location: {{ farm.latitude }}, {{ farm.longitude }}</p>
              </div>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Order Summary</h2>
            
            <div class="space-y-4">
              <div class="flex justify-between">
                <span class="text-gray-600">Semen Total</span>
                <span class="font-semibold">₨{{ subtotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Visit Fee</span>
                <span class="font-semibold">₨{{ visitFee.toFixed(2) }}</span>
              </div>
              <div class="border-t pt-4">
                <div class="flex justify-between text-lg font-bold">
                  <span>Total</span>
                  <span class="text-green-600">₨{{ total.toFixed(2) }}</span>
                </div>
              </div>
            </div>

            <!-- Notes -->
            <div class="mt-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Additional Notes</label>
              <textarea
                v-model="notes"
                rows="3"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                placeholder="Any additional notes for the technician..."
              ></textarea>
            </div>

            <!-- Place Order Button -->
            <div class="mt-6">
              <button
                @click="placeOrder"
                :disabled="!canPlaceOrder"
                class="w-full bg-green-600 text-white px-6 py-4 rounded-xl hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors font-semibold text-lg"
              >
                Place Order
              </button>
            </div>
          </div>
        </div>

        <!-- Empty Cart -->
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Your cart is empty</h3>
          <p class="mt-1 text-sm text-gray-500">Add some semen to get started.</p>
          <div class="mt-6">
            <button
              @click="$router.push('/farmer/semen-catalog')"
              class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors"
            >
              Browse Semen Catalog
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'OrderCart',
  setup() {
    const router = useRouter()
    const cartItems = ref([])
    const farms = ref([])
    const selectedFarm = ref(null)
    const notes = ref('')
    const visitFee = 50.00

    const subtotal = computed(() => {
      return cartItems.value.reduce((total, item) => {
        return total + (item.semen.price * item.quantity)
      }, 0)
    })

    const total = computed(() => {
      return subtotal.value + visitFee
    })

    const canPlaceOrder = computed(() => {
      if (cartItems.value.length === 0) return false
      if (!selectedFarm.value) return false
      
      return cartItems.value.every(item => 
        item.cow_type && 
        item.cow_age && 
        item.heat_started_at
      )
    })

    const loadFarms = async () => {
      try {
        const response = await window.axios.get('/farmer/farms')
        farms.value = response.data.farms
      } catch (error) {
        console.error('Error loading farms:', error)
      }
    }

    const updateQuantity = (index, newQuantity) => {
      if (newQuantity >= 1 && newQuantity <= cartItems.value[index].semen.quantity_available) {
        cartItems.value[index].quantity = newQuantity
      }
    }

    const removeItem = (index) => {
      cartItems.value.splice(index, 1)
    }

    const placeOrder = async () => {
      try {
        const orderData = {
          cattle_farm_id: selectedFarm.value,
          order_items: cartItems.value.map(item => ({
            bull_semen_id: item.semen.id,
            quantity: item.quantity,
            cow_type: item.cow_type,
            cow_age: item.cow_age,
            heat_started_at: item.heat_started_at
          })),
          notes: notes.value
        }

        const response = await window.axios.post('/orders', orderData)
        
        // Clear cart and redirect
        cartItems.value = []
        router.push('/farmer/dashboard')
        
        // Show success message
        alert('Order placed successfully!')
      } catch (error) {
        console.error('Error placing order:', error)
        alert('Failed to place order. Please try again.')
      }
    }

    onMounted(() => {
      loadFarms()
      // Load cart items from localStorage or props
      const savedCart = localStorage.getItem('farmer_cart')
      if (savedCart) {
        cartItems.value = JSON.parse(savedCart)
      }
    })

    return {
      cartItems,
      farms,
      selectedFarm,
      notes,
      visitFee,
      subtotal,
      total,
      canPlaceOrder,
      updateQuantity,
      removeItem,
      placeOrder
    }
  }
}
</script>

