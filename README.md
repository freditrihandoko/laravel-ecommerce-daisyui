# 🛍️ Laravel E-commerce with Livewire & DaisyUI

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3-FB70A9?style=for-the-badge&logo=livewire&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![DaisyUI](https://img.shields.io/badge/DaisyUI-Latest-5A0EF8?style=for-the-badge&logo=daisyui&logoColor=white)

A modern e-commerce platform built with Laravel 11, Livewire 3, and DaisyUI, featuring a responsive design and essential e-commerce functionalities.

## ✨ Features

- 📦 Product Management with Variants
- 🛒 Shopping Cart
- 🗂️ Category Management
- 💳 Order Management
- 🏷️ Discount System
- 📱 Responsive Design
- 🎨 Beautiful UI with DaisyUI
- 📦 Product Stock Management
- 🚚 Shipping Method Management
- 💰 Payment Method Integration
- 🖼️ Hero Slides Management
- 🔐 User Authentication & Authorization
- ⚙️ General Settings Configuration

## 📸 Screenshots

![Index](https://i.ibb.co.com/cY3CnxV/Screenshot-2025-01-08-at-22-46-25.png)

![Shopping Cart](https://i.ibb.co.com/GQGmFSm/Screenshot-2025-01-08-at-22-48-51.png)

![Orders](https://i.ibb.co.com/RbhWBLG/Screenshot-2025-01-08-at-22-51-14.png)

![Dashboard](https://i.ibb.co.com/ZYPVQL3/Screenshot-2025-01-08-at-22-52-20.png)

![Order Admin](https://i.ibb.co.com/PGdnKfX/Screenshot-2025-01-08-at-22-54-10.png)

![Hero Slides](https://i.ibb.co.com/rMFbdZf/Screenshot-2025-01-08-at-22-54-34.png)

## 🚀 Installation

1. Clone the repository
```bash
git clone https://github.com/freditrihandoko/laravel-ecommerce-daisyui.git
cd laravel-ecommerce-daisyui
```

2. Install dependencies
```bash
composer install
npm install
```

3. Copy the environment file
```bash
cp .env.example .env
```

4. Configure your database in `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Generate application key
```bash
php artisan key:generate
```

6. Run migrations and seeders
```bash
php artisan migrate --seed
```

7. Link storage
```bash
php artisan storage:link
```

8. Compile assets
```bash
npm run dev
```

9. Start the server
```bash
php artisan serve
```

## 👤 Default Users

After running the seeders, you can log in with these default accounts:

**Admin Account**
- Email: admin@gmail.com
- Password: password

**Customer Account**
- Email: user@gmail.com
- Password: password

## 🔄 Database Seeder

The DatabaseSeeder includes:
- Super Admin and Customer user creation
- Sample categories and products
- General settings
- Payment methods
- Shipping methods
- Order statuses

## 🛠️ Future Improvements

1. **Dynamic Shipping Integration**
   - Integration with shipping providers (JNE, J&T, SiCepat, etc.)
   - Real-time shipping cost calculation
   - Tracking number integration

2. **Payment Gateway Integration**
   - Integration with payment gateways (Midtrans, Xendit, etc.)
   - Automatic payment status updates
   - Payment notification handling

3. **Additional Features**
   - Wishlist functionality
   - Advanced product filtering
   - Product comparison
   - Customer chat support
   - Sms / Whatsapp notifications
   - Email notifications
   - Product recommendations
   - Multi-language support
   - Advanced reporting and analytics
   - Inventory management alerts

## 💾 Database Structure

The application includes the following main tables:
- Users
- Categories
- Products
- Product Variants
- Orders
- Order Items
- Discounts
- Addresses
- Payment Information
- Shipping Information
- Product Stocks
- General Settings

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📝 License

This project is open-sourced software licensed under the [MIT license](LICENSE.md).
