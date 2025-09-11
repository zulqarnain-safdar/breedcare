import axios from 'axios';
window.axios = axios;

// Configure axios defaults
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.headers.common['Content-Type'] = 'application/json';

// Set base URL for API calls
window.axios.defaults.baseURL = '/api';

// Add request interceptor to include auth token and CSRF token
window.axios.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
      config.headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
    }
    
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Add response interceptor to handle auth errors
window.axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Only redirect if not already on login page and not a login request
      const currentPath = window.location.pathname;
      const isLoginRequest = error.config?.url?.includes('/auth/login');
      
      if (currentPath !== '/login' && !isLoginRequest) {
        // Token expired or invalid, redirect to login
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '/login';
      }
    }
    return Promise.reject(error);
  }
);
