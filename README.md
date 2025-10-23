# Inventory & Billing System

A comprehensive single-store inventory and billing system built with Laravel 12, Vue.js 3, and MySQL.

## Features

### Backend (Laravel)
- ✅ **CRUD Operations**: Complete CRUD for products and customers
- ✅ **Invoice Management**: Create invoices with automatic stock deduction
- ✅ **API Endpoints**: RESTful API with Sanctum authentication
- ✅ **Validation**: Form request validation for all inputs
- ✅ **Relationships**: Proper Eloquent relationships (hasMany, belongsTo)
- ✅ **Pagination**: Built-in pagination for products and invoices
- ✅ **Search**: Search functionality for products and customers
- ✅ **Stock Management**: Automatic stock deduction on invoice creation

### Frontend (Vue.js)
- ✅ **Single Page Application**: Vue.js 3 with Composition API
- ✅ **Product Management**: Add, edit, delete, and search products
- ✅ **Customer Management**: Complete customer CRUD operations
- ✅ **Invoice Creation**: Dynamic invoice creation with product search
- ✅ **Invoice Listing**: View, filter, and manage invoices
- ✅ **Real-time Updates**: AJAX-based operations with no page reload
- ✅ **Toast Notifications**: Success/error message system
- ✅ **Responsive Design**: Tailwind CSS for modern UI

### Authentication
- ✅ **Laravel Sanctum**: API token authentication
- ✅ **Secure Routes**: Protected API endpoints
- ✅ **Login/Logout**: Complete authentication flow

### Database Structure
- **products**: id, name, sku, price, quantity, description
- **customers**: id, name, phone, email, address
- **invoices**: id, customer_id, total, created_by, created_at
- **invoice_items**: id, invoice_id, product_id, quantity, price, subtotal

## Installation

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL
- Laravel 12+

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd inventory-billing-system
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database configuration**
   - Create a MySQL database
   - Update `.env` file with your database credentials:
     ```
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=inventory_billing
     DB_USERNAME=your_username
     DB_PASSWORD=your_password
     ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Build assets**
   ```bash
   npm run build
   # OR for development:
   npm run dev
   ```

8. **Start the application**
   ```bash
   # Terminal 1 - Laravel server
   php artisan serve
   
   # Terminal 2 - Vite dev server (for development)
   npm run dev
   ```

## Default Login Credentials

- **Email**: admin@example.com
- **Password**: password

## Usage

### Products Management
- View all products with search and filtering
- Add new products with SKU, price, and quantity
- Edit existing products
- Delete products
- Low stock alerts (quantity ≤ 5)

### Customer Management
- Add customer information (name, phone, email, address)
- Edit customer details
- Delete customers
- Search customers

### Invoice Creation
1. Select a customer
2. Search and add products
3. Set quantities (validates stock availability)
4. Review total and create invoice
5. Stock is automatically deducted

### Invoice Management
- View all invoices with filters
- Filter by customer and date range
- View invoice details
- Delete invoices (restores stock)

## API Endpoints

### Authentication
- `POST /api/login` - User login
- `POST /api/logout` - User logout

### Products
- `GET /api/products` - List products (with search & pagination)
- `POST /api/products` - Create product
- `GET /api/products/{id}` - Get product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product
- `GET /api/products/low-stock` - Get low stock products

### Customers
- `GET /api/customers` - List customers (with search & pagination)
- `POST /api/customers` - Create customer
- `GET /api/customers/{id}` - Get customer
- `PUT /api/customers/{id}` - Update customer
- `DELETE /api/customers/{id}` - Delete customer

### Invoices
- `GET /api/invoices` - List invoices (with filters & pagination)
- `POST /api/invoices` - Create invoice
- `GET /api/invoices/{id}` - Get invoice
- `DELETE /api/invoices/{id}` - Delete invoice
- `GET /api/invoices/{id}/pdf` - Download PDF

## Technology Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Vue.js 3, Tailwind CSS
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **Build Tool**: Vite
- **Additional**: Chart.js, DomPDF

## Development

### Running in Development Mode
```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

### Building for Production
```bash
npm run build
```

## Features Implemented

✅ **Core Requirements**
- Laravel 10+ backend
- Vue.js frontend with Vite
- MySQL database
- Complete CRUD operations
- API with authentication
- Form validation
- Pagination and search
- Stock management
- Invoice generation

✅ **Authentication**
- Laravel Sanctum integration
- Protected API routes
- Login/logout functionality

✅ **Database**
- Proper relationships
- Seeders with demo data
- Migrations for all tables

## Future Enhancements (Bonus Features)

- [ ] PDF invoice generation
- [ ] Stock alerts dashboard
- [ ] Sales charts and analytics
- [ ] Dark/light theme toggle
- [ ] Email notifications
- [ ] Barcode scanning
- [ ] Multi-store support

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).