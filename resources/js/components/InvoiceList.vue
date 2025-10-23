<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Invoices</h2>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="form-label">Customer</label>
          <select v-model="filters.customer_id" @change="fetchInvoices" class="form-input">
            <option value="">All Customers</option>
            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
              {{ customer.name }}
            </option>
          </select>
        </div>
        
        <div>
          <label class="form-label">Date From</label>
          <input 
            v-model="filters.date_from" 
            @change="fetchInvoices" 
            type="date" 
            class="form-input"
          />
        </div>
        
        <div>
          <label class="form-label">Date To</label>
          <input 
            v-model="filters.date_to" 
            @change="fetchInvoices" 
            type="date" 
            class="form-input"
          />
        </div>
      </div>
    </div>


    <!-- Invoices Table -->
    <div class="card">
      <div class="overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>Invoice #</th>
              <th>Customer</th>
              <th>Total</th>
              <th>Created By</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="invoice in (invoices.data || [])" :key="invoice.id">
              <td>#{{ invoice.id }}</td>
              <td>{{ invoice.customer.name }}</td>
              <td>${{ invoice.total }}</td>
              <td>{{ invoice.creator.name }}</td>
              <td>{{ formatDate(invoice.created_at) }}</td>
              <td>
                <button @click="viewInvoice(invoice)" class="btn btn-secondary mr-2">
                  View
                </button>
                <button @click="downloadPDF(invoice)" class="btn btn-primary mr-2">
                  PDF
                </button>
                <button @click="deleteInvoice(invoice.id)" class="btn btn-danger">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="invoices.last_page > 1" class="mt-4 flex justify-center">
        <nav class="flex space-x-2">
          <button 
            v-for="page in invoices.last_page" 
            :key="page"
            @click="currentPage = page; fetchInvoices()"
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

    <!-- Invoice Details Modal -->
    <div v-if="showInvoiceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-bold">Invoice #{{ selectedInvoice.id }}</h3>
          <button @click="showInvoiceModal = false" class="text-gray-500 hover:text-gray-700">
            ✕
          </button>
        </div>
        
        <div v-if="selectedInvoice" class="space-y-4">
          <!-- Customer Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <h4 class="font-bold">Customer Information</h4>
              <p><strong>Name:</strong> {{ selectedInvoice.customer.name }}</p>
              <p v-if="selectedInvoice.customer.phone"><strong>Phone:</strong> {{ selectedInvoice.customer.phone }}</p>
              <p v-if="selectedInvoice.customer.email"><strong>Email:</strong> {{ selectedInvoice.customer.email }}</p>
              <p v-if="selectedInvoice.customer.address"><strong>Address:</strong> {{ selectedInvoice.customer.address }}</p>
            </div>
            <div>
              <h4 class="font-bold">Invoice Details</h4>
              <p><strong>Date:</strong> {{ formatDate(selectedInvoice.created_at) }}</p>
              <p><strong>Created By:</strong> {{ selectedInvoice.creator.name }}</p>
              <p><strong>Total:</strong> ${{ selectedInvoice.total }}</p>
            </div>
          </div>
          
          <!-- Invoice Items -->
          <div>
            <h4 class="font-bold mb-2">Items</h4>
            <table class="table">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in selectedInvoice.invoice_items" :key="item.id">
                  <td>{{ item.product.name }}</td>
                  <td>${{ item.price }}</td>
                  <td>{{ item.quantity }}</td>
                  <td>${{ item.subtotal }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'InvoiceList',
  data() {
    return {
      invoices: { data: [], last_page: 1 },
      customers: [],
      currentPage: 1,
      showInvoiceModal: false,
      selectedInvoice: null,
      filters: {
        customer_id: '',
        date_from: '',
        date_to: ''
      }
    };
  },
  mounted() {
    this.fetchInvoices();
    this.fetchCustomers();
  },
  methods: {
    async fetchInvoices() {
      try {
        const params = new URLSearchParams({
          page: this.currentPage,
          ...this.filters
        });
        
        const response = await axios.get(`/api/invoices?${params}`);
        this.invoices = response.data.data;
      } catch (error) {
        this.$parent.showToast('Failed to fetch invoices', 'error');
      }
    },
    
    async fetchCustomers() {
      try {
        const response = await axios.get('/api/customers');
        this.customers = response.data.data.data;
      } catch (error) {
        this.$parent.showToast('Failed to fetch customers', 'error');
      }
    },
    
    async viewInvoice(invoice) {
      try {
        const response = await axios.get(`/api/invoices/${invoice.id}`);
        this.selectedInvoice = response.data.data;
        this.showInvoiceModal = true;
      } catch (error) {
        this.$parent.showToast('Failed to fetch invoice details', 'error');
      }
    },
    
    async downloadPDF(invoice) {
      try {
        const response = await axios.get(`/api/invoices/${invoice.id}/pdf`, {
          responseType: 'blob'
        });
        
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `invoice-${invoice.id}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
        
        this.$parent.showToast('PDF downloaded successfully');
      } catch (error) {
        this.$parent.showToast('Failed to download PDF', 'error');
      }
    },
    
    async deleteInvoice(id) {
      if (confirm('Are you sure you want to delete this invoice?')) {
        try {
          await axios.delete(`/api/invoices/${id}`);
          this.$parent.showToast('Invoice deleted successfully');
          this.fetchInvoices();
        } catch (error) {
          this.$parent.showToast('Failed to delete invoice', 'error');
        }
      }
    },
    
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString();
    }
  }
};
</script>
