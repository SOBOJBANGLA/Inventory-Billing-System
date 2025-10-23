<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Create Invoice</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Customer Selection -->
      <div class="card">
        <h3 class="text-lg font-bold mb-4">Customer Information</h3>
        <div class="mb-4">
          <label class="form-label">Select Customer</label>
          <select v-model="selectedCustomer" class="form-input" required>
            <option value="">Choose a customer...</option>
            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
              {{ customer.name }} - {{ customer.phone || customer.email }}
            </option>
          </select>
        </div>
        
        <div v-if="selectedCustomer" class="bg-gray-50 p-4 rounded">
          <h4 class="font-medium">Customer Details:</h4>
          <p><strong>Name:</strong> {{ getCustomerDetails.name }}</p>
          <p v-if="getCustomerDetails.phone"><strong>Phone:</strong> {{ getCustomerDetails.phone }}</p>
          <p v-if="getCustomerDetails.email"><strong>Email:</strong> {{ getCustomerDetails.email }}</p>
          <p v-if="getCustomerDetails.address"><strong>Address:</strong> {{ getCustomerDetails.address }}</p>
        </div>
      </div>

      <!-- Product Search and Add -->
      <div class="card">
        <h3 class="text-lg font-bold mb-4">Add Products</h3>
        <div class="mb-4">
          <label class="form-label">Search Products</label>
          <input 
            v-model="productSearch" 
            @input="searchProducts" 
            type="text" 
            placeholder="Type product name or SKU..." 
            class="form-input"
          />
        </div>
        

        <!-- Product Search Results -->
        <div v-if="productSearchResults.length > 0" class="mb-4 max-h-40 overflow-y-auto border rounded">
          <div 
            v-for="product in productSearchResults" 
            :key="product.id"
            @click="addProductToInvoice(product)"
            class="p-2 hover:bg-gray-100 cursor-pointer border-b last:border-b-0"
          >
            <div class="flex justify-between">
              <span class="font-medium">{{ product.name }}</span>
              <span class="text-gray-600">${{ product.price }} (Stock: {{ product.quantity }})</span>
            </div>
            <div class="text-sm text-gray-500">{{ product.sku }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Invoice Items -->
    <div class="card mt-6">
      <h3 class="text-lg font-bold mb-4">Invoice Items</h3>
      
      <div v-if="invoiceItems.length === 0" class="text-center py-8 text-gray-500">
        <p>No items added yet. Search and select products above to add them to the invoice.</p>
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>Product</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Subtotal</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in invoiceItems" :key="index">
              <td>{{ item.product.name }}</td>
              <td>${{ item.product.price }}</td>
              <td>
                <input 
                  v-model.number="item.quantity" 
                  @input="updateSubtotal(index)"
                  @change="updateSubtotal(index)"
                  type="number" 
                  min="1" 
                  :max="item.product.quantity"
                  class="form-input w-20"
                />
              </td>
              <td>${{ (parseFloat(item.subtotal) || 0).toFixed(2) }}</td>
              <td>
                <button @click="removeItem(index)" class="btn btn-danger">
                  Remove
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Total -->
      <div v-if="invoiceItems.length > 0" class="mt-4 flex justify-end">
        <div class="text-right">
          <div class="text-2xl font-bold">
            Total: ${{ total.toFixed(2) }}
          </div>
        </div>
      </div>
      
      <!-- Create Invoice Button -->
      <div class="mt-6 flex justify-end">
        <button 
          @click="createInvoice" 
          :disabled="!selectedCustomer || invoiceItems.length === 0"
          class="btn btn-success text-lg px-8 py-3"
        >
          Create Invoice
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'InvoiceCreate',
  data() {
    return {
      customers: [],
      selectedCustomer: '',
      productSearch: '',
      productSearchResults: [],
      invoiceItems: []
    };
  },
  computed: {
    getCustomerDetails() {
      return this.customers.find(c => c.id == this.selectedCustomer) || {};
    },
    total() {
      return this.invoiceItems.reduce((sum, item) => sum + (parseFloat(item.subtotal) || 0), 0);
    }
  },
  mounted() {
    this.fetchCustomers();
  },
  methods: {
    async fetchCustomers() {
      try {
        const response = await axios.get('/api/customers');
        this.customers = response.data.data.data;
      } catch (error) {
        if (this.$parent && this.$parent.showToast) {
          this.$parent.showToast('Failed to fetch customers', 'error');
        }
      }
    },
    
    async searchProducts() {
      if (this.productSearch.length < 2) {
        this.productSearchResults = [];
        return;
      }
      
      try {
        const response = await axios.get(`/api/products?search=${this.productSearch}`);
        this.productSearchResults = response.data.data.data;
      } catch (error) {
        if (this.$parent && this.$parent.showToast) {
          this.$parent.showToast('Failed to search products', 'error');
        }
      }
    },
    
    addProductToInvoice(product) {
      try {
        console.log('Adding product to invoice:', product);
        console.log('Current invoice items:', this.invoiceItems);
        
        // Check if product already exists in invoice
        const existingItem = this.invoiceItems.find(item => item.product.id === product.id);
        
        if (existingItem) {
          if (existingItem.quantity < product.quantity) {
            existingItem.quantity++;
            this.updateSubtotal(this.invoiceItems.indexOf(existingItem));
          } else {
            if (this.$parent && this.$parent.showToast) {
              this.$parent.showToast('Cannot add more items - insufficient stock', 'error');
            }
          }
        } else {
          this.invoiceItems.push({
            product: product,
            quantity: 1,
            subtotal: parseFloat(product.price)
          });
        }
        
        console.log('Updated invoice items:', this.invoiceItems);
        this.productSearch = '';
        this.productSearchResults = [];
      } catch (error) {
        console.error('Error adding product to invoice:', error);
        // Don't let the error break the component
      }
    },
    
    updateSubtotal(index) {
      const item = this.invoiceItems[index];
      const newSubtotal = parseFloat(item.product.price) * item.quantity;
      item.subtotal = newSubtotal;
      console.log(`Updated subtotal for item ${index}: ${item.quantity} x $${item.product.price} = $${newSubtotal}`);
    },
    
    removeItem(index) {
      this.invoiceItems.splice(index, 1);
    },
    

    async createInvoice() {
      if (!this.selectedCustomer || this.invoiceItems.length === 0) {
        if (this.$parent && this.$parent.showToast) {
          this.$parent.showToast('Please select a customer and add products', 'error');
        }
        return;
      }
      
      try {
        const invoiceData = {
          customer_id: this.selectedCustomer,
          items: this.invoiceItems.map(item => ({
            product_id: item.product.id,
            quantity: item.quantity
          }))
        };
        
        const response = await axios.post('/api/invoices', invoiceData);
        if (this.$parent && this.$parent.showToast) {
          this.$parent.showToast('Invoice created successfully');
        }
        
        // Reset form
        this.selectedCustomer = '';
        this.invoiceItems = [];
        this.productSearch = '';
        this.productSearchResults = [];
        
      } catch (error) {
        const message = error.response?.data?.message || 'Failed to create invoice';
        if (this.$parent && this.$parent.showToast) {
          this.$parent.showToast(message, 'error');
        }
      }
    }
  }
};
</script>
