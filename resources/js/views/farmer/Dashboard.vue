<template>
  <div class="min-h-screen bg-gradient-to-br from-green-50 via-emerald-50 to-teal-100 flex">
    <!-- Sidebar -->
    <div class="w-64 bg-white/80 backdrop-blur-md shadow-xl border-r border-white/20 flex-shrink-0">
      <!-- Logo & Header -->
      <div class="p-6 border-b border-white/20">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-lg font-bold text-gray-900">Farmer Panel</h1>
            <p class="text-xs text-gray-600">BreedCare Platform</p>
          </div>
        </div>
      </div>

    <!-- Navigation -->
      <nav class="p-4 space-y-2">
          <button 
            v-for="tab in tabs" 
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
            'w-full flex items-center space-x-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group',
              activeTab === tab.id 
              ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white shadow-lg' 
              : 'text-gray-600 hover:text-gray-900 hover:bg-white/60'
          ]"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path v-if="tab.id === 'overview'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            <path v-else-if="tab.id === 'farms'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            <path v-else-if="tab.id === 'orders'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            <path v-else-if="tab.id === 'pregnancy'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            <path v-else-if="tab.id === 'profile'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          <span>{{ tab.name }}</span>
          </button>
      </nav>

      <!-- User Info & Logout -->
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/20">
        <div class="flex items-center space-x-3 mb-4">
          <div class="w-8 h-8 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full flex items-center justify-center">
            <span class="text-white text-sm font-semibold">{{ farmer?.name?.charAt(0) }}</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">{{ farmer?.name }}</p>
            <p class="text-xs text-gray-500">Farmer</p>
          </div>
        </div>
        <button @click="logout" class="w-full flex items-center space-x-3 px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 rounded-xl transition-all duration-200">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
          </svg>
          <span>Logout</span>
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Top Header -->
      <header class="bg-white/60 backdrop-blur-md shadow-lg border-b border-white/20 px-6 py-4">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold text-gray-900">{{ tabs.find(t => t.id === activeTab)?.name }}</h2>
          <div class="text-sm text-gray-600">
            {{ new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
          </div>
        </div>
      </header>

      <!-- Content Area -->
      <main class="flex-1 p-6 overflow-y-auto">
      <!-- Overview Tab -->
      <div v-if="activeTab === 'overview'" class="space-y-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Total Orders Card -->
          <div class="group bg-white/70 backdrop-blur-sm overflow-hidden shadow-xl rounded-2xl border border-white/20 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
            <div class="p-6">
              <div class="flex items-center justify-between">
                <div class="flex-shrink-0">
                  <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-600 mb-1">Total Orders</p>
                  <p class="text-3xl font-bold text-gray-900">{{ orders.length }}</p>
                  <div class="flex items-center text-green-600 text-sm mt-1">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    All Time
                  </div>
                </div>
              </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-blue-500 to-blue-600"></div>
          </div>

          <!-- Farms Card -->
          <div class="group bg-white/70 backdrop-blur-sm overflow-hidden shadow-xl rounded-2xl border border-white/20 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
            <div class="p-6">
              <div class="flex items-center justify-between">
                <div class="flex-shrink-0">
                  <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-600 mb-1">My Farms</p>
                  <p class="text-3xl font-bold text-gray-900">{{ farms.length }}</p>
                  <div class="flex items-center text-green-600 text-sm mt-1">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Active
                  </div>
                </div>
              </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-green-500 to-emerald-600"></div>
          </div>

          <!-- Pending Orders Card -->
          <div class="group bg-white/70 backdrop-blur-sm overflow-hidden shadow-xl rounded-2xl border border-white/20 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
            <div class="p-6">
              <div class="flex items-center justify-between">
                <div class="flex-shrink-0">
                  <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-600 mb-1">Pending Orders</p>
                  <p class="text-3xl font-bold text-gray-900">{{ pendingOrdersCount }}</p>
                  <div class="flex items-center text-amber-600 text-sm mt-1">
                    <div class="w-2 h-2 bg-amber-500 rounded-full mr-2 animate-pulse"></div>
                    In Progress
                  </div>
                </div>
              </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-amber-500 to-orange-600"></div>
          </div>

          <!-- Completed Orders Card -->
          <div class="group bg-white/70 backdrop-blur-sm overflow-hidden shadow-xl rounded-2xl border border-white/20 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
            <div class="p-6">
              <div class="flex items-center justify-between">
                <div class="flex-shrink-0">
                  <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-violet-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-600 mb-1">Completed Orders</p>
                  <p class="text-3xl font-bold text-gray-900">{{ completedOrdersCount }}</p>
                  <div class="flex items-center text-green-600 text-sm mt-1">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Success
                  </div>
                </div>
              </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-purple-500 to-violet-600"></div>
          </div>
        </div>

        <!-- Current Technician Assignments -->
        <div class="bg-white/70 backdrop-blur-sm shadow-xl rounded-2xl border border-white/20 overflow-hidden">
          <div class="px-6 py-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-white/20">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-bold text-gray-900 flex items-center">
                <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Current Assignments
              </h3>
              <button @click="activeTab = 'orders'" class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center">
                View All Orders
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
          </div>
          <div class="p-6">
            <div v-if="activeOrders.length > 0" class="space-y-4">
              <div v-for="order in activeOrders" :key="order.id" class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <p class="font-semibold text-gray-900">{{ order.order_number }}</p>
                    <p class="text-sm text-gray-600">{{ order.cattle_farm?.name }}</p>
                    <div v-if="order.jobs && order.jobs.length > 0" class="mt-2">
                      <div class="bg-blue-50 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                          <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="text-sm font-semibold text-gray-900">{{ order.jobs[0].technician.user.name }}</span>
                          </div>
                          <div class="flex space-x-1">
                            <button 
                              @click="callTechnician(order.jobs[0].technician)"
                              class="p-1 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors"
                              title="Call Technician"
                            >
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                              </svg>
                            </button>
                            <button 
                              @click="messageTechnician(order.jobs[0].technician)"
                              class="p-1 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
                              title="Message Technician"
                            >
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                              </svg>
                            </button>
                          </div>
                        </div>
                        <div class="text-xs text-gray-600 space-y-1">
                          <div class="flex items-center">
                            <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span>{{ order.jobs[0].technician.user.mobile || 'Phone not available' }}</span>
                          </div>
                          <div class="flex items-center">
                            <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>{{ order.jobs[0].technician.user.email || 'Email not available' }}</span>
                          </div>
                        </div>
                      </div>
                      <div v-if="order.jobs[0].estimated_visit_at" class="mt-1 text-sm text-blue-600">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        ETA: {{ formatDateTime(order.jobs[0].estimated_visit_at) }}
                      </div>
                      <div v-else class="mt-1 text-sm text-gray-500">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        ETA: Pending
                      </div>
                    </div>
                    <div v-else class="mt-2 text-sm text-gray-500">
                      <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      Technician: Not assigned
                    </div>
                  </div>
                  <div class="text-right">
                    <span :class="getStatusBadgeClass(order.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                      {{ order.status }}
                    </span>
                    <p class="text-sm text-gray-500 mt-1">₨{{ order.total_amount }}</p>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
              <p class="mt-2">No active assignments</p>
            </div>
          </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white/70 backdrop-blur-sm shadow-xl rounded-2xl border border-white/20 overflow-hidden">
          <div class="px-6 py-6 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-white/20">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-bold text-gray-900 flex items-center">
                <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Recent Orders
              </h3>
              <button class="text-green-600 hover:text-green-800 font-medium text-sm flex items-center">
                View All
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
          </div>
            <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-gray-50/50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order #</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Farm</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Technician</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-gray-50/50 transition-colors duration-150">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mr-3">
                        <span class="text-white text-xs font-bold">#</span>
                      </div>
                      <span class="text-sm font-semibold text-gray-900">{{ order.order_number }}</span>
                    </div>
                    </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-8 h-8 bg-gradient-to-r from-blue-400 to-green-500 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                      </div>
                      <span class="text-sm font-medium text-gray-900">{{ order.cattle_farm?.name }}</span>
                    </div>
                    </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm font-bold text-gray-900">₨{{ order.total_amount }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusBadgeClass(order.status)" class="px-3 py-1 inline-flex text-xs font-semibold rounded-full">
                        {{ order.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <div v-if="order.jobs?.[0]?.technician">
                        <div class="font-medium">{{ order.jobs[0].technician.user.name }}</div>
                        <div class="text-xs text-gray-500">{{ order.jobs[0].technician.user.mobile || 'Phone not available' }}</div>
                      </div>
                      <div v-else class="text-gray-500">Not Assigned</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(order.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div class="flex space-x-2">
                        <button 
                          v-if="order.jobs?.[0]?.technician"
                          @click="callTechnician(order.jobs[0].technician)"
                          class="p-1 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors"
                          title="Call Technician"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                          </svg>
                        </button>
                        <button 
                          v-if="order.jobs?.[0]?.technician"
                          @click="messageTechnician(order.jobs[0].technician)"
                          class="p-1 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
                          title="Message Technician"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                          </svg>
                        </button>
                        <button 
                          v-if="order.jobs?.[0] && !order.jobs[0].farmer_notes"
                          @click="goToServiceConfirmation(order.jobs[0].id)"
                          class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
                          title="Confirm Service"
                        >
                          Confirm
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
          </div>
        </div>
      </div>

      <!-- Farms Tab -->
      <div v-if="activeTab === 'farms'" class="space-y-8">
        <div class="flex justify-between items-center">
          <div>
            <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">My Farms</h2>
            <p class="text-gray-600 mt-1">Manage your farm locations and livestock</p>
          </div>
          <button @click="showAddFarmModal = true" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add New Farm
          </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="farm in farms" :key="farm.id" class="group bg-white/70 backdrop-blur-sm overflow-hidden shadow-xl rounded-2xl border border-white/20 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                  <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg mr-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg font-bold text-gray-900">{{ farm.name }}</h3>
                </div>
                <div class="flex space-x-2">
                  <button @click="editFarm(farm)" class="text-green-600 hover:text-green-800 p-2 rounded-lg hover:bg-green-50 transition-colors duration-200" title="Edit Farm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <button @click="deleteFarm(farm)" class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors duration-200" title="Delete Farm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </div>
              <div class="space-y-3">
                <div class="flex justify-between items-center py-2 px-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg">
                  <div class="flex items-center">
                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                      <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                      </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-600">Cows</span>
                  </div>
                  <span class="text-lg font-bold text-gray-900">{{ farm.number_of_cows }}</span>
                </div>
                <div class="flex justify-between items-center py-2 px-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
                  <div class="flex items-center">
                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                      <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                      </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-600">Calves</span>
                  </div>
                  <span class="text-lg font-bold text-gray-900">{{ farm.number_of_calves }}</span>
                </div>
                <div class="flex justify-between items-center py-2 px-3 bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg">
                  <div class="flex items-center">
                    <div class="w-6 h-6 bg-amber-100 rounded-full flex items-center justify-center mr-3">
                      <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                      </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-600">Bulls</span>
                  </div>
                  <span class="text-lg font-bold text-gray-900">{{ farm.number_of_bulls }}</span>
                </div>
                <div class="flex justify-between items-center py-2 px-3 bg-gradient-to-r from-purple-50 to-violet-50 rounded-lg">
                  <div class="flex items-center">
                    <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                      <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                      </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-600">Goats</span>
                  </div>
                  <span class="text-lg font-bold text-gray-900">{{ farm.number_of_goats }}</span>
                </div>
              </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-green-500 to-emerald-600"></div>
          </div>
        </div>
      </div>

      <!-- Orders Tab -->
      <div v-if="activeTab === 'orders'" class="space-y-6">
        <div class="flex justify-between items-center">
          <h2 class="text-2xl font-bold text-gray-900">My Orders</h2>
          <div class="flex space-x-3">
            <button @click="goToSemenCatalog" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
              Browse Semen Catalog
            </button>
            <button @click="goToCart" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
              View Cart
            </button>
          </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Farm</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technician</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="order in orders" :key="order.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ order.order_number }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ order.cattle_farm?.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ order.order_items?.length || 0 }} items
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ₨{{ order.total_amount }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusBadgeClass(order.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ order.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div v-if="order.jobs && order.jobs.length > 0">
                      <div class="font-medium">{{ order.jobs[0].technician.user.name }}</div>
                      <div v-if="order.jobs[0].estimated_visit_at" class="text-xs text-green-600">
                        ETA: {{ formatDateTime(order.jobs[0].estimated_visit_at) }}
                      </div>
                      <div v-else class="text-xs text-gray-400">ETA: Pending</div>
                    </div>
                    <div v-else class="text-gray-400">Not assigned</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(order.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div v-if="order.jobs && order.jobs.length > 0 && order.jobs[0].status === 'completed' && !order.jobs[0].farmer_notes">
                      <button
                        @click="goToServiceConfirmation(order.jobs[0].id)"
                        class="text-green-600 hover:text-green-800 font-medium"
                      >
                        Confirm Service
                      </button>
                    </div>
                    <div v-else-if="order.jobs && order.jobs.length > 0 && order.jobs[0].farmer_notes">
                      <span class="text-gray-500">Confirmed</span>
                    </div>
                    <div v-else>
                      <span class="text-gray-400">-</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Pregnancy Tracking Tab -->
      <div v-if="activeTab === 'pregnancy'" class="space-y-6">
        <div class="flex justify-between items-center">
          <h2 class="text-2xl font-bold text-gray-900">Pregnancy Tracking</h2>
          <button @click="goToPregnancyTracking" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
            View Full Tracking
          </button>
        </div>
        
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
          <p class="text-gray-600 mb-4">Track pregnancy outcomes and deliveries for your cattle.</p>
          <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Pregnancy Tracking</h3>
            <p class="mt-1 text-sm text-gray-500">Track your cattle's pregnancy journey from insemination to delivery.</p>
            <div class="mt-6">
              <button
                @click="goToPregnancyTracking"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors"
              >
                Open Pregnancy Tracking
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Profile Tab -->
      <div v-if="activeTab === 'profile'" class="space-y-6">
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Profile Information</h3>
            <form @submit.prevent="updateProfile" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Name</label>
                  <input v-model="profileForm.name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                  <input v-model="profileForm.date_of_birth" type="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Mobile</label>
                <input v-model="profileForm.mobile" type="text" disabled class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
              </div>
              <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                  Update Profile
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
    </div>

    <!-- Add Farm Modal -->
    <div v-if="showAddFarmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Farm</h3>
          <form @submit.prevent="addFarm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Farm Name</label>
                <input v-model="farmForm.name" type="text" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Enter farm name">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Father's Name</label>
                <input v-model="farmForm.father_name" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Enter father's name (optional)">
              </div>
            </div>

            <!-- Google Maps Location Picker -->
            <div class="bg-gray-50 p-4 rounded-xl">
              <GoogleMapLocationPicker
                v-model="farmLocation"
                @update:modelValue="updateFarmLocation"
              />
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Cows</label>
                <input v-model="farmForm.number_of_cows" type="number" min="0" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="0">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Calves</label>
                <input v-model="farmForm.number_of_calves" type="number" min="0" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="0">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Bulls</label>
                <input v-model="farmForm.number_of_bulls" type="number" min="0" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="0">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Goats</label>
                <input v-model="farmForm.number_of_goats" type="number" min="0" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="0">
              </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
              <button type="button" @click="showAddFarmModal = false" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-400 transition-colors duration-200">
                Cancel
              </button>
              <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-colors duration-200">
                Add Farm
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit Farm Modal -->
    <div v-if="showEditFarmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Edit Farm</h2>
            <button @click="showEditFarmModal = false" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <form @submit.prevent="updateFarm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Farm Name *</label>
                <input
                  v-model="farmForm.name"
                  type="text"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  placeholder="Enter farm name"
                />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Father's Name</label>
                <input
                  v-model="farmForm.father_name"
                  type="text"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  placeholder="Enter father's name (optional)"
                />
              </div>
            </div>

            <!-- Google Maps Location Picker -->
            <div class="bg-gray-50 p-4 rounded-xl">
              <GoogleMapLocationPicker
                v-model="farmLocation"
                :initial-lat="farmForm.latitude"
                :initial-lng="farmForm.longitude"
                @update:modelValue="updateFarmLocation"
              />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Cows *</label>
                <input
                  v-model.number="farmForm.number_of_cows"
                  type="number"
                  min="0"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Calves *</label>
                <input
                  v-model.number="farmForm.number_of_calves"
                  type="number"
                  min="0"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Bulls *</label>
                <input
                  v-model.number="farmForm.number_of_bulls"
                  type="number"
                  min="0"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Goats *</label>
                <input
                  v-model.number="farmForm.number_of_goats"
                  type="number"
                  min="0"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                />
              </div>
            </div>

            <div class="flex justify-end space-x-4">
              <button
                type="button"
                @click="showEditFarmModal = false"
                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors font-medium"
              >
                Update Farm
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Technician Details Modal -->
    <div v-if="showTechnicianModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Technician Details</h2>
            <button @click="showTechnicianModal = false" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <div v-if="selectedTechnician" class="space-y-6">
            <!-- Technician Profile -->
            <div class="text-center">
              <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900">{{ selectedTechnician.user.name }}</h3>
              <p class="text-gray-600">Assigned Technician</p>
            </div>

            <!-- Contact Information -->
            <div class="space-y-4">
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Contact Information</h4>
                <div class="space-y-3">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                      <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-900">Phone Number</p>
                      <p class="text-lg font-semibold text-gray-700">{{ selectedTechnician.user.mobile || 'Not available' }}</p>
                    </div>
                  </div>
                  
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-900">Email Address</p>
                      <p class="text-sm text-gray-700">{{ selectedTechnician.user.email || 'Not available' }}</p>
                    </div>
                  </div>
                </div>
              </div>

              
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Notification -->
    <div v-if="notification.show" class="fixed top-4 right-4 z-50 max-w-sm w-full">
      <div :class="[
        'rounded-lg shadow-lg p-4 flex items-center',
        notification.type === 'success' ? 'bg-green-500 text-white' : 
        notification.type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
      ]">
        <div class="flex-1">
          <p class="text-sm font-medium">{{ notification.message }}</p>
        </div>
        <button @click="notification.show = false" class="ml-4 text-white hover:text-gray-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>
    
    <!-- Profile Completion Alert -->
    <div v-if="profileCompletionAlert.show" class="fixed top-4 left-4 right-4 z-50 max-w-4xl mx-auto">
      <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg shadow-lg p-4 flex items-center">
        <div class="flex-shrink-0 mr-4">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <div class="flex-1">
          <h3 class="text-lg font-semibold mb-1">Welcome to BreedCare!</h3>
          <p class="text-sm">{{ profileCompletionAlert.message }}</p>
        </div>
        <button @click="dismissProfileAlert" class="ml-4 text-white hover:text-gray-200 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import GoogleMapLocationPicker from '@/components/GoogleMapLocationPicker.vue'

export default {
  name: 'FarmerDashboard',
  components: {
    GoogleMapLocationPicker
  },
  setup() {
    const authStore = useAuthStore()
    const router = useRouter()
    
    const activeTab = ref('overview')
    const farmer = ref(null)
    const farms = ref([])
    const orders = ref([])
    const showAddFarmModal = ref(false)
    const showEditFarmModal = ref(false)
    const showOrderModal = ref(false)
    const showTechnicianModal = ref(false)
    const editingFarm = ref(null)
    const selectedTechnician = ref(null)
    
    // Profile completion alert state
    const profileCompletionAlert = ref({
      show: false,
      message: 'Welcome! Please complete your profile to get started. Fill in all required information to access full features.'
    })
    
    // Notification state
    const notification = ref({
      show: false,
      message: '',
      type: 'success' // success, error, info
    })
    
    const farmForm = ref({
      name: '',
      father_name: '',
      latitude: '',
      longitude: '',
      number_of_cows: 0,
      number_of_calves: 0,
      number_of_bulls: 0,
      number_of_goats: 0
    })
    
    const farmLocation = ref({
      lat: 31.5204, // Default to Lahore, Pakistan
      lng: 74.3587
    })
    
    const profileForm = ref({
      name: '',
      mobile: '',
      date_of_birth: ''
    })
    
    const tabs = [
      { id: 'overview', name: 'Overview' },
      { id: 'farms', name: 'My Farms' },
      { id: 'orders', name: 'Orders' },
      { id: 'pregnancy', name: 'Pregnancy Tracking' },
      { id: 'profile', name: 'Profile' }
    ]
    
    const pendingOrdersCount = computed(() => 
      orders.value.filter(order => ['pending', 'assigned', 'in_progress'].includes(order.status)).length
    )
    
    const completedOrdersCount = computed(() => 
      orders.value.filter(order => order.status === 'completed').length
    )
    
    const recentOrders = computed(() => 
      orders.value.slice(0, 5)
    )
    
    const activeOrders = computed(() => 
      orders.value.filter(order => ['pending', 'assigned', 'in_progress'].includes(order.status))
    )
    
    const loadProfile = async () => {
      try {
        const response = await window.axios.get('/farmer/profile')
        farmer.value = response.data.farmer
        profileForm.value = {
          name: farmer.value.name,
          mobile: farmer.value.mobile,
          date_of_birth: farmer.value.date_of_birth
        }
      } catch (error) {
        console.error('Error loading profile:', error)
      }
    }
    
    const loadFarms = async () => {
      try {
        const response = await window.axios.get('/farmer/farms')
        farms.value = response.data.farms
      } catch (error) {
        console.error('Error loading farms:', error)
      }
    }
    
    const loadOrders = async () => {
      try {
        const response = await window.axios.get('/farmer/orders')
        orders.value = response.data.orders.data
      } catch (error) {
        console.error('Error loading orders:', error)
      }
    }
    
    const addFarm = async () => {
      try {
        await window.axios.post('/farmer/farms', farmForm.value)
        showAddFarmModal.value = false
        farmForm.value = {
          name: '',
          father_name: '',
          latitude: '',
          longitude: '',
          number_of_cows: 0,
          number_of_calves: 0,
          number_of_bulls: 0,
          number_of_goats: 0
        }
        loadFarms()
      } catch (error) {
        console.error('Error adding farm:', error)
      }
    }
    
    const editFarm = (farm) => {
      editingFarm.value = farm
      farmForm.value = {
        name: farm.name,
        father_name: farm.father_name || '',
        latitude: farm.latitude,
        longitude: farm.longitude,
        number_of_cows: farm.number_of_cows,
        number_of_calves: farm.number_of_calves,
        number_of_bulls: farm.number_of_bulls,
        number_of_goats: farm.number_of_goats
      }
      farmLocation.value = {
        lat: parseFloat(farm.latitude) || 31.5204,
        lng: parseFloat(farm.longitude) || 74.3587
      }
      showEditFarmModal.value = true
    }
    
    const updateFarm = async () => {
      try {
        await window.axios.put(`/farmer/farms/${editingFarm.value.id}`, farmForm.value)
        showEditFarmModal.value = false
        editingFarm.value = null
        farmForm.value = {
          name: '',
          father_name: '',
          latitude: '',
          longitude: '',
          number_of_cows: 0,
          number_of_calves: 0,
          number_of_bulls: 0,
          number_of_goats: 0
        }
        loadFarms()
        alert('Farm updated successfully!')
      } catch (error) {
        console.error('Error updating farm:', error)
        alert('Failed to update farm. Please try again.')
      }
    }
    
    const deleteFarm = async (farm) => {
      if (confirm(`Are you sure you want to delete "${farm.name}"? This action cannot be undone.`)) {
        try {
          await window.axios.delete(`/farmer/farms/${farm.id}`)
          loadFarms()
          alert('Farm deleted successfully!')
        } catch (error) {
          console.error('Error deleting farm:', error)
          alert('Failed to delete farm. Please try again.')
        }
      }
    }
    
    const updateProfile = async () => {
      try {
        await window.axios.put('/farmer/profile', profileForm.value)
        loadProfile()
        showNotification('Profile updated successfully!', 'success')
      } catch (error) {
        console.error('Error updating profile:', error)
        showNotification('Failed to update profile. Please try again.', 'error')
      }
    }
    
    const getStatusBadgeClass = (status) => {
      const classes = {
        pending: 'bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800 border border-yellow-200',
        assigned: 'bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 border border-blue-200',
        in_progress: 'bg-gradient-to-r from-purple-100 to-violet-100 text-purple-800 border border-purple-200',
        completed: 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200',
        cancelled: 'bg-gradient-to-r from-red-100 to-rose-100 text-red-800 border border-red-200'
      }
      return classes[status] || 'bg-gradient-to-r from-gray-100 to-slate-100 text-gray-800 border border-gray-200'
    }
    
    const formatDate = (date) => {
      return new Date(date).toLocaleDateString()
    }
    
    const formatDateTime = (date) => {
      return new Date(date).toLocaleString()
    }
    
    const logout = () => {
      authStore.logout()
      router.push('/login')
    }
    
    const goToSemenCatalog = () => {
      router.push('/farmer/semen-catalog')
    }
    
    const goToCart = () => {
      router.push('/farmer/cart')
    }
    
    const goToServiceConfirmation = (jobId) => {
      router.push(`/farmer/service-confirmation/${jobId}`)
    }
    
    const goToPregnancyTracking = () => {
      router.push('/farmer/pregnancy-tracking')
    }
    
    const updateFarmLocation = (location) => {
      farmForm.value.latitude = location.lat
      farmForm.value.longitude = location.lng
    }
    
    const callTechnician = (technician) => {
      selectedTechnician.value = technician
      showTechnicianModal.value = true
    }
    
    const messageTechnician = (technician) => {
      selectedTechnician.value = technician
      showTechnicianModal.value = true
    }
    
    const initiateCall = (technician) => {
      if (technician.user.mobile) {
        window.open(`tel:${technician.user.mobile}`, '_self')
        showTechnicianModal.value = false
      } else {
        alert('Technician phone number not available')
      }
    }
    
    const initiateMessage = (technician) => {
      if (technician.user.mobile) {
        const message = `Hello ${technician.user.name}, I have a question about my order.`
        window.open(`sms:${technician.user.mobile}?body=${encodeURIComponent(message)}`, '_self')
        showTechnicianModal.value = false
      } else {
        alert('Technician phone number not available')
      }
    }
    
    const dismissProfileAlert = () => {
      profileCompletionAlert.value.show = false
      localStorage.removeItem('showProfileCompletionAlert')
    }
    
    const showNotification = (message, type = 'success') => {
      notification.value = {
        show: true,
        message,
        type
      }
      // Auto hide after 5 seconds
      setTimeout(() => {
        notification.value.show = false
      }, 5000)
    }
    
    onMounted(() => {
      // Check URL parameters for tab switching
      const urlParams = new URLSearchParams(window.location.search)
      const tabParam = urlParams.get('tab')
      if (tabParam === 'profile') {
        activeTab.value = 'profile'
      }
      
      loadProfile()
      loadFarms()
      loadOrders()
      
      // Check if user should see profile completion alert
      if (localStorage.getItem('showProfileCompletionAlert') === 'true') {
        profileCompletionAlert.value.show = true
      }
    })
    
    return {
      activeTab,
      farmer,
      farms,
      orders,
      showAddFarmModal,
      showEditFarmModal,
      showOrderModal,
      editingFarm,
      farmForm,
      farmLocation,
      profileForm,
      tabs,
      pendingOrdersCount,
      completedOrdersCount,
      recentOrders,
      activeOrders,
      addFarm,
      editFarm,
      updateFarm,
      deleteFarm,
      updateProfile,
      getStatusBadgeClass,
      formatDate,
      formatDateTime,
      logout,
      goToSemenCatalog,
      goToCart,
      goToServiceConfirmation,
      goToPregnancyTracking,
      updateFarmLocation,
      callTechnician,
      messageTechnician,
      initiateCall,
      initiateMessage,
      showTechnicianModal,
      selectedTechnician,
      profileCompletionAlert,
      dismissProfileAlert,
      notification,
      showNotification
    }
  }
}
</script>