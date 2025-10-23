<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Customers</h2>
      <button @click="showModal = true" class="btn btn-primary">
        Add Customer
      </button>
    </div>

    <!-- Search -->
    <div class="mb-4">
      <input 
        v-model="search" 
        @input="fetchCustomers" 
        type="text" 
        placeholder="Search customers..." 
        class="form-input w-full max-w-md"
      />
    </div>

    <!-- Customers Table -->
    <div class="card">
      <div class="overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Phone</th>
              <th>Email</th>
              <th>Address</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="customer in customers.data" :key="customer.id">
              <td>{{ customer.name }}</td>
              <td>{{ customer.phone || '-' }}</td>
              <td>{{ customer.email || '-' }}</td>
              <td>{{ customer.address || '-' }}</td>
              <td>
                <button @click="editCustomer(customer)" class="btn btn-secondary mr-2">
                  Edit
                </button>
                <button @click="deleteCustomer(customer.id)" class="btn btn-danger">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="customers.last_page > 1" class="mt-4 flex justify-center">
        <nav class="flex space-x-2">
          <button 
            v-for="page in customers.last_page" 
            :key="page"
            @click="currentPage = page; fetchCustomers()"
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

    <!-- Add/Edit Customer Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">
          {{ editingCustomer ? 'Edit Customer' : 'Add Customer' }}
        </h3>
        
        <form @submit.prevent="saveCustomer">
          <div class="mb-4">
            <label class="form-label">Name</label>
            <input v-model="form.name" type="text" class="form-input" required>
          </div>
          
          <div class="mb-4">
            <label class="form-label">Phone</label>
            <input v-model="form.phone" type="text" class="form-input">
          </div>
          
          <div class="mb-4">
            <label class="form-label">Email</label>
            <input v-model="form.email" type="email" class="form-input">
          </div>
          
          <div class="mb-4">
            <label class="form-label">Address</label>
            <textarea v-model="form.address" class="form-input" rows="3"></textarea>
          </div>
          
          <div class="flex justify-end space-x-2">
            <button type="button" @click="closeModal" class="btn btn-secondary">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary">
              {{ editingCustomer ? 'Update' : 'Create' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CustomerList',
  data() {
    return {
      customers: { data: [], last_page: 1 },
      search: '',
      currentPage: 1,
      showModal: false,
      editingCustomer: null,
      form: {
        name: '',
        phone: '',
        email: '',
        address: ''
      }
    };
  },
  mounted() {
    this.fetchCustomers();
  },
  methods: {
    async fetchCustomers() {
      try {
        const params = new URLSearchParams({
          page: this.currentPage,
          search: this.search
        });
        
        const response = await axios.get(`/api/customers?${params}`);
        this.customers = response.data.data;
      } catch (error) {
        this.$parent.showToast('Failed to fetch customers', 'error');
      }
    },
    
    async saveCustomer() {
      try {
        if (this.editingCustomer) {
          await axios.put(`/api/customers/${this.editingCustomer.id}`, this.form);
          this.$parent.showToast('Customer updated successfully');
        } else {
          await axios.post('/api/customers', this.form);
          this.$parent.showToast('Customer created successfully');
        }
        
        this.closeModal();
        this.fetchCustomers();
      } catch (error) {
        this.$parent.showToast('Failed to save customer', 'error');
      }
    },
    
    editCustomer(customer) {
      this.editingCustomer = customer;
      this.form = { ...customer };
      this.showModal = true;
    },
    
    async deleteCustomer(id) {
      if (confirm('Are you sure you want to delete this customer?')) {
        try {
          await axios.delete(`/api/customers/${id}`);
          this.$parent.showToast('Customer deleted successfully');
          this.fetchCustomers();
        } catch (error) {
          this.$parent.showToast('Failed to delete customer', 'error');
        }
      }
    },
    
    closeModal() {
      this.showModal = false;
      this.editingCustomer = null;
      this.form = {
        name: '',
        phone: '',
        email: '',
        address: ''
      };
    }
  }
};
</script>
