# Smart Dine - Folder Structure

## Overview
This document describes the organized folder structure of the Smart Dine application.

## Directory Structure

```
smart-dine/
├── admin/                      # Admin panel pages
│   ├── index.php              # Admin dashboard
│   ├── manage_orders.php      # Order management
│   ├── manage_products.php    # Product management
│   ├── manage_users.php       # User management
│   └── order_detail.php       # Order details view
│
├── api/                        # API endpoints
│   ├── .php        # Add items to cart
│   ├── get_cart_count.php     # Get cart item count
│   ├── remove_from_cart.php   # Remove items from cart
│   └── update_cart_quantity.php # Update cart quantities
│
├── assets/                     # Static assets
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   ├── images/
│   │   └── food pics/         # Product images
│   └── js/
│       └── script.js          # Main JavaScript file
│
├── data/                       # Database and data files
│   ├── products.json          # Product data
│   ├── smartdine.sql          # Database schema
│   └── smartdine_updated.sql  # Updated database schema
│
├── docs/                       # Documentation
│   ├── README.md              # Project documentation
│   ├── FEATURES.md            # Feature list
│   └── FOLDER_STRUCTURE.md    # This file
│
├── includes/                   # Shared PHP includes
│   ├── connect.php            # Database connection
│   ├── header.php             # Common header
│   └── footer.php             # Common footer
│
├── pages/                      # Application pages
│   ├── auth/                  # Authentication pages
│   │   ├── login.php          # User login
│   │   ├── register.php       # User registration
│   │   └── logout.php         # User logout
│   │
│   ├── categories/            # Food category pages
│   │   ├── appetizers.php     # Appetizers menu
│   │   ├── main-courses.php   # Main courses menu
│   │   ├── desserts.php       # Desserts menu
│   │   └── beverages.php      # Beverages menu
│   │
│   ├── orders/                # Order-related pages
│   │   ├── orders.php         # Order history
│   │   └── order_details.php  # Individual order details
│   │
│   └── user/                  # User account pages
│       ├── profile.php        # User profile
│       ├── cart.php           # Shopping cart
│       ├── checkout.php       # Checkout process
│       └── search.php         # Product search
│
├── index.php                   # Landing page
├── home.php                    # Main dashboard
└── setup.php                   # Database setup script

```

## Page Categories

### Authentication (`pages/auth/`)
- User login and registration
- Session management
- Logout functionality

### User Pages (`pages/user/`)
- Profile management
- Shopping cart
- Checkout process
- Product search

### Category Pages (`pages/categories/`)
- Browse products by category
- Filter by restaurant
- View product details

### Order Pages (`pages/orders/`)
- View order history
- Track order status
- View order details

### Admin Panel (`admin/`)
- Dashboard with statistics
- Manage orders, products, and users
- View detailed order information

## Path References

### From Root Directory
- Use relative paths: `pages/auth/login.php`
- Assets: `assets/css/style.css`
- Includes: `includes/connect.php`

### From Subdirectories (e.g., `pages/user/`)
- Use `../../` to go back to root
- Assets: `../../assets/css/style.css`
- Includes: `../../includes/connect.php`
- Other pages: `../auth/login.php`

### From Admin Directory
- Use `../` to go back to root
- Assets: `../assets/css/style.css`
- Includes: `../includes/connect.php`

## Benefits of This Structure

1. **Organization**: Related files are grouped together
2. **Maintainability**: Easy to find and update specific features
3. **Scalability**: Simple to add new pages in appropriate categories
4. **Security**: Sensitive files (admin, api) are clearly separated
5. **Clarity**: Purpose of each directory is immediately clear

## Navigation

The application uses a smart base path system in `includes/header.php` that automatically adjusts navigation links based on the current page location. This ensures all navigation works correctly regardless of directory depth.
