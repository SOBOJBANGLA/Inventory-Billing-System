<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Products</h2>
      <div class="flex space-x-2">
        <button @click="fetchProducts" class="btn btn-secondary">
          Refresh
        </button>
        <button @click="showModal = true" class="btn btn-primary">
          Add Product
        </button>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-4 flex gap-4">
      <input 
        v-model="search" 
        @input="fetchProducts" 
        type="text" 
        placeholder="Search products..." 
        class="form-input flex-1"
      />
      <label class="flex items-center">
        <input v-model="lowStockOnly" @change="fetchProducts" type="checkbox" class="mr-2">
        Low Stock Only
      </label>
    </div>


    <!-- Products Table -->
    <div class="card">
      <div class="overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>Name</th>
              <th>SKU</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in (products.data || [])" :key="product.id">
              <td>{{ product.name }}</td>
              <td>{{ product.sku }}</td>
              <td>${{ product.price }}</td>
              <td :class="product.quantity <= 5 ? 'text-red-600 font-bold' : ''">
                {{ product.quantity }}
                <span v-if="product.quantity <= 5" class="text-xs text-red-500">(Low Stock)</span>
              </td>
              <td>{{ product.description || '-' }}</td>
              <td>
                <button @click="editProduct(product)" class="btn btn-secondary mr-2">
                  Edit
                </button>
                <button @click="deleteProduct(product.id)" class="btn btn-danger">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="products.last_page > 1" class="mt-4 flex justify-center">
        <nav class="flex space-x-2">
          <button 
            v-for="page in products.last_page" 
            :key="page"
            @click="currentPage = page; fetchProducts()"
            :class="[
              'px-3 py-1 rounded',
              currentPage === page ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'
            ]"
          >
            {{ page }}
          </button>
        </nav>
      </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">
          {{ editingProduct ? 'Edit Product' : 'Add Product' }}
        </h3>
        
        <form @submit.prevent="saveProduct">
          <div class="mb-4">
            <label class="form-label">Name</label>
            <input v-model="form.name" type="text" class="form-input" required>
          </div>
          
          <div class="mb-4">
            <label class="form-label">SKU</label>
            <input v-model="form.sku" type="text" class="form-input" required>
          </div>
          
          <div class="mb-4">
            <label class="form-label">Price</label>
            <input v-model="form.price" type="number" step="0.01" class="form-input" required>
          </div>
          
          <div class="mb-4">
            <label class="form-label">Quantity</label>
            <input v-model="form.quantity" type="number" class="form-input" required>
          </div>
          
          <div class="mb-4">
            <label class="form-label">Description</label>
            <textarea v-model="form.description" class="form-input" rows="3"></textarea>
          </div>
          
          <div class="flex justify-end space-x-2">
            <button type="button" @click="closeModal" class="btn btn-secondary">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary">
              {{ editingProduct ? 'Update' : 'Create' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProductList',
  data() {
    return {
      products: { data: [], last_page: 1 },
      search: '',
      lowStockOnly: false,
      currentPage: 1,
      showModal: false,
      editingProduct: null,
      form: {
        name: '',
        sku: '',
        price: '',
        quantity: '',
        description: ''
      }
    };
  },
  mounted() {
    this.fetchProducts();
  },
  methods: {
    async fetchProducts() {
      try {
        // Simple API call without parameters first
        const response = await axios.get('/api/products');
        console.log('Products API response:', response.data);
        this.products = response.data.data;
        console.log('Products set to:', this.products);
      } catch (error) {
        console.error('Fetch products error:', error);
        this.$parent.showToast('Failed to fetch products: ' + (error.response?.data?.message || error.message), 'error');
      }
    },
    
    async saveProduct() {
      try {
        if (this.editingProduct) {
          await axios.put(`/api/products/${this.editingProduct.id}`, this.form);
          this.$parent.showToast('Product updated successfully');
        } else {
          await axios.post('/api/products', this.form);
          this.$parent.showToast('Product created successfully');
        }
        
        this.closeModal();
        // Force refresh the products list
        await this.fetchProducts();
      } catch (error) {
        console.error('Save product error:', error);
        this.$parent.showToast('Failed to save product: ' + (error.response?.data?.message || error.message), 'error');
      }
    },
    
    editProduct(product) {
      this.editingProduct = product;
      this.form = { ...product };
      this.showModal = true;
    },
    
    async deleteProduct(id) {
      if (confirm('Are you sure you want to delete this product?')) {
        try {
          await axios.delete(`/api/products/${id}`);
          this.$parent.showToast('Product deleted successfully');
          this.fetchProducts();
        } catch (error) {
          this.$parent.showToast('Failed to delete product', 'error');
        }
      }
    },
    
    closeModal() {
      this.showModal = false;
      this.editingProduct = null;
      this.form = {
        name: '',
        sku: '',
        price: '',
        quantity: '',
        description: ''
      };
    },
    
  }
};
</script>
