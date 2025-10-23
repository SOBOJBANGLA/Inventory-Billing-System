<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-4">API Debug</h2>
    
    <div class="space-y-4">
      <button @click="testLogin" class="btn btn-primary">Test Login</button>
      <button @click="testProducts" class="btn btn-secondary">Test Products API</button>
      <button @click="testCustomers" class="btn btn-success">Test Customers API</button>
      <button @click="testInvoices" class="btn btn-danger">Test Invoices API</button>
    </div>
    
    <div v-if="results.length > 0" class="mt-6">
      <h3 class="text-lg font-bold mb-2">Results:</h3>
      <div v-for="(result, index) in results" :key="index" class="mb-2 p-3 bg-gray-100 rounded">
        <strong>{{ result.test }}:</strong> 
        <span :class="result.success ? 'text-green-600' : 'text-red-600'">
          {{ result.success ? 'SUCCESS' : 'FAILED' }}
        </span>
        <div v-if="result.error" class="text-red-600 text-sm mt-1">{{ result.error }}</div>
        <div v-if="result.data" class="text-gray-600 text-sm mt-1">{{ JSON.stringify(result.data) }}</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DebugApi',
  data() {
    return {
      results: []
    };
  },
  methods: {
    async testLogin() {
      try {
        const response = await axios.post('/api/login', {
          email: 'admin@gmail.com',
          password: '12345678'
        });
        
        this.results.push({
          test: 'Login',
          success: true,
          data: response.data
        });
        
        // Store token for other tests
        if (response.data.token) {
          localStorage.setItem('token', response.data.token);
          axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        }
      } catch (error) {
        this.results.push({
          test: 'Login',
          success: false,
          error: error.response?.data?.message || error.message
        });
      }
    },
    
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
    
    async testInvoices() {
      try {
        const response = await axios.get('/api/invoices');
        this.results.push({
          test: 'Invoices API',
          success: true,
          data: response.data
        });
      } catch (error) {
        this.results.push({
          test: 'Invoices API',
          success: false,
          error: error.response?.data?.message || error.message
        });
      }
    }
  }
};
</script>
