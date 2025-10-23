<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Debug Authentication & API</h2>
    
    <div class="space-y-4">
      <div>
        <strong>Authentication Status:</strong>
        <span :class="isAuthenticated ? 'text-green-600' : 'text-red-600'">
          {{ isAuthenticated ? 'AUTHENTICATED' : 'NOT AUTHENTICATED' }}
        </span>
      </div>
      
      <div>
        <strong>Token:</strong>
        <span class="text-gray-600">{{ token ? 'Present' : 'Missing' }}</span>
      </div>
      
      <div>
        <strong>Axios Headers:</strong>
        <pre class="bg-gray-100 p-2 rounded text-sm">{{ JSON.stringify(axiosHeaders, null, 2) }}</pre>
      </div>
      
      <button @click="testProducts" class="btn btn-primary">Test Products API</button>
      <button @click="testCustomers" class="btn btn-secondary">Test Customers API</button>
      <button @click="clearAuth" class="btn btn-danger">Clear Auth & Reload</button>
    </div>
    
    <div v-if="results.length > 0" class="mt-6">
      <h3 class="text-lg font-bold mb-2">API Test Results:</h3>
      <div v-for="(result, index) in results" :key="index" class="mb-2 p-3 bg-gray-100 rounded">
        <strong>{{ result.test }}:</strong> 
        <span :class="result.success ? 'text-green-600' : 'text-red-600'">
          {{ result.success ? 'SUCCESS' : 'FAILED' }}
        </span>
        <div v-if="result.error" class="text-red-600 text-sm mt-1">{{ result.error }}</div>
        <div v-if="result.data" class="text-gray-600 text-sm mt-1 max-h-32 overflow-y-auto">{{ JSON.stringify(result.data) }}</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DebugAuth',
  data() {
    return {
      results: []
    };
  },
  computed: {
    isAuthenticated() {
      return this.$parent.isAuthenticated;
    },
    token() {
      return localStorage.getItem('token');
    },
    axiosHeaders() {
      return axios.defaults.headers.common;
    }
  },
  methods: {
    async testProducts() {
      try {
        const response = await axios.get('/api/products');
        this.results.push({
          test: 'Products API',
          success: true,
          data: response.data
        });
      } catch (error) {
        this.results.push({
          test: 'Products API',
          success: false,
          error: error.response?.data?.message || error.message
        });
      }
    },
    
    async testCustomers() {
      try {
        const response = await axios.get('/api/customers');
        this.results.push({
          test: 'Customers API',
          success: true,
          data: response.data
        });
      } catch (error) {
        this.results.push({
          test: 'Customers API',
          success: false,
          error: error.response?.data?.message || error.message
        });
      }
    },
    
    clearAuth() {
      localStorage.removeItem('token');
      delete axios.defaults.headers.common['Authorization'];
      this.$parent.isAuthenticated = false;
      this.$parent.user = null;
      window.location.reload();
    }
  }
};
</script>
