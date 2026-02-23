# SmartDine - Multi-Restaurant Food Ordering Platform

A comprehensive PHP-based food ordering system supporting multiple restaurants with user authentication, cart management, order tracking, and admin panel.

## Features

### User Features
- Multi-restaurant browsing
- Product categories (Appetizers, Main Courses, Desserts, Beverages)
- Shopping cart with session management
- User registration and login
- Order placement and tracking
- Order history with detailed views
- User profile management
- Password change functionality

### Admin Features
- Admin dashboard with statistics
- Order management (view, update status)
- Product management (add, delete)
- User overview
- Revenue tracking

## Tech Stack
- PHP 7.4+
- MySQL/MariaDB
- HTML5, CSS3, JavaScript
- PDO for database operations
- Session-based authentication

## Installation

1. Clone the repository
2. Import the database:
   ```bash
   mysql -u root -p < data/smartdine_updated.sql
   ```
3. Configure database connection in `includes/connect.php`
4. Place food images in `assets/images/food pics/`
5. Access via web server (Apache/Nginx)

## Demo Credentials

**Users:**
- demo1 / password
- demo2 / password

**Admin:**
- admin / admin123

## Project Structure
- `/admin` - Admin panel pages
- `/api` - AJAX endpoints for cart operations
- `/assets` - CSS, JS, and images
- `/data` - Database schemas and product data
- `/includes` - Database connection

## Documentation
See [FEATURES.md](FEATURES.md) for detailed feature documentation.

## License
Educational/Personal Use