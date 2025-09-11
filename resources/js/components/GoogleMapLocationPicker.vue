<template>
  <div class="google-map-location-picker">
    <div class="mb-4">
      <label class="block text-sm font-semibold text-gray-700 mb-2">
        Select Farm Location
      </label>
      <div class="flex space-x-2 mb-2">
        <button
          @click="getCurrentLocation"
          type="button"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          Use Current Location
        </button>
        <button
          @click="searchLocation"
          type="button"
          class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          Search Location
        </button>
      </div>
      <input
        v-model="searchQuery"
        @keyup.enter="searchLocation"
        type="text"
        placeholder="Search for a location..."
        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
      />
    </div>
    
    <div class="relative">
      <div ref="mapContainer" class="w-full h-96 rounded-xl border border-gray-300"></div>
      <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75 rounded-xl">
        <div class="flex items-center space-x-2">
          <svg class="animate-spin h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span class="text-gray-600">Loading map...</span>
        </div>
      </div>
    </div>
    
    <div class="mt-4 grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Latitude</label>
        <input
          v-model="coordinates.lat"
          type="number"
          step="any"
          readonly
          class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-gray-600"
        />
      </div>
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Longitude</label>
        <input
          v-model="coordinates.lng"
          type="number"
          step="any"
          readonly
          class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-gray-600"
        />
      </div>
    </div>
    
    <div v-if="selectedAddress" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-xl">
      <p class="text-sm text-green-800">
        <strong>Selected Location:</strong> {{ selectedAddress }}
      </p>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue'

export default {
  name: 'GoogleMapLocationPicker',
  props: {
    modelValue: {
      type: Object,
      default: () => ({ lat: null, lng: null })
    },
    initialLat: {
      type: Number,
      default: 31.5204 // Default to Lahore, Pakistan
    },
    initialLng: {
      type: Number,
      default: 74.3587
    }
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    const mapContainer = ref(null)
    const loading = ref(true)
    const searchQuery = ref('')
    const selectedAddress = ref('')
    const coordinates = ref({
      lat: props.modelValue.lat || props.initialLat,
      lng: props.modelValue.lng || props.initialLng
    })
    
    let map = null
    let marker = null
    let geocoder = null
    let placesService = null
    let searchBox = null

    // Initialize Google Maps
    const initMap = () => {
      if (!window.google) {
        console.error('Google Maps API not loaded')
        loading.value = false
        return
      }

      const mapOptions = {
        center: { lat: coordinates.value.lat, lng: coordinates.value.lng },
        zoom: 15,
        mapTypeId: 'roadmap',
        styles: [
          {
            featureType: 'poi',
            elementType: 'labels',
            stylers: [{ visibility: 'off' }]
          }
        ]
      }

      map = new google.maps.Map(mapContainer.value, mapOptions)
      geocoder = new google.maps.Geocoder()
      placesService = new google.maps.places.PlacesService(map)

      // Create marker
      marker = new google.maps.Marker({
        position: { lat: coordinates.value.lat, lng: coordinates.value.lng },
        map: map,
        draggable: true,
        title: 'Farm Location'
      })

      // Add click listener to map
      map.addListener('click', (event) => {
        const lat = event.latLng.lat()
        const lng = event.latLng.lng()
        updateLocation(lat, lng)
      })

      // Add drag listener to marker
      marker.addListener('dragend', (event) => {
        const lat = event.latLng.lat()
        const lng = event.latLng.lng()
        updateLocation(lat, lng)
      })

      // Initialize search box
      const searchInput = document.querySelector('input[placeholder="Search for a location..."]')
      if (searchInput) {
        searchBox = new google.maps.places.SearchBox(searchInput)
        searchBox.addListener('places_changed', handleSearchResults)
      }

      loading.value = false
    }

    // Update location coordinates and address
    const updateLocation = (lat, lng) => {
      coordinates.value.lat = parseFloat(lat.toFixed(6))
      coordinates.value.lng = parseFloat(lng.toFixed(6))
      
      // Update marker position
      if (marker) {
        marker.setPosition({ lat, lng })
      }
      
      // Update map center
      if (map) {
        map.setCenter({ lat, lng })
      }
      
      // Get address from coordinates
      getAddressFromCoordinates(lat, lng)
      
      // Emit updated coordinates
      emit('update:modelValue', { lat: coordinates.value.lat, lng: coordinates.value.lng })
    }

    // Get address from coordinates
    const getAddressFromCoordinates = (lat, lng) => {
      if (!geocoder) return
      
      geocoder.geocode({ location: { lat, lng } }, (results, status) => {
        if (status === 'OK' && results[0]) {
          selectedAddress.value = results[0].formatted_address
        }
      })
    }

    // Get current location using geolocation
    const getCurrentLocation = () => {
      if (!navigator.geolocation) {
        alert('Geolocation is not supported by this browser.')
        return
      }

      loading.value = true
      navigator.geolocation.getCurrentPosition(
        (position) => {
          const lat = position.coords.latitude
          const lng = position.coords.longitude
          updateLocation(lat, lng)
          loading.value = false
        },
        (error) => {
          console.error('Error getting location:', error)
          alert('Unable to retrieve your location. Please try again or select manually.')
          loading.value = false
        },
        {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 300000
        }
      )
    }

    // Search for location
    const searchLocation = () => {
      if (!searchQuery.value.trim()) return
      
      if (!geocoder) return
      
      loading.value = true
      geocoder.geocode({ address: searchQuery.value }, (results, status) => {
        if (status === 'OK' && results[0]) {
          const location = results[0].geometry.location
          const lat = location.lat()
          const lng = location.lng()
          updateLocation(lat, lng)
          selectedAddress.value = results[0].formatted_address
        } else {
          alert('Location not found. Please try a different search term.')
        }
        loading.value = false
      })
    }

    // Handle search results from search box
    const handleSearchResults = () => {
      const places = searchBox.getPlaces()
      if (places.length === 0) return

      const place = places[0]
      if (place.geometry && place.geometry.location) {
        const lat = place.geometry.location.lat()
        const lng = place.geometry.location.lng()
        updateLocation(lat, lng)
        selectedAddress.value = place.formatted_address || place.name
      }
    }

    // Watch for prop changes
    watch(() => props.modelValue, (newValue) => {
      if (newValue && newValue.lat && newValue.lng) {
        coordinates.value.lat = newValue.lat
        coordinates.value.lng = newValue.lng
        if (map && marker) {
          updateLocation(newValue.lat, newValue.lng)
        }
      }
    }, { deep: true })

    // Load Google Maps API
    const loadGoogleMapsAPI = () => {
      if (window.google && window.google.maps) {
        initMap()
        return
      }

      const script = document.createElement('script')
      script.src = `https://maps.googleapis.com/maps/api/js?key=${import.meta.env.VITE_GOOGLE_MAPS_API_KEY}&libraries=places&callback=initGoogleMaps`
      script.async = true
      script.defer = true
      
      window.initGoogleMaps = initMap
      document.head.appendChild(script)
    }

    onMounted(() => {
      nextTick(() => {
        loadGoogleMapsAPI()
      })
    })

    onUnmounted(() => {
      if (window.initGoogleMaps) {
        delete window.initGoogleMaps
      }
    })

    return {
      mapContainer,
      loading,
      searchQuery,
      selectedAddress,
      coordinates,
      getCurrentLocation,
      searchLocation
    }
  }
}
</script>

<style scoped>
.google-map-location-picker {
  @apply w-full;
}

/* Custom marker styles */
:deep(.gm-style-iw) {
  border-radius: 8px;
}

:deep(.gm-style-iw-c) {
  border-radius: 8px;
}

/* Hide Google Maps UI elements we don't need */
:deep(.gm-style-cc) {
  display: none;
}

:deep(.gm-style .gm-style-iw-d) {
  overflow: hidden !important;
}
</style>
