<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="card">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100 text-blue-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Products</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.totalProducts }}</p>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100 text-green-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Customers</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.totalCustomers }}</p>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100 text-purple-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Invoices</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.totalInvoices }}</p>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-900">${{ stats.totalRevenue }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Low Stock Alert -->
      <div class="card">
        <h3 class="text-lg font-bold mb-4 text-red-600">⚠️ Low Stock Alert</h3>
        <div v-if="lowStockProducts.length === 0" class="text-green-600">
          ✅ All products are well stocked!
        </div>
        <div v-else class="space-y-2">
          <div v-for="product in lowStockProducts" :key="product.id" class="flex justify-between items-center p-2 bg-red-50 rounded">
            <span class="font-medium">{{ product.name }}</span>
            <span class="text-red-600 font-bold">{{ product.quantity }} left</span>
          </div>
        </div>
      </div>

      <!-- Recent Invoices -->
      <div class="card">
        <h3 class="text-lg font-bold mb-4">Recent Invoices</h3>
        <div v-if="recentInvoices.length === 0" class="text-gray-500">
          No invoices yet
        </div>
        <div v-else class="space-y-2">
          <div v-for="invoice in recentInvoices" :key="invoice.id" class="flex justify-between items-center p-2 bg-gray-50 rounded">
            <div>
              <span class="font-medium">#{{ invoice.id }}</span>
              <span class="text-gray-600 ml-2">{{ invoice.customer.name }}</span>
            </div>
            <span class="font-bold">${{ invoice.total }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Sales Chart -->
    <div class="card mt-6">
      <h3 class="text-lg font-bold mb-4">Sales Overview</h3>
      <div class="h-64">
        <canvas ref="salesChart"></canvas>
      </div>
    </div>
  </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

export default {
  name: 'Dashboard',
  data() {
    return {
      stats: {
        totalProducts: 0,
        totalCustomers: 0,
        totalInvoices: 0,
        totalRevenue: 0
      },
      lowStockProducts: [],
      recentInvoices: [],
      salesChart: null
    };
  },
  mounted() {
    this.fetchDashboardData();
  },
  methods: {
    async fetchDashboardData() {
      try {
        // Fetch stats
        const [productsRes, customersRes, invoicesRes, lowStockRes] = await Promise.all([
          axios.get('/api/products'),
          axios.get('/api/customers'),
          axios.get('/api/invoices'),
          axios.get('/api/products/low-stock')
        ]);

        // Fix the data structure access
        this.stats.totalProducts = productsRes.data.data.total || productsRes.data.data.data.length;
        this.stats.totalCustomers = customersRes.data.data.total || customersRes.data.data.data.length;
        this.stats.totalInvoices = invoicesRes.data.data.total || invoicesRes.data.data.data.length;
        this.stats.totalRevenue = invoicesRes.data.data.data.reduce((sum, invoice) => sum + parseFloat(invoice.total), 0).toFixed(2);
        
        this.lowStockProducts = lowStockRes.data.data || [];
        this.recentInvoices = invoicesRes.data.data.data.slice(0, 5);

        // Create sales chart
        this.createSalesChart();
      } catch (error) {
        console.error('Dashboard error:', error);
        this.$parent.showToast('Failed to fetch dashboard data: ' + (error.response?.data?.message || error.message), 'error');
      }
    },
    
    createSalesChart() {
      const ctx = this.$refs.salesChart.getContext('2d');
      
      // Sample data - in real app, you'd fetch this from API
      const salesData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
          label: 'Sales',
          data: [1200, 1900, 3000, 5000, 2000, 3000],
          borderColor: 'rgb(59, 130, 246)',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          tension: 0.1
        }]
      };

      this.salesChart = new Chart(ctx, {
        type: 'line',
        data: salesData,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    }
  }
};
</script>
