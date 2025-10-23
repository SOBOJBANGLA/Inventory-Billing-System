<template>
  <div id="app" class="min-h-screen bg-gray-100">
    <!-- Login Screen -->
    <Login v-if="!isAuthenticated" @login-success="handleLoginSuccess" />
    
    <!-- Main Application -->
    <div v-else>
      <!-- Navigation -->
      <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
          <div class="flex justify-between h-16">
            <div class="flex items-center">
              <h1 class="text-xl font-bold text-gray-800">Inventory & Billing System</h1>
            </div>
            <div class="flex items-center space-x-4">
              <button 
                v-for="tab in tabs" 
                :key="tab.key"
                @click="activeTab = tab.key"
                :class="[
                  'px-4 py-2 rounded-md font-medium transition-colors',
                  activeTab === tab.key 
                    ? 'bg-blue-600 text-white' 
                    : 'text-gray-600 hover:text-gray-800'
                ]"
              >
                {{ tab.label }}
              </button>
              <ThemeToggle />
              <button @click="logout" class="btn btn-danger">
                Logout
              </button>
            </div>
          </div>
        </div>
      </nav>

      <!-- Main Content -->
      <main class="max-w-7xl mx-auto py-6 px-4">
        <!-- Dashboard Tab -->
        <Dashboard v-if="activeTab === 'dashboard'" />
        
        <!-- Products Tab -->
        <ProductList v-if="activeTab === 'products'" />
        
        <!-- Customers Tab -->
        <CustomerList v-if="activeTab === 'customers'" />
        
        <!-- Create Invoice Tab -->
        <InvoiceCreate v-if="activeTab === 'create-invoice'" />
        
        <!-- Invoices Tab -->
        <InvoiceList v-if="activeTab === 'invoices'" />
      </main>
    </div>

    <!-- Toast Notifications -->
    <div v-if="toast.show" class="fixed top-4 right-4 z-50">
      <div :class="[
        'px-6 py-4 rounded-lg shadow-lg',
        toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
      ]">
        {{ toast.message }}
      </div>
    </div>
  </div>
</template>

<script>
import Dashboard from './Dashboard.vue';
import ProductList from './ProductList.vue';
import CustomerList from './CustomerList.vue';
import InvoiceCreate from './InvoiceCreate.vue';
import InvoiceList from './InvoiceList.vue';
import Login from './Login.vue';
import ThemeToggle from './ThemeToggle.vue';

export default {
  name: 'App',
  components: {
    Dashboard,
    ProductList,
    CustomerList,
    InvoiceCreate,
    InvoiceList,
    Login,
    ThemeToggle,
  },
  data() {
    return {
      isAuthenticated: false,
      user: null,
      activeTab: 'dashboard',
      tabs: [
        { key: 'dashboard', label: 'Dashboard' },
        { key: 'products', label: 'Products' },
        { key: 'customers', label: 'Customers' },
        { key: 'create-invoice', label: 'Create Invoice' },
        { key: 'invoices', label: 'Invoices' },
      ],
      toast: {
        show: false,
        message: '',
        type: 'success'
      }
    };
  },
  mounted() {
    this.checkAuth();
  },
  methods: {
    checkAuth() {
      const token = localStorage.getItem('token');
      if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        this.isAuthenticated = true;
        console.log('Auth token set:', token);
      } else {
        console.log('No auth token found');
      }
    },
    handleLoginSuccess(user) {
      this.user = user;
      this.isAuthenticated = true;
      this.showToast('Login successful');
    },
    showToast(message, type = 'success') {
      this.toast = {
        show: true,
        message,
        type
      };
      setTimeout(() => {
        this.toast.show = false;
      }, 3000);
    },
    async logout() {
      try {
        await axios.post('/api/logout');
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        localStorage.removeItem('token');
        delete axios.defaults.headers.common['Authorization'];
        this.isAuthenticated = false;
        this.user = null;
        this.showToast('Logged out successfully');
      }
    }
  },
  provide() {
    return {
      showToast: this.showToast
    };
  }
};
</script>
