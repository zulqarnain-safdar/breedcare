<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-50 via-violet-50 to-indigo-100 flex">
    <!-- Mobile Menu Button -->
    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-white/80 backdrop-blur-md rounded-lg shadow-lg">
      <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
      </svg>
    </button>

    <!-- Sidebar -->
    <div :class="[
      'w-64 bg-white/80 backdrop-blur-md shadow-xl border-r border-white/20 flex-shrink-0 transition-transform duration-300 ease-in-out',
      sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]" class="fixed lg:static inset-y-0 left-0 z-40">
      <!-- Logo & Header -->
      <div class="p-6 border-b border-white/20">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-violet-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-lg font-bold text-gray-900">Technician Panel</h1>
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
              ? 'bg-gradient-to-r from-purple-500 to-violet-600 text-white shadow-lg' 
              : 'text-gray-600 hover:text-gray-900 hover:bg-white/60'
          ]"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path v-if="tab.id === 'overview'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            <path v-else-if="tab.id === 'jobs'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            <path v-else-if="tab.id === 'availability'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            <path v-else-if="tab.id === 'inventory'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            <path v-else-if="tab.id === 'earnings'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            <path v-else-if="tab.id === 'profile'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          <span>{{ tab.name }}</span>
        </button>
      </nav>

      <!-- User Info & Logout -->
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/20">
        <div class="flex items-center space-x-3 mb-4">
          <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-violet-500 rounded-full flex items-center justify-center">
            <span class="text-white text-sm font-semibold">{{ technician?.name?.charAt(0) }}</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">{{ technician?.name }}</p>
            <p class="text-xs text-gray-500">Technician</p>
          </div>
        </div>
        <div class="mb-4">
          <span :class="getAvailabilityBadgeClass(technician?.availability)" class="w-full flex items-center justify-center px-3 py-2 text-xs font-semibold rounded-lg">
            {{ technician?.availability || 'offline' }}
          </span>
        </div>
        <button @click="logout" class="w-full flex items-center space-x-3 px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 rounded-xl transition-all duration-200">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
          </svg>
          <span>Logout</span>
        </button>
      </div>
    </div>

    <!-- Mobile Overlay -->
    <div v-if="sidebarOpen" @click="sidebarOpen = false" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30"></div>

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
          <!-- Total Jobs Card -->
          <div class="group bg-white/70 backdrop-blur-sm overflow-hidden shadow-xl rounded-2xl border border-white/20 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
            <div class="p-6">
              <div class="flex items-center justify-between">
                <div class="flex-shrink-0">
                  <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-600 mb-1">Total Jobs</p>
                  <p class="text-3xl font-bold text-gray-900">{{ jobs.length }}</p>
                  <div class="flex items-center text-blue-600 text-sm mt-1">
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

          <!-- Total Earnings Card -->
          <div class="group bg-white/70 backdrop-blur-sm overflow-hidden shadow-xl rounded-2xl border border-white/20 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
            <div class="p-6">
              <div class="flex items-center justify-between">
                <div class="flex-shrink-0">
                  <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-600 mb-1">Total Earnings</p>
                  <p class="text-3xl font-bold text-gray-900">₨{{ totalEarnings }}</p>
                  <div class="flex items-center text-green-600 text-sm mt-1">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                    </svg>
                    +15%
                  </div>
                </div>
              </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-green-500 to-emerald-600"></div>
          </div>

          <!-- Pending Jobs Card -->
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
                  <p class="text-sm font-medium text-gray-600 mb-1">Pending Jobs</p>
                  <p class="text-3xl font-bold text-gray-900">{{ pendingJobsCount }}</p>
                  <div class="flex items-center text-amber-600 text-sm mt-1">
                    <div class="w-2 h-2 bg-amber-500 rounded-full mr-2 animate-pulse"></div>
                    Awaiting
                  </div>
                </div>
              </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-amber-500 to-orange-600"></div>
          </div>

          <!-- Completed Jobs Card -->
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
                  <p class="text-sm font-medium text-gray-600 mb-1">Completed Jobs</p>
                  <p class="text-3xl font-bold text-gray-900">{{ completedJobsCount }}</p>
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

        <!-- Recent Jobs -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Jobs</h3>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Farmer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Farm</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="job in recentJobs" :key="job.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      #{{ job.id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ job.order?.farmer?.user?.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ job.order?.cattle_farm?.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="getJobStatusBadgeClass(job.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ job.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      ₨{{ formatCurrency(job.amount_paid) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(job.created_at) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Jobs Tab -->
      <div v-if="activeTab === 'jobs'" class="space-y-6">
        <div class="flex justify-between items-center">
          <h2 class="text-2xl font-bold text-gray-900">My Jobs</h2>
          <div class="flex space-x-4">
            <select v-model="jobStatusFilter" @change="loadJobs" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="all">All Jobs</option>
              <option value="pending">Pending</option>
              <option value="accepted">Accepted</option>
              <option value="in_progress">In Progress</option>
              <option value="completed">Completed</option>
              <option value="rejected">Rejected</option>
              <option value="no_heat">No Heat</option>
            </select>
            <button @click="loadJobs" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
              Refresh
            </button>
          </div>
        </div>

        <!-- Job Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="bg-blue-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-blue-600">{{ jobStats.pending || 0 }}</div>
            <div class="text-sm text-gray-600">Pending</div>
          </div>
          <div class="bg-green-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-green-600">{{ jobStats.completed || 0 }}</div>
            <div class="text-sm text-gray-600">Completed</div>
          </div>
          <div class="bg-yellow-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-yellow-600">{{ jobStats.in_progress || 0 }}</div>
            <div class="text-sm text-gray-600">In Progress</div>
          </div>
          <div class="bg-red-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-red-600">{{ jobStats.rejected || 0 }}</div>
            <div class="text-sm text-gray-600">Rejected</div>
          </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job #</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Farmer</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Farm</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semen</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="job in jobs" :key="job.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    #{{ job.id }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ job.order?.farmer?.user?.name }}</div>
                    <div class="text-sm text-gray-500">{{ job.order?.farmer?.mobile_1 }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ job.order?.cattle_farm?.name }}</div>
                    <div class="text-sm text-gray-500">{{ job.order?.cattle_farm?.address }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ job.order?.bull_semen?.bull_name }}</div>
                    <div class="text-sm text-gray-500">{{ job.order?.bull_semen?.breed?.name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getJobStatusBadgeClass(job.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ job.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ₨{{ formatCurrency(job.amount_paid) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex space-x-2">
                      <button 
                        v-if="job.status === 'pending'"
                        @click="acceptJob(job)"
                        class="text-green-600 hover:text-green-800 font-medium"
                      >
                        Accept
                      </button>
                      <button 
                        v-if="job.status === 'pending'"
                        @click="rejectJob(job)"
                        class="text-red-600 hover:text-red-800 font-medium"
                      >
                        Reject
                      </button>
                      <button 
                        v-if="job.status === 'accepted'"
                        @click="provideETA(job)"
                        class="text-blue-600 hover:text-blue-800 font-medium"
                      >
                        Provide ETA
                      </button>
                      <button 
                        v-if="job.status === 'accepted'"
                        @click="startJob(job)"
                        class="text-green-600 hover:text-green-800 font-medium"
                      >
                        Start Job
                      </button>
                      <button 
                        v-if="job.status === 'in_progress'"
                        @click="openJobModal(job)"
                        class="text-purple-600 hover:text-purple-800 font-medium"
                      >
                        Farm Visit
                      </button>
                      <button 
                        v-if="job.status === 'in_progress'"
                        @click="completeJob(job)"
                        class="text-green-600 hover:text-green-800 font-medium"
                      >
                        Complete
                      </button>
                      <button 
                        @click="viewJobDetails(job)"
                        class="text-gray-600 hover:text-gray-800 font-medium"
                      >
                        View
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Earnings Tab -->
      <div v-if="activeTab === 'earnings'" class="space-y-6">
        <div class="flex justify-between items-center">
          <h2 class="text-2xl font-bold text-gray-900">Earnings</h2>
          <div class="flex space-x-4">
            <select v-model="earningsPeriod" @change="loadEarnings" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="day">Today</option>
              <option value="week">This Week</option>
              <option value="month">This Month</option>
              <option value="year">This Year</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Earnings</dt>
                    <dd class="text-lg font-medium text-gray-900">₨{{ formatCurrency(earnings.total) }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Jobs Completed</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ earnings.job_count || 0 }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Average per Job</dt>
                    <dd class="text-lg font-medium text-gray-900">RS{{ averagePerJob }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Earnings History -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Earnings History</h3>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Farmer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="job in earnings.jobs" :key="job.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      #{{ job.id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ job.order?.farmer?.user?.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      ₨{{ formatCurrency(job.amount_paid) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(job.created_at) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Profile Tab -->
      <div v-if="activeTab === 'profile'" class="space-y-6">
        <!-- Profile Status & Unique Code -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Profile Status</h3>
          </div>
          <div class="p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                  <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-violet-600 rounded-full flex items-center justify-center">
                    <span class="text-white text-xl font-bold">{{ technician?.unique_code?.charAt(0) || 'T' }}</span>
                  </div>
                </div>
                <div>
                  <h4 class="text-lg font-semibold text-gray-900">{{ technician?.name || 'Technician' }}</h4>
                  <p class="text-sm text-gray-500">Unique Code: {{ technician?.unique_code || 'Not assigned' }}</p>
                  <div class="flex items-center mt-2">
                    <span :class="getStatusBadgeClass(technician?.status)" class="px-3 py-1 text-xs font-semibold rounded-full">
                      {{ technician?.status || 'Pending' }}
                    </span>
                    <span v-if="technician?.status === 'rejected'" class="ml-2 text-sm text-red-600">
                      {{ technician?.rejection_reason || 'Profile rejected' }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm text-gray-500">Profile Completion</p>
                <div class="w-32 bg-gray-200 rounded-full h-2 mt-1">
                  <div class="bg-blue-600 h-2 rounded-full" :style="{ width: profileCompletion + '%' }"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ profileCompletion }}% Complete</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Profile Information Form -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Profile Information</h3>
          </div>
          <div class="p-6">
            <form @submit.prevent="updateProfile" class="space-y-6">
              <!-- Basic Information -->
              <div>
                <h4 class="text-md font-semibold text-gray-900 mb-4">Basic Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Full Name *</label>
                    <input v-model="profileForm.name" type="text" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Father's Name *</label>
                    <input v-model="profileForm.father_name" type="text" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">CNIC * (Must be unique)</label>
                    <input v-model="profileForm.cnic" type="text" required pattern="[0-9]{5}-[0-9]{7}-[0-9]{1}" placeholder="12345-1234567-1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Date of Birth *</label>
                    <input v-model="profileForm.date_of_birth" type="date" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  </div>
                </div>
              </div>

              <!-- Contact Information -->
              <div>
                <h4 class="text-md font-semibold text-gray-900 mb-4">Contact Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Primary Mobile *</label>
                    <input v-model="profileForm.mobile_1" type="tel" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Secondary Mobile</label>
                    <input v-model="profileForm.mobile_2" type="tel" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input v-model="profileForm.email" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  </div>
                </div>
              </div>

              <!-- Address Information -->
              <div>
                <h4 class="text-md font-semibold text-gray-900 mb-4">Address Information</h4>
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Home Address *</label>
                    <textarea v-model="profileForm.home_address" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Clinic/Work Address</label>
                    <textarea v-model="profileForm.clinic_address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                   
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Province *</label>
                      <select v-model="profileForm.province" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Province</option>
                        <option value="Punjab">Punjab</option>
                        <option value="Sindh">Sindh</option>
                        <option value="KPK">KPK</option>
                        <option value="Balochistan">Balochistan</option>
                        <option value="Islamabad">Islamabad</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700">District *</label>
                      <input v-model="profileForm.district" type="text" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter district">
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Tehsil *</label>
                      <input v-model="profileForm.tehsil" type="text" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter tehsil">
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700">City *</label>
                      <input v-model="profileForm.city" type="text" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter city">
                    </div>
                  </div>
                </div>
              </div>

              <!-- Professional Information -->
              <div>
                <h4 class="text-md font-semibold text-gray-900 mb-4">Professional Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Years of Experience *</label>
                    <input v-model="profileForm.years_of_experience" type="number" min="0" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Degree Year *</label>
                    <input v-model="profileForm.degree_year" type="number" min="1950" :max="new Date().getFullYear()" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Capacity (Customers per Month) *</label>
                    <input v-model="profileForm.customers_per_month" type="number" min="1" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Specialization</label>
                    <input v-model="profileForm.specialization" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  </div>
                </div>
                <div class="mt-4">
                  <label class="block text-sm font-medium text-gray-700">Areas of Expertise *</label>
                  <textarea v-model="profileForm.areas_of_expertise" rows="3" required placeholder="e.g., Artificial Insemination, Animal Health, Breeding Management" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
              </div>

              <!-- Document Uploads -->
              <div>
                <h4 class="text-md font-semibold text-gray-900 mb-4">Required Documents</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- CNIC Front -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CNIC Front *</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                      <div class="space-y-1 text-center">
                        <div v-if="!documents.cnic_front" class="space-y-2">
                          <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                          </svg>
                          <div class="flex text-sm text-gray-600">
                            <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                              <span>Upload CNIC Front</span>
                              <input type="file" @change="handleDocumentUpload('cnic_front', $event)" class="sr-only" accept="image/*,.pdf" :disabled="uploading.cnic_front">
                            </label>
                          </div>
                          <p class="text-xs text-gray-500">PNG, JPG, PDF up to 10MB</p>
                        </div>
                        <div v-else class="space-y-2">
                          <img :src="documents.cnic_front" alt="CNIC Front" class="mx-auto h-32 w-auto object-contain rounded-md">
                          <div class="flex space-x-2 justify-center">
                            <button @click="viewDocument(documents.cnic_front)" type="button" class="text-sm text-blue-600 hover:text-blue-800">View</button>
                            <button @click="deleteDocument('cnic_front')" type="button" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                          </div>
                        </div>
                        <div v-if="uploading.cnic_front" class="text-sm text-blue-600">Uploading...</div>
                      </div>
                    </div>
                  </div>

                  <!-- CNIC Back -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CNIC Back *</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                      <div class="space-y-1 text-center">
                        <div v-if="!documents.cnic_back" class="space-y-2">
                          <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                          </svg>
                          <div class="flex text-sm text-gray-600">
                            <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                              <span>Upload CNIC Back</span>
                              <input type="file" @change="handleDocumentUpload('cnic_back', $event)" class="sr-only" accept="image/*,.pdf" :disabled="uploading.cnic_back">
                            </label>
                          </div>
                          <p class="text-xs text-gray-500">PNG, JPG, PDF up to 10MB</p>
                        </div>
                        <div v-else class="space-y-2">
                          <img :src="documents.cnic_back" alt="CNIC Back" class="mx-auto h-32 w-auto object-contain rounded-md">
                          <div class="flex space-x-2 justify-center">
                            <button @click="viewDocument(documents.cnic_back)" type="button" class="text-sm text-blue-600 hover:text-blue-800">View</button>
                            <button @click="deleteDocument('cnic_back')" type="button" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                          </div>
                        </div>
                        <div v-if="uploading.cnic_back" class="text-sm text-blue-600">Uploading...</div>
                      </div>
                    </div>
                  </div>

                  <!-- Degree Front -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Degree Certificate Front *</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                      <div class="space-y-1 text-center">
                        <div v-if="!documents.degree_front" class="space-y-2">
                          <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                          </svg>
                          <div class="flex text-sm text-gray-600">
                            <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                              <span>Upload Degree Front</span>
                              <input type="file" @change="handleDocumentUpload('degree_front', $event)" class="sr-only" accept="image/*,.pdf" :disabled="uploading.degree_front">
                            </label>
                          </div>
                          <p class="text-xs text-gray-500">PNG, JPG, PDF up to 10MB</p>
                        </div>
                        <div v-else class="space-y-2">
                          <img :src="documents.degree_front" alt="Degree Front" class="mx-auto h-32 w-auto object-contain rounded-md">
                          <div class="flex space-x-2 justify-center">
                            <button @click="viewDocument(documents.degree_front)" type="button" class="text-sm text-blue-600 hover:text-blue-800">View</button>
                            <button @click="deleteDocument('degree_front')" type="button" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                          </div>
                        </div>
                        <div v-if="uploading.degree_front" class="text-sm text-blue-600">Uploading...</div>
                      </div>
                    </div>
                  </div>

                  <!-- Degree Back -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Degree Certificate Back *</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                      <div class="space-y-1 text-center">
                        <div v-if="!documents.degree_back" class="space-y-2">
                          <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                          </svg>
                          <div class="flex text-sm text-gray-600">
                            <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                              <span>Upload Degree Back</span>
                              <input type="file" @change="handleDocumentUpload('degree_back', $event)" class="sr-only" accept="image/*,.pdf" :disabled="uploading.degree_back">
                            </label>
                          </div>
                          <p class="text-xs text-gray-500">PNG, JPG, PDF up to 10MB</p>
                        </div>
                        <div v-else class="space-y-2">
                          <img :src="documents.degree_back" alt="Degree Back" class="mx-auto h-32 w-auto object-contain rounded-md">
                          <div class="flex space-x-2 justify-center">
                            <button @click="viewDocument(documents.degree_back)" type="button" class="text-sm text-blue-600 hover:text-blue-800">View</button>
                            <button @click="deleteDocument('degree_back')" type="button" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                          </div>
                        </div>
                        <div v-if="uploading.degree_back" class="text-sm text-blue-600">Uploading...</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Location Information -->
              <div>
                <h4 class="text-md font-semibold text-gray-900 mb-4">Location Information</h4>
                <div class="bg-gray-50 p-4 rounded-xl">
                  <GoogleMapLocationPicker
                    v-model="technicianLocation"
                    :initial-lat="profileForm.clinic_latitude"
                    :initial-lng="profileForm.clinic_longitude"
                    @update:modelValue="updateTechnicianLocation"
                  />
                </div>
              </div>

              <div class="flex justify-between">
                <button type="button" @click="submitForApproval" :disabled="!isProfileComplete" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 disabled:bg-gray-300">
                  Submit for Approval
                </button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                  Update Profile
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Availability Tab -->
      <div v-if="activeTab === 'availability'" class="space-y-6">
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Set Your Availability</h3>
          </div>
          <div class="p-6">
            <div class="space-y-6">
              <!-- Current Status -->
              <div class="text-center">
                <div class="inline-flex items-center space-x-4">
                  <div class="text-center">
                    <div :class="getAvailabilityBadgeClass(technician?.availability)" class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-2">
                      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="technician?.availability === 'online'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <path v-else-if="technician?.availability === 'offline'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                    <p class="text-lg font-semibold text-gray-900">{{ technician?.availability || 'Offline' }}</p>
                  </div>
                </div>
              </div>

              <!-- Availability Options -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button 
                  @click="updateAvailability('online')"
                  :class="technician?.availability === 'online' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-green-50'"
                  class="p-6 border-2 rounded-lg text-center transition-all duration-200"
                >
                  <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg font-semibold mb-2">Online</h3>
                  <p class="text-sm">Available for new jobs</p>
                </button>

                <button 
                  @click="updateAvailability('offline')"
                  :class="technician?.availability === 'offline' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-red-50'"
                  class="p-6 border-2 rounded-lg text-center transition-all duration-200"
                >
                  <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg font-semibold mb-2">Offline</h3>
                  <p class="text-sm">Not available for jobs</p>
                </button>

                <button 
                  @click="updateAvailability('on_leave')"
                  :class="technician?.availability === 'on_leave' ? 'bg-yellow-600 text-white border-yellow-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-yellow-50'"
                  class="p-6 border-2 rounded-lg text-center transition-all duration-200"
                >
                  <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg font-semibold mb-2">On Leave</h3>
                  <p class="text-sm">Temporarily unavailable</p>
                </button>
              </div>

              <!-- Working Hours -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-md font-semibold text-gray-900 mb-4">Working Hours</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                    <input v-model="workingHours.start_time" type="time" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                    <input v-model="workingHours.end_time" type="time" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                  </div>
                </div>
                <div class="mt-4">
                  <label class="flex items-center">
                    <input v-model="workingHours.available_24_7" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">Available 24/7</span>
                  </label>
                </div>
              </div>

              <!-- Service Areas -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-md font-semibold text-gray-900 mb-4">Service Areas</h4>
                <div class="space-y-2">
                  <div v-for="(area, index) in serviceAreas" :key="index" class="flex items-center space-x-2">
                    <input v-model="area.name" type="text" placeholder="Enter area name" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <input v-model="area.radius" type="number" placeholder="Radius (km)" class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <button @click="removeServiceArea(index)" type="button" class="text-red-600 hover:text-red-800">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                  <button @click="addServiceArea" type="button" class="text-blue-600 hover:text-blue-800 text-sm">
                    + Add Service Area
                  </button>
                </div>
              </div>
              
              <!-- Save Settings Button -->
              <div class="flex justify-end">
                <button @click="saveAvailabilitySettings" type="button" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                  Save Settings
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Inventory Tab -->
      <div v-if="activeTab === 'inventory'" class="space-y-6">
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">My Inventory</h3>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
              <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ inventory.total_items || 0 }}</div>
                <div class="text-sm text-gray-600">Total Items</div>
              </div>
              <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ inventory.available_items || 0 }}</div>
                <div class="text-sm text-gray-600">Available</div>
              </div>
              <div class="bg-yellow-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">{{ inventory.low_stock_items || 0 }}</div>
                <div class="text-sm text-gray-600">Low Stock</div>
              </div>
              <div class="bg-red-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-red-600">{{ inventory.out_of_stock || 0 }}</div>
                <div class="text-sm text-gray-600">Out of Stock</div>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Breed</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="item in inventory.items" :key="item.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ item.bull_name }}</div>
                      <div class="text-sm text-gray-500">₨{{ formatCurrency(item.price) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ item.breed?.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ item.stock_quantity }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="getStockStatusClass(item.stock_quantity)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ getStockStatus(item.stock_quantity) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <button @click="requestStock(item)" class="text-blue-600 hover:text-blue-800">Request Stock</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Farm Visit Modal -->
      <div v-if="showJobModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-2xl font-bold text-gray-900">Farm Visit - Job #{{ selectedJob?.id }}</h2>
              <button @click="closeJobModal" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>

            <div class="space-y-6">
              <!-- Job Information -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Job Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <p class="text-sm text-gray-600">Farmer</p>
                    <p class="font-medium">{{ selectedJob?.order?.farmer?.user?.name }}</p>
                    <p class="text-sm text-gray-500">{{ selectedJob?.order?.farmer?.mobile_1 }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600">Farm</p>
                    <p class="font-medium">{{ selectedJob?.order?.cattle_farm?.name }}</p>
                    <p class="text-sm text-gray-500">{{ selectedJob?.order?.cattle_farm?.address }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600">Semen</p>
                    <p class="font-medium">{{ selectedJob?.order?.bull_semen?.bull_name }}</p>
                    <p class="text-sm text-gray-500">{{ selectedJob?.order?.bull_semen?.breed?.name }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600">Amount</p>
                    <p class="font-medium text-green-600">₨{{ formatCurrency(selectedJob?.amount_paid) }}</p>
                  </div>
                </div>
              </div>

              <!-- Cow Heat Check -->
              <div class="bg-white border rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Cow Heat Check</h3>
                <div class="space-y-4">
                  <div>
                    <label class="flex items-center">
                      <input v-model="farmVisitForm.cow_in_heat" type="radio" value="yes" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                      <span class="ml-2 text-sm text-gray-700">Cow is in heat - Proceed with insemination</span>
                    </label>
                  </div>
                  <div>
                    <label class="flex items-center">
                      <input v-model="farmVisitForm.cow_in_heat" type="radio" value="no" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                      <span class="ml-2 text-sm text-gray-700">Cow is not in heat - Record visit fee only</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Cow Photo -->
              <div v-if="farmVisitForm.cow_in_heat === 'yes'" class="bg-white border rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Take Cow Photo</h3>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                  <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                      <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600">
                      <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                        <span>Take Photo</span>
                        <input type="file" @change="handleCowPhotoUpload" class="sr-only" accept="image/*" capture="environment">
                      </label>
                    </div>
                    <p class="text-xs text-gray-500">PNG, JPG up to 10MB</p>
                  </div>
                </div>
              </div>

              <!-- Straw Information -->
              <div v-if="farmVisitForm.cow_in_heat === 'yes'" class="bg-white border rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Straw Information</h3>
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Straw Barcode/Code</label>
                    <div class="flex space-x-2">
                      <input v-model="farmVisitForm.straw_code" type="text" placeholder="Scan or enter straw code" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                      <button @click="scanBarcode" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Scan
                      </button>
                    </div>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Straw Photo</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                      <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                          <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                          <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                            <span>Upload Straw Photo</span>
                            <input type="file" @change="handleStrawPhotoUpload" class="sr-only" accept="image/*" capture="environment">
                          </label>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG up to 10MB</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Payment Information -->
              <div class="bg-white border rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount Received</label>
                    <input v-model="farmVisitForm.amount_received" type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select v-model="farmVisitForm.payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                      <option value="">Select Payment Method</option>
                      <option value="cash">Cash</option>
                      <option value="bank_transfer">Bank Transfer</option>
                      <option value="mobile_money">Mobile Money</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Location Recording -->
              <div class="bg-white border rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Location Recording</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                    <input v-model="farmVisitForm.latitude" type="number" step="any" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                    <input v-model="farmVisitForm.longitude" type="number" step="any" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                  </div>
                </div>
                <div class="mt-2">
                  <button @click="getCurrentLocation" type="button" class="text-sm text-blue-600 hover:text-blue-800">
                    📍 Get Current Location
                  </button>
                </div>
              </div>

              <!-- Notes -->
              <div class="bg-white border rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Notes</h3>
                <textarea v-model="farmVisitForm.notes" rows="3" placeholder="Add any additional notes about the visit..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
              </div>

              <div class="flex justify-end space-x-4">
                <button @click="closeJobModal" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                  Cancel
                </button>
                <button 
                  v-if="farmVisitForm.cow_in_heat === 'no'"
                  @click="markNoHeat"
                  class="px-6 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                >
                  Mark as No Heat
                </button>
                <button 
                  v-if="farmVisitForm.cow_in_heat === 'yes'"
                  @click="administerSemen"
                  class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                >
                  Administer Semen
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ETA Modal -->
      <div v-if="showETAModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
          <div class="p-6">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-xl font-bold text-gray-900">Provide ETA</h2>
              <button @click="closeETAModal" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>

            <form @submit.prevent="submitETA" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Arrival Time</label>
                <input v-model="etaForm.eta" type="datetime-local" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea v-model="etaForm.notes" rows="3" placeholder="Any additional information for the farmer..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
              </div>
              <div class="flex justify-end space-x-4">
                <button type="button" @click="closeETAModal" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                  Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                  Submit ETA
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Job Details Modal -->
      <div v-if="showJobDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-xl font-bold text-gray-900">Job Details - #{{ selectedJobForDetails?.id }}</h2>
              <button @click="closeJobDetailsModal" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>

            <div v-if="selectedJobForDetails" class="space-y-6">
              <!-- Job Status -->
              <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-semibold text-gray-900">Job Status</h3>
                  <span :class="getJobStatusBadgeClass(selectedJobForDetails.status)" class="px-3 py-1 text-sm font-semibold rounded-full">
                    {{ selectedJobForDetails.status }}
                  </span>
                </div>
                <div class="mt-2 text-sm text-gray-600">
                  Created: {{ formatDate(selectedJobForDetails.created_at) }}
                </div>
              </div>

              <!-- Farmer Information -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                  <h3 class="text-lg font-semibold text-blue-900 mb-3">Farmer Information</h3>
                  <div class="space-y-2 text-sm">
                    <div><span class="font-medium">Name:</span> {{ selectedJobForDetails.order?.farmer?.user?.name || 'N/A' }}</div>
                    <div><span class="font-medium">Phone:</span> {{ selectedJobForDetails.order?.farmer?.mobile || 'N/A' }}</div>
                    <div><span class="font-medium">Date of Birth:</span> {{ selectedJobForDetails.order?.farmer?.date_of_birth || 'N/A' }}</div>
                    <div><span class="font-medium">Status:</span> {{ selectedJobForDetails.order?.farmer?.status || 'N/A' }}</div>
                  </div>
                </div>

                <div class="bg-green-50 p-4 rounded-lg">
                  <h3 class="text-lg font-semibold text-green-900 mb-3">Farm Information</h3>
                  <div class="space-y-2 text-sm">
                    <div><span class="font-medium">Farm Name:</span> {{ selectedJobForDetails.order?.cattle_farm?.name || 'N/A' }}</div>
                    <div><span class="font-medium">Location:</span> {{ selectedJobForDetails.order?.cattle_farm?.latitude && selectedJobForDetails.order?.cattle_farm?.longitude ? `${selectedJobForDetails.order.cattle_farm.latitude}, ${selectedJobForDetails.order.cattle_farm.longitude}` : 'N/A' }}</div>
                    <div><span class="font-medium">Number of Cows:</span> {{ selectedJobForDetails.order?.cattle_farm?.number_of_cows || 'N/A' }}</div>
                    <div><span class="font-medium">Number of Calves:</span> {{ selectedJobForDetails.order?.cattle_farm?.number_of_calves || 'N/A' }}</div>
                    <div><span class="font-medium">Number of Bulls:</span> {{ selectedJobForDetails.order?.cattle_farm?.number_of_bulls || 'N/A' }}</div>
                  </div>
                </div>
              </div>

              <!-- Semen Information -->
              <div v-if="selectedJobForDetails.order?.orderItems?.length" class="bg-yellow-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-yellow-900 mb-3">Semen Information</h3>
                <div class="space-y-3">
                  <div v-for="item in selectedJobForDetails.order.orderItems" :key="item.id" class="bg-white p-3 rounded border">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                      <div><span class="font-medium">Breed:</span> {{ item.bullSemen?.breed?.name || 'N/A' }}</div>
                      <div><span class="font-medium">Bull Name:</span> {{ item.bullSemen?.bull_name || 'N/A' }}</div>
                      <div><span class="font-medium">Quantity:</span> {{ item.quantity || 'N/A' }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Job Details -->
              <div class="bg-purple-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-900 mb-3">Job Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                  <div><span class="font-medium">Amount Paid:</span> Rs{{ formatCurrency(selectedJobForDetails.amount_paid) }}</div>
                  <div><span class="font-medium">Visit Fee:</span> Rs{{ formatCurrency(selectedJobForDetails.order?.visit_fee) }}</div>
                  <div><span class="font-medium">Estimated Visit:</span> {{ selectedJobForDetails.estimated_visit_at ? formatDate(selectedJobForDetails.estimated_visit_at) : 'N/A' }}</div>
                  <div><span class="font-medium">Actual Visit:</span> {{ selectedJobForDetails.actual_visit_at ? formatDate(selectedJobForDetails.actual_visit_at) : 'N/A' }}</div>
                  <div><span class="font-medium">Progress Status:</span> {{ selectedJobForDetails.progress_status || 'N/A' }}</div>
                  <div><span class="font-medium">Rating:</span> {{ selectedJobForDetails.rating || 'N/A' }}</div>
                </div>
                <div v-if="selectedJobForDetails.technician_notes" class="mt-3">
                  <span class="font-medium">Technician Notes:</span>
                  <p class="text-gray-700 mt-1">{{ selectedJobForDetails.technician_notes }}</p>
                </div>
                <div v-if="selectedJobForDetails.farmer_notes" class="mt-3">
                  <span class="font-medium">Farmer Notes:</span>
                  <p class="text-gray-700 mt-1">{{ selectedJobForDetails.farmer_notes }}</p>
                </div>
              </div>

              <!-- Visit Information -->
              <div v-if="selectedJobForDetails.visit_latitude || selectedJobForDetails.visit_longitude" class="bg-indigo-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-indigo-900 mb-3">Visit Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                  <div><span class="font-medium">Visit Latitude:</span> {{ selectedJobForDetails.visit_latitude || 'N/A' }}</div>
                  <div><span class="font-medium">Visit Longitude:</span> {{ selectedJobForDetails.visit_longitude || 'N/A' }}</div>
                </div>
              </div>

              <!-- Semen Delivery Information -->
              <div v-if="selectedJobForDetails.semen_straw_code || selectedJobForDetails.cow_status || selectedJobForDetails.delivery_notes" class="bg-orange-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-orange-900 mb-3">Semen Delivery Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                  <div><span class="font-medium">Straw Code:</span> {{ selectedJobForDetails.semen_straw_code || 'N/A' }}</div>
                  <div><span class="font-medium">Cow Status:</span> {{ selectedJobForDetails.cow_status || 'N/A' }}</div>
                </div>
                <div v-if="selectedJobForDetails.delivery_notes" class="mt-3">
                  <span class="font-medium">Delivery Notes:</span>
                  <p class="text-gray-700 mt-1">{{ selectedJobForDetails.delivery_notes }}</p>
                </div>
              </div>

              <!-- Images Section -->
              <div v-if="selectedJobForDetails.semen_straw_image || selectedJobForDetails.calf_image" class="bg-pink-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-pink-900 mb-3">Images</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div v-if="selectedJobForDetails.semen_straw_image" class="text-center">
                    <h4 class="font-medium text-sm mb-2">Semen Straw Image</h4>
                    <img :src="selectedJobForDetails.semen_straw_image" alt="Semen Straw" class="w-full h-32 object-cover rounded border">
                  </div>
                  <div v-if="selectedJobForDetails.calf_image" class="text-center">
                    <h4 class="font-medium text-sm mb-2">Calf Image</h4>
                    <img :src="selectedJobForDetails.calf_image" alt="Calf" class="w-full h-32 object-cover rounded border">
                  </div>
                </div>
              </div>

              <!-- Order Information -->
              <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Order Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                  <div><span class="font-medium">Order Number:</span> {{ selectedJobForDetails.order?.order_number || 'N/A' }}</div>
                  <div><span class="font-medium">Total Amount:</span> Rs{{ formatCurrency(selectedJobForDetails.order?.total_amount) }}</div>
                  <div><span class="font-medium">Order Status:</span> {{ selectedJobForDetails.order?.status || 'N/A' }}</div>
                  <div><span class="font-medium">Order Date:</span> {{ selectedJobForDetails.order?.created_at ? formatDate(selectedJobForDetails.order.created_at) : 'N/A' }}</div>
                </div>
                <div v-if="selectedJobForDetails.order?.notes" class="mt-3">
                  <span class="font-medium">Order Notes:</span>
                  <p class="text-gray-700 mt-1">{{ selectedJobForDetails.order.notes }}</p>
                </div>
              </div>
            </div>

            <div class="flex justify-end mt-6">
              <button @click="closeJobDetailsModal" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
      </main>
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
  name: 'TechnicianDashboard',
  setup() {
    const authStore = useAuthStore()
    const router = useRouter()
    
    const activeTab = ref('overview')
    const sidebarOpen = ref(false)
    const technician = ref(null)
    const jobs = ref([])
    const earnings = ref({})
    const earningsPeriod = ref('month')
    const jobStatusFilter = ref('all')
    
    const profileForm = ref({
      name: '',
      father_name: '',
      cnic: '',
      date_of_birth: '',
      mobile_1: '',
      mobile_2: '',
      email: '',
      home_address: '',
      clinic_address: '',
      city: '',
      province: '',
      district: '',
      tehsil: '',
      years_of_experience: 0,
      degree_year: '',
      customers_per_month: 0,
      specialization: '',
      areas_of_expertise: '',
      clinic_latitude: '',
      clinic_longitude: ''
    })
    
    // New reactive variables
    const showJobModal = ref(false)
    const showETAModal = ref(false)
    const showJobDetailsModal = ref(false)
    const selectedJob = ref(null)
    const selectedJobForDetails = ref(null)
    const jobStats = ref({})
    const inventory = ref({})
    const workingHours = ref({
      start_time: '09:00',
      end_time: '17:00',
      available_24_7: false
    })
    const serviceAreas = ref([])
    
    // Farm visit form
    const farmVisitForm = ref({
      cow_in_heat: '',
      straw_code: '',
      amount_received: '',
      payment_method: '',
      latitude: '',
      longitude: '',
      notes: ''
    })
    
    // ETA form
    const etaForm = ref({
      eta: '',
      notes: ''
    })
    
    // Technician location for Google Maps
    const technicianLocation = ref({
      lat: 31.5204, // Default to Lahore, Pakistan
      lng: 74.3587
    })
    
    // Document upload state
    const documents = ref({
      cnic_front: null,
      cnic_back: null,
      degree_front: null,
      degree_back: null
    })
    
    const uploading = ref({
      cnic_front: false,
      cnic_back: false,
      degree_front: false,
      degree_back: false
    })
    
    // Notification state
    const notification = ref({
      show: false,
      message: '',
      type: 'success' // success, error, info
    })
    
    // Profile completion alert state
    const profileCompletionAlert = ref({
      show: false,
      message: 'Welcome! Please complete your profile to get started. Fill in all required information to access full features.'
    })
    
    const tabs = [
      { id: 'overview', name: 'Overview' },
      { id: 'jobs', name: 'My Jobs' },
      { id: 'availability', name: 'Availability' },
      { id: 'inventory', name: 'Inventory' },
      { id: 'earnings', name: 'Earnings' },
      { id: 'profile', name: 'Profile' }
    ]
    
    const pendingJobsCount = computed(() => 
      jobs.value.filter(job => job.status === 'pending').length
    )
    
    const completedJobsCount = computed(() => 
      jobs.value.filter(job => job.status === 'completed').length
    )
    
    const totalEarnings = computed(() => {
      const total = jobs.value.reduce((sum, job) => {
        // Handle malformed amount_paid values
        let amount = job.amount_paid
        if (typeof amount === 'string') {
          // Remove any non-numeric characters except decimal point
          amount = amount.replace(/[^\d.]/g, '')
          // Handle cases where there might be multiple decimal points
          const parts = amount.split('.')
          if (parts.length > 2) {
            // Take only the first part as integer and second part as decimal
            amount = parts[0] + '.' + parts.slice(1).join('')
          }
        }
        return sum + (Number(amount) || 0)
      }, 0)
      return total.toLocaleString('en-PK', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      })
    })
    
    const recentJobs = computed(() => 
      jobs.value.slice(0, 5)
    )
    
    const averagePerJob = computed(() => 
      earnings.value.job_count > 0 ? (earnings.value.total / earnings.value.job_count).toFixed(2) : 0
    )
    
    // Currency formatting helper
    const formatCurrency = (amount) => {
      // Handle malformed amount values
      let cleanAmount = amount
      if (typeof amount === 'string') {
        // Remove any non-numeric characters except decimal point
        cleanAmount = amount.replace(/[^\d.]/g, '')
        // Handle cases where there might be multiple decimal points
        const parts = cleanAmount.split('.')
        if (parts.length > 2) {
          // Take only the first part as integer and second part as decimal
          cleanAmount = parts[0] + '.' + parts.slice(1).join('')
        }
      }
      return Number(cleanAmount || 0).toLocaleString('en-PK', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      })
    }
    
    // Profile completion percentage
    const profileCompletion = computed(() => {
      const requiredFields = ['name', 'father_name', 'cnic', 'date_of_birth', 'mobile_1', 'home_address', 'city', 'province', 'district', 'tehsil', 'years_of_experience', 'degree_year', 'customers_per_month', 'areas_of_expertise', 'clinic_latitude', 'clinic_longitude']
      const documentFields = ['cnic_front', 'cnic_back', 'degree_front', 'degree_back']
      
      const filledFields = requiredFields.filter(field => profileForm.value[field] && profileForm.value[field] !== '')
      const filledDocuments = documentFields.filter(field => documents.value[field])
      
      const totalFields = requiredFields.length + documentFields.length
      const totalFilled = filledFields.length + filledDocuments.length
      
      return Math.round((totalFilled / totalFields) * 100)
    })
    
    // Check if profile is complete for submission
    const isProfileComplete = computed(() => {
      return profileCompletion.value >= 80 // 80% completion required
    })
    
    // Stock status helper
    const getStockStatus = (quantity) => {
      if (quantity === 0) return 'Out of Stock'
      if (quantity <= 5) return 'Low Stock'
      return 'Available'
    }
    
    const getStockStatusClass = (quantity) => {
      if (quantity === 0) return 'bg-red-100 text-red-800'
      if (quantity <= 5) return 'bg-yellow-100 text-yellow-800'
      return 'bg-green-100 text-green-800'
    }
    
    // Status badge classes
    const getStatusBadgeClass = (status) => {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    }
    
    const loadProfile = async () => {
      try {
        console.log('Loading technician profile...')
        const response = await window.axios.get('/technician/profile')
        console.log('Profile response:', response.data)
        technician.value = response.data.technician
        console.log('Technician data:', technician.value)
        console.log('Degree year from API:', technician.value.degree_year)
        profileForm.value = {
          name: technician.value.name,
          father_name: technician.value.father_name,
          cnic: technician.value.cnic,
          date_of_birth: technician.value.date_of_birth,
          mobile_1: technician.value.mobile_1,
          mobile_2: technician.value.mobile_2,
          home_address: technician.value.home_address,
          clinic_address: technician.value.clinic_address,
          city: technician.value.city || '',
          province: technician.value.province || '',
          district: technician.value.district || '',
          tehsil: technician.value.tehsil || '',
          years_of_experience: technician.value.years_of_experience,
          degree_year: technician.value.degree_year ? String(technician.value.degree_year) : '',
          customers_per_month: technician.value.customers_per_month,
          areas_of_expertise: technician.value.areas_of_expertise,
          clinic_latitude: technician.value.clinic_latitude || '',
          clinic_longitude: technician.value.clinic_longitude || ''
        }
        
        console.log('Profile form after assignment:', profileForm.value)
        console.log('Degree year in form:', profileForm.value.degree_year)
        
        // Set technician location for Google Maps
        if (technician.value.clinic_latitude && technician.value.clinic_longitude) {
          technicianLocation.value = {
            lat: parseFloat(technician.value.clinic_latitude),
            lng: parseFloat(technician.value.clinic_longitude)
          }
        }
        
        // Load document URLs
        documents.value = {
          cnic_front: technician.value.cnic_front_image ? `/storage/${technician.value.cnic_front_image}` : null,
          cnic_back: technician.value.cnic_back_image ? `/storage/${technician.value.cnic_back_image}` : null,
          degree_front: technician.value.degree_front_image ? `/storage/${technician.value.degree_front_image}` : null,
          degree_back: technician.value.degree_back_image ? `/storage/${technician.value.degree_back_image}` : null
        }
        
        // Load working hours and service areas
        
        if (technician.value.working_hours_start) {
          // Format datetime to time string (HH:MM)
          const startTime = new Date(technician.value.working_hours_start)
          // Use UTC time to avoid timezone issues
          const hours = startTime.getUTCHours().toString().padStart(2, '0')
          const minutes = startTime.getUTCMinutes().toString().padStart(2, '0')
          workingHours.value.start_time = `${hours}:${minutes}`
        }
        if (technician.value.working_hours_end) {
          // Format datetime to time string (HH:MM)
          const endTime = new Date(technician.value.working_hours_end)
          // Use UTC time to avoid timezone issues
          const hours = endTime.getUTCHours().toString().padStart(2, '0')
          const minutes = endTime.getUTCMinutes().toString().padStart(2, '0')
          workingHours.value.end_time = `${hours}:${minutes}`
        }
        if (technician.value.available_24_7 !== undefined) {
          workingHours.value.available_24_7 = technician.value.available_24_7
        }
        if (technician.value.service_areas) {
          serviceAreas.value = technician.value.service_areas
        }
      } catch (error) {
        console.error('Error loading profile:', error)
        // Set default data for demo purposes
        technician.value = {
          name: 'Dr. Sarah Ahmed',
          availability: 'online',
          years_of_experience: 5,
          customers_per_month: 25
        }
      }
    }
    
    const updateTechnicianLocation = (location) => {
      technicianLocation.value = location
      profileForm.value.clinic_latitude = location.lat.toString()
      profileForm.value.clinic_longitude = location.lng.toString()
    }
    
    const loadJobs = async () => {
      try {
        console.log('Loading technician jobs...')
        const params = new URLSearchParams()
        if (jobStatusFilter.value !== 'all') {
          params.append('status', jobStatusFilter.value)
        }
        
        const response = await window.axios.get(`/technician/jobs?${params}`)
        console.log('Jobs response:', response.data)
        jobs.value = response.data.jobs?.data || response.data.jobs || []
        
        // Update job statistics after loading jobs
        loadJobStats()
      } catch (error) {
        console.error('Error loading jobs:', error)
        // Set sample data for demo purposes
        jobs.value = [
          {
            id: 1,
            status: 'pending',
            amount_paid: 200,
            created_at: new Date().toISOString(),
            order: {
              farmer: { 
                user: { name: 'Ahmed Ali' },
                mobile: '+923001234567',
                date_of_birth: '1985-01-15',
                status: 'active'
              },
              cattle_farm: { 
                name: 'Green Valley Farm',
                latitude: 31.5204,
                longitude: 74.3587,
                number_of_cows: 25,
                number_of_calves: 8,
                number_of_bulls: 3
              }
            }
          },
          {
            id: 2,
            status: 'completed',
            amount_paid: 250,
            created_at: new Date(Date.now() - 86400000).toISOString(),
            order: {
              farmer: { 
                user: { name: 'Fatima Khan' },
                mobile: '+923001234568',
                date_of_birth: '1988-03-22',
                status: 'active'
              },
              cattle_farm: { 
                name: 'Sunrise Dairy',
                latitude: 31.5404,
                longitude: 74.3687,
                number_of_cows: 30,
                number_of_calves: 12,
                number_of_bulls: 4
              }
            }
          }
        ]
        
        // Update job statistics after setting sample data
        loadJobStats()
      }
    }
    
    const loadEarnings = async () => {
      try {
        console.log('Loading technician earnings...')
        const response = await window.axios.get(`/technician/earnings?period=${earningsPeriod.value}`)
        console.log('Earnings response:', response.data)
        earnings.value = response.data.earnings
      } catch (error) {
        console.error('Error loading earnings:', error)
        // Set sample data for demo purposes
        earnings.value = {
          total: 1250,
          job_count: 5,
          jobs: [
            {
              id: 1,
              amount_paid: 200,
              created_at: new Date().toISOString(),
              order: { farmer: { user: { name: 'Ahmed Ali' } } }
            },
            {
              id: 2,
              amount_paid: 250,
              created_at: new Date(Date.now() - 86400000).toISOString(),
              order: { farmer: { user: { name: 'Fatima Khan' } } }
            }
          ]
        }
      }
    }
    
    const acceptJob = async (job) => {
      try {
        const estimatedVisitAt = new Date()
        estimatedVisitAt.setHours(estimatedVisitAt.getHours() + 2) // 2 hours from now
        
        await window.axios.put(`/technician/jobs/${job.id}/accept`, 
          { estimated_visit_at: estimatedVisitAt.toISOString() }
        )
        job.status = 'accepted'
        job.progress_status = 'accepted'
        loadJobStats()
      } catch (error) {
        console.error('Error accepting job:', error)
      }
    }
    
    const rejectJob = async (job) => {
      try {
        await window.axios.put(`/technician/jobs/${job.id}/reject`, 
          { notes: 'Rejected by technician' }
        )
        job.status = 'rejected'
        job.progress_status = 'rejected'
        loadJobStats()
      } catch (error) {
        console.error('Error rejecting job:', error)
      }
    }
    
    const completeJob = async (job) => {
      try {
        // In a real app, this would open a modal with completion details
        const completionData = {
          visit_latitude: 31.5204, // Current location
          visit_longitude: 74.3587,
          cow_in_heat: true,
          amount_paid: 200,
          semen_straw_code: 'STRAW' + Date.now(),
          cow_status: 'Healthy',
          delivery_notes: 'Successfully administered'
        }
        
        await window.axios.put(`/technician/jobs/${job.id}/complete`, 
          completionData
        )
        job.status = 'completed'
        job.progress_status = 'completed'
        job.amount_paid = completionData.amount_paid
        loadJobStats()
      } catch (error) {
        console.error('Error completing job:', error)
      }
    }
    
    const updateProfile = async () => {
      try {
        console.log('Updating profile with data:', profileForm.value)
        const response = await window.axios.put('/technician/profile', profileForm.value)
        console.log('Profile update response:', response.data)
        
        if (response.data.success) {
          // Show success message
          showNotification('Profile updated successfully!', 'success')
          // Reload profile to get updated data
          await loadProfile()
        } else {
          showNotification('Update failed: ' + (response.data.message || 'Unknown error'), 'error')
        }
      } catch (error) {
        console.error('Error updating profile:', error)
        if (error.response && error.response.data && error.response.data.message) {
          showNotification('Update failed: ' + error.response.data.message, 'error')
        } else {
          showNotification('Update failed. Please try again.', 'error')
        }
      }
    }
    
    const updateAvailability = async (availability) => {
      try {
        const data = {
          availability,
          working_hours_start: workingHours.value.start_time,
          working_hours_end: workingHours.value.end_time,
          available_24_7: workingHours.value.available_24_7,
          service_areas: serviceAreas.value
        }
        
        await window.axios.put('/technician/availability', data)
        technician.value.availability = availability
        technician.value.working_hours_start = workingHours.value.start_time
        technician.value.working_hours_end = workingHours.value.end_time
        technician.value.available_24_7 = workingHours.value.available_24_7
        technician.value.service_areas = serviceAreas.value
        
        alert('Availability settings updated successfully!')
      } catch (error) {
        console.error('Error updating availability:', error)
        alert('Failed to update availability settings')
      }
    }
    
    const saveAvailabilitySettings = async () => {
      try {
        const data = {
          availability: technician.value.availability,
          working_hours_start: workingHours.value.start_time,
          working_hours_end: workingHours.value.end_time,
          available_24_7: workingHours.value.available_24_7,
          service_areas: serviceAreas.value
        }
        
        await window.axios.put('/technician/availability', data)
        technician.value.working_hours_start = workingHours.value.start_time
        technician.value.working_hours_end = workingHours.value.end_time
        technician.value.available_24_7 = workingHours.value.available_24_7
        technician.value.service_areas = serviceAreas.value
        
        alert('Settings saved successfully!')
      } catch (error) {
        console.error('Error saving settings:', error)
        alert('Failed to save settings')
      }
    }
    
    // New functions for enhanced functionality
    const loadInventory = async () => {
      try {
        const response = await window.axios.get('/technician/inventory')
        if (response.data.success) {
          inventory.value = {
            total_items: response.data.total_items,
            available_items: response.data.available_items,
            low_stock_items: response.data.low_stock_items,
            out_of_stock: response.data.out_of_stock,
            items: response.data.items
          }
        } else {
          throw new Error(response.data.message || 'Failed to load inventory')
        }
      } catch (error) {
        console.error('Error loading inventory:', error)
        // Set empty data on error
        inventory.value = {
          total_items: 0,
          available_items: 0,
          low_stock_items: 0,
          out_of_stock: 0,
          items: []
        }
      }
    }
    
    const loadJobStats = () => {
      jobStats.value = {
        pending: jobs.value.filter(job => job.status === 'pending').length,
        completed: jobs.value.filter(job => job.status === 'completed').length,
        in_progress: jobs.value.filter(job => job.status === 'in_progress' || job.status === 'accepted').length,
        rejected: jobs.value.filter(job => job.status === 'rejected' || job.status === 'failed').length
      }
    }
    
    // Document upload handling
    const handleDocumentUpload = async (type, event) => {
      const file = event.target.files[0]
      if (!file) return
      
      // Validate file size (10MB max)
      if (file.size > 10 * 1024 * 1024) {
        alert('File size must be less than 10MB')
        return
      }
      
      // Validate file type
      const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf']
      if (!allowedTypes.includes(file.type)) {
        alert('Please upload a valid image (JPEG, PNG) or PDF file')
        return
      }
      
      uploading.value[type] = true
      
      try {
        const formData = new FormData()
        formData.append('document', file)
        formData.append('document_type', type)
        
        const response = await window.axios.post('/technician/upload-document', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        
        if (response.data.success) {
          documents.value[type] = response.data.file_url
          // Reload profile to get updated data
          await loadProfile()
        } else {
          alert('Upload failed: ' + response.data.message)
        }
      } catch (error) {
        console.error('Upload error:', error)
        alert('Upload failed. Please try again.')
      } finally {
        uploading.value[type] = false
        // Reset file input
        event.target.value = ''
      }
    }
    
    const deleteDocument = async (type) => {
      if (!confirm('Are you sure you want to delete this document?')) return
      
      try {
        const response = await window.axios.delete('/technician/delete-document', {
          data: { document_type: type }
        })
        
        if (response.data.success) {
          documents.value[type] = null
          // Reload profile to get updated data
          await loadProfile()
        } else {
          alert('Delete failed: ' + response.data.message)
        }
      } catch (error) {
        console.error('Delete error:', error)
        alert('Delete failed. Please try again.')
      }
    }
    
    const viewDocument = (url) => {
      window.open(url, '_blank')
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
    
    const dismissProfileAlert = () => {
      profileCompletionAlert.value.show = false
      localStorage.removeItem('showProfileCompletionAlert')
    }
    
    // Location services
    const getCurrentLocation = () => {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          (position) => {
            const lat = position.coords.latitude
            const lng = position.coords.longitude
            profileForm.value.latitude = lat
            profileForm.value.longitude = lng
            farmVisitForm.value.latitude = lat
            farmVisitForm.value.longitude = lng
          },
          (error) => {
            console.error('Error getting location:', error)
            alert('Unable to get your location. Please enter manually.')
          }
        )
      } else {
        alert('Geolocation is not supported by this browser.')
      }
    }
    
    // Profile submission for approval
    const submitForApproval = async () => {
      try {
        await window.axios.post('/technician/profile/submit-for-approval')
        alert('Profile submitted for approval successfully!')
        loadProfile()
      } catch (error) {
        console.error('Error submitting profile:', error)
        alert('Failed to submit profile. Please try again.')
      }
    }
    
    // Job management functions
    const provideETA = (job) => {
      selectedJob.value = job
      showETAModal.value = true
    }
    
    const submitETA = async () => {
      try {
        await window.axios.put(`/technician/jobs/${selectedJob.value.id}/eta`, etaForm.value)
        selectedJob.value.eta = etaForm.value.eta
        showETAModal.value = false
        etaForm.value = { eta: '', notes: '' }
        alert('ETA submitted successfully!')
      } catch (error) {
        console.error('Error submitting ETA:', error)
        alert('Failed to submit ETA. Please try again.')
      }
    }
    
    const startJob = async (job) => {
      try {
        await window.axios.put(`/technician/jobs/${job.id}/start`)
        job.status = 'in_progress'
        loadJobStats()
        alert('Job started successfully!')
      } catch (error) {
        console.error('Error starting job:', error)
        alert('Failed to start job. Please try again.')
      }
    }
    
    const openJobModal = (job) => {
      selectedJob.value = job
      farmVisitForm.value = {
        cow_in_heat: '',
        straw_code: '',
        amount_received: job.amount_paid || '',
        payment_method: '',
        latitude: '',
        longitude: '',
        notes: ''
      }
      showJobModal.value = true
    }
    
    const closeJobModal = () => {
      showJobModal.value = false
      selectedJob.value = null
    }
    
    const closeETAModal = () => {
      showETAModal.value = false
      selectedJob.value = null
    }
    
    // Farm visit functions
    const handleCowPhotoUpload = (event) => {
      const file = event.target.files[0]
      if (file) {
        console.log('Cow photo uploaded:', file.name)
      }
    }
    
    const handleStrawPhotoUpload = (event) => {
      const file = event.target.files[0]
      if (file) {
        console.log('Straw photo uploaded:', file.name)
      }
    }
    
    const scanBarcode = () => {
      // In a real app, this would open camera for barcode scanning
      alert('Barcode scanner would open here')
    }
    
    const markNoHeat = async () => {
      try {
        const data = {
          cow_in_heat: false,
          amount_received: farmVisitForm.value.amount_received,
          payment_method: farmVisitForm.value.payment_method,
          latitude: farmVisitForm.value.latitude,
          longitude: farmVisitForm.value.longitude,
          notes: farmVisitForm.value.notes
        }
        
        await window.axios.put(`/technician/jobs/${selectedJob.value.id}/no-heat`, data)
        selectedJob.value.status = 'no_heat'
        closeJobModal()
        loadJobStats()
        alert('Job marked as No Heat successfully!')
      } catch (error) {
        console.error('Error marking no heat:', error)
        alert('Failed to mark as no heat. Please try again.')
      }
    }
    
    const administerSemen = async () => {
      try {
        const data = {
          cow_in_heat: true,
          straw_code: farmVisitForm.value.straw_code,
          amount_received: farmVisitForm.value.amount_received,
          payment_method: farmVisitForm.value.payment_method,
          latitude: farmVisitForm.value.latitude,
          longitude: farmVisitForm.value.longitude,
          notes: farmVisitForm.value.notes
        }
        
        await window.axios.put(`/technician/jobs/${selectedJob.value.id}/administer`, data)
        selectedJob.value.status = 'administered'
        closeJobModal()
        loadJobStats()
        alert('Semen administered successfully!')
      } catch (error) {
        console.error('Error administering semen:', error)
        alert('Failed to administer semen. Please try again.')
      }
    }
    
    const viewJobDetails = (job) => {
      selectedJobForDetails.value = job
      showJobDetailsModal.value = true
    }
    
    const closeJobDetailsModal = () => {
      showJobDetailsModal.value = false
      selectedJobForDetails.value = null
    }
    
    // Service area management
    const addServiceArea = () => {
      serviceAreas.value.push({ name: '', radius: 10 })
    }
    
    const removeServiceArea = (index) => {
      serviceAreas.value.splice(index, 1)
    }
    
    // Inventory management
    const requestStock = (item) => {
      alert(`Requesting stock for ${item.bull_name}`)
    }
    
    const getJobStatusBadgeClass = (status) => {
      const classes = {
        pending: 'bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800 border border-yellow-200',
        accepted: 'bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 border border-blue-200',
        completed: 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200',
        rejected: 'bg-gradient-to-r from-red-100 to-rose-100 text-red-800 border border-red-200'
      }
      return classes[status] || 'bg-gradient-to-r from-gray-100 to-slate-100 text-gray-800 border border-gray-200'
    }
    
    const getAvailabilityBadgeClass = (availability) => {
      const classes = {
        online: 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200',
        offline: 'bg-gradient-to-r from-gray-100 to-slate-100 text-gray-800 border border-gray-200',
        on_leave: 'bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800 border border-yellow-200'
      }
      return classes[availability] || 'bg-gradient-to-r from-gray-100 to-slate-100 text-gray-800 border border-gray-200'
    }
    
    const formatDate = (date) => {
      return new Date(date).toLocaleDateString()
    }
    
    const logout = () => {
      authStore.logout()
      router.push('/login')
    }
    
    onMounted(async () => {
      console.log('Technician Dashboard mounted')
      console.log('Auth store user:', authStore.user)
      console.log('Auth store token:', authStore.token)
      console.log('Is authenticated:', authStore.isAuthenticated)
      
      // Check URL parameters for tab switching
      const urlParams = new URLSearchParams(window.location.search)
      const tabParam = urlParams.get('tab')
      if (tabParam === 'profile') {
        activeTab.value = 'profile'
      }
      
      await loadProfile()
      await loadJobs()
      loadEarnings()
      loadInventory()
      loadJobStats() // This should be called after loadJobs completes
      
      // Check if user should see profile completion alert
      if (localStorage.getItem('showProfileCompletionAlert') === 'true') {
        profileCompletionAlert.value.show = true
      }
    })
    
    return {
      activeTab,
      sidebarOpen,
      technician,
      jobs,
      earnings,
      earningsPeriod,
      jobStatusFilter,
      profileForm,
      tabs,
      pendingJobsCount,
      completedJobsCount,
      totalEarnings,
      recentJobs,
      averagePerJob,
      // New computed properties
      profileCompletion,
      isProfileComplete,
      getStockStatus,
      getStockStatusClass,
      getStatusBadgeClass,
      // New reactive variables
      showJobModal,
      showETAModal,
      showJobDetailsModal,
      selectedJob,
      selectedJobForDetails,
      jobStats,
      inventory,
      workingHours,
      serviceAreas,
      farmVisitForm,
      etaForm,
      technicianLocation,
      documents,
      uploading,
      notification,
      profileCompletionAlert,
      // Existing functions
      loadProfile,
      updateTechnicianLocation,
      loadJobs,
      loadEarnings,
      acceptJob,
      rejectJob,
      completeJob,
      updateProfile,
      updateAvailability,
      saveAvailabilitySettings,
      getJobStatusBadgeClass,
      getAvailabilityBadgeClass,
      formatDate,
      logout,
      // New functions
      loadInventory,
      loadJobStats,
      handleDocumentUpload,
      deleteDocument,
      viewDocument,
      showNotification,
      dismissProfileAlert,
      getCurrentLocation,
      submitForApproval,
      provideETA,
      submitETA,
      startJob,
      openJobModal,
      closeJobModal,
      closeETAModal,
      closeJobDetailsModal,
      handleCowPhotoUpload,
      handleStrawPhotoUpload,
      scanBarcode,
      markNoHeat,
      administerSemen,
      viewJobDetails,
      addServiceArea,
      removeServiceArea,
      requestStock,
      formatCurrency
    }
  },
  components: {
    GoogleMapLocationPicker
  }
}
</script>