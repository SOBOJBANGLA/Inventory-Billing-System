<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Test Products API</h2>
    
    <div class="space-y-4">
      <button @click="testFetchProducts" class="btn btn-primary">Fetch Products</button>
      <button @click="testCreateProduct" class="btn btn-success">Create Test Product</button>
      <button @click="testDeleteProduct" class="btn btn-danger">Delete Test Product</button>
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
  name: 'TestProducts',
  data() {
    return {
      results: [],
      testProductId: null
    };
  },
  methods: {
    async testFetchProducts() {
      try {
        const response = await axios.get('/api/products');
        this.results.push({
          test: 'Fetch Products',
          success: true,
          data: response.data
        });
      } catch (error) {
        this.results.push({
          test: 'Fetch Products',
          success: false,
          error: error.response?.data?.message || error.message
        });
      }
    },
    
    async testCreateProduct() {
      try {
        const productData = {
          name: 'Test Product ' + Date.now(),
          sku: 'TEST' + Date.now(),
          price: 99.99,
          quantity: 10,
          description: 'Test product created via API'
        };
        
        const response = await axios.post('/api/products', productData);
        this.testProductId = response.data.data.id;
        
        this.results.push({
          test: 'Create Product',
          success: true,
          data: response.data
        });
      } catch (error) {
        this.results.push({
          test: 'Create Product',
          success: false,
          error: error.response?.data?.message || error.message
        });
      }
    },
    
    async testDeleteProduct() {
      if (!this.testProductId) {
        this.results.push({
          test: 'Delete Product',
          success: false,
          error: 'No test product ID available'
        });
        return;
      }
      
      try {
        await axios.delete(`/api/products/${this.testProductId}`);
        this.results.push({
          test: 'Delete Product',
          success: true,
          data: 'Product deleted successfully'
        });
        this.testProductId = null;
      } catch (error) {
        this.results.push({
          test: 'Delete Product',
          success: false,
          error: error.response?.data?.message || error.message
        });
      }
    }
  }
};
</script>
