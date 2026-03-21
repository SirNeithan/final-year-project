# SmartDine Hub — Project Proposal

**Student Signature:** ________________________  
**Date:** 28th October, 2025  
**Supervisor Approval:** ________________________  
**Date:** ________________________

---

## EXECUTIVE SUMMARY

SmartDine Hub is an integrated web-based multi-restaurant food ordering platform designed to address critical inefficiencies in how customers discover, browse, and order food from multiple restaurants. The system provides end-to-end digitization of the food ordering experience through an intuitive customer-facing interface, real-time cart management, order tracking, and an administrative backend for restaurant and order management.

Current food ordering approaches suffer from fragmented experiences — customers must visit multiple websites or physical locations to compare menus across restaurants. SmartDine Hub addresses this by aggregating multiple restaurants under a single platform, organized by geographic region, enabling customers to browse, compare, and order from any restaurant in one seamless session.

The project is implemented using PHP, MySQL, and vanilla JavaScript, deployed on a WAMP/LAMP stack, and targets the Ugandan market with pricing in Ugandan Shillings (UGX).

---

## 1. INTRODUCTION

### 1.1 Background

The food service industry in Uganda is experiencing rapid growth, with an increasing number of restaurants operating across Kampala and other major cities. However, customers face significant friction when trying to discover restaurants, browse menus, and place orders — particularly when they want to compare options across different cuisines or locations.

Digital transformation in the hospitality sector has become imperative. Restaurants seek integrated solutions that streamline customer interactions while providing actionable business intelligence. Many existing systems are either too expensive for local establishments, not tailored to the Ugandan market, or require separate platforms that do not communicate effectively.

### 1.2 Project Rationale

SmartDine Hub addresses the need for a unified food ordering platform that aggregates multiple restaurants across Uganda's regions into a single, cohesive web application. By leveraging PHP, MySQL, and modern web technologies, the platform provides:

- Customers with a single destination to discover and order from multiple restaurants
- Restaurant administrators with tools to manage products, orders, and users
- Business owners with analytics and reporting to support data-driven decisions

The platform is specifically designed for the Ugandan context, using UGX pricing, local restaurant names, and regional organization (Central, Eastern, Western, Northern Uganda).

---

## 2. PROBLEM STATEMENT

Food ordering in Uganda faces several interconnected challenges:

**Customer Discovery Problems:**
- Customers must visit multiple websites or physically travel to compare restaurant options
- No centralized platform aggregates menus from restaurants across different regions
- Difficulty filtering by cuisine type, region, or price range

**Order Management Issues:**
- Manual order-taking processes result in errors during busy periods
- Poor communication between front-of-house and kitchen staff causes delays
- No real-time order status tracking for customers

**Administrative Inefficiencies:**
- Restaurant owners lack tools to manage product listings, stock status, and pricing from a single interface
- No centralized view of orders, revenue, or customer activity
- Manual processes for tracking which products are in or out of stock

**Customer Engagement Gaps:**
- No systematic approach to capturing customer feedback and reviews
- Limited ability to implement loyalty or repeat-order features
- No data on customer browsing and ordering behavior

These challenges collectively result in lost revenue, poor customer experience, and reduced competitiveness for local restaurants.

---

## 3. PROJECT OBJECTIVES

### 3.1 Main Objective

To design, develop, and deploy a web-based multi-restaurant food ordering platform that enables customers across Uganda to discover restaurants by region, browse menus, add items to a session cart, and place orders for pay-on-delivery fulfillment — while providing administrators with tools to manage the full operational lifecycle.

### 3.2 Specific Objectives

- **User Authentication:** Implement secure registration, login, logout, password reset, and profile management for all users.
- **Multi-Restaurant Browsing:** Organize 8 restaurants across 4 Ugandan regions (Central, Eastern, Western, Northern) with dedicated restaurant pages showing menus, descriptions, opening hours, and customer reviews.
- **Product Catalog:** Maintain a product catalog of 30+ menu items across 4 categories (Appetizers, Main Courses, Desserts, Beverages) loaded from a JSON data source for fast frontend rendering.
- **Cart & Checkout:** Implement a session-based shopping cart with quantity management, delivery zone selection, and pay-on-delivery checkout flow.
- **Order Management:** Enable customers to place orders, view order history, track order status through a visual timeline, and reorder previous orders.
- **Admin Dashboard:** Provide administrators with revenue charts, order management, product management (including stock toggling and JSON sync), user management, and user activity tracking.
- **Review System:** Allow authenticated customers to submit star ratings and comments for restaurants, with average ratings displayed on restaurant pages.
- **Search & Filter:** Enable customers to search products by name, category, region, and restaurant with real-time filtering.

---

## 4. SCOPE OF THE PROJECT

### 4.1 In Scope

**Core Functionalities:**
- User registration, login, logout, forgot/reset password
- Multi-region restaurant browsing (Central, Eastern, Western, Northern)
- Restaurant detail pages with menu, info cards, and reviews
- Product catalog with categories: Appetizers, Main Courses, Desserts, Beverages
- Session-based shopping cart with add, remove, and quantity update
- Checkout with delivery zone selection and pay-on-delivery
- Order history with status timeline and reorder functionality
- Admin dashboard with revenue charts (Chart.js), order/product/user management
- User activity tracking (online users, login history, pages visited)
- Product stock management with JSON synchronization
- Customer review and rating submission

**Technical Deliverables:**
- Fully functional PHP web application
- MySQL database with 8+ tables
- JSON-based product catalog (`data/products.json`)
- SQL migration scripts (`data/missing_features.sql`)
- Responsive CSS with Poppins/Playfair Display typography
- Remix Icon integration replacing all emoji icons
- Dockerfile for containerized deployment

### 4.2 Out of Scope

- Native mobile applications (iOS/Android)
- Real-time payment gateway integration (pay-on-delivery only)
- Kitchen display system
- Automated staff scheduling
- SMS/email order notifications (infrastructure not configured)

### 4.3 Target Users

**Primary Users:**
- Customers browsing and ordering food
- Restaurant administrators managing products and orders
- System administrators managing users and platform settings

**Secondary Users:**
- Restaurant owners reviewing analytics and revenue reports

---

## 5. LITERATURE REVIEW

### 5.1 Multi-Restaurant Ordering Platforms

Aggregator platforms such as Uber Eats, Jumia Food, and Glovo have demonstrated that consolidating multiple restaurants under a single ordering interface significantly increases order volume and customer retention. Research indicates that customers are 3x more likely to complete an order when they can compare options without leaving a platform (Law et al., 2009).

### 5.2 Web Technologies in Hospitality

PHP remains the dominant server-side language for hospitality web applications due to its low hosting cost, wide availability, and mature ecosystem. MySQL provides reliable relational data storage for transactional systems. Studies show that PHP/MySQL stacks power over 60% of small-to-medium hospitality platforms globally (Paraskevas & Buhalis, 2002).

### 5.3 Session-Based Cart Architecture

Session-based shopping carts, as opposed to database-persisted carts, offer lower latency and reduced database load for anonymous or short-session users. This approach is well-suited for food ordering where cart lifetime is typically under 30 minutes (Gregoire et al., 2011).

### 5.4 Regional Organization in Food Discovery

Organizing restaurants by geographic region improves discoverability in markets where customers associate cuisine quality with location. In the Ugandan context, regional identity (Kampala Central, Jinja, Mbarara, Gulu) is a meaningful filter for customers choosing where to order from.

### 5.5 Customer Reviews and Trust

Integrated review systems increase customer trust and conversion rates. Restaurants with visible star ratings experience 15-25% higher click-through rates compared to listings without ratings (Cheng, 2019). SmartDine Hub implements a 5-star rating system with text comments per restaurant.

### 5.6 Gap Analysis

Existing platforms available in Uganda either:
- Charge high commission fees that are prohibitive for small restaurants
- Lack regional organization relevant to Ugandan geography
- Do not support UGX pricing natively
- Require mobile apps rather than accessible web interfaces

SmartDine Hub addresses all four gaps with a zero-commission, web-first, UGX-native platform organized around Ugandan regions.

---

## 6. METHODOLOGY

### 6.1 Development Approach

The project uses an iterative development approach with feature-based milestones, enabling continuous testing and refinement. Development proceeded in logical layers: database and authentication first, then product catalog and browsing, then cart and checkout, then admin tools, and finally UI polish and icon replacement.

### 6.2 System Development Phases

**Phase 1: Requirements & Design**
- Identified 8 partner restaurants across 4 Ugandan regions
- Defined product categories and catalog structure
- Designed database schema (users, products, orders, order_items, reviews, restaurants, delivery_zones, user_activity, user_logins)
- Created wireframes for customer and admin flows

**Phase 2: Core Implementation**
- Built authentication system (register, login, logout, forgot/reset password)
- Implemented product catalog loaded from `data/products.json`
- Built session-based cart with add, remove, and quantity management APIs
- Developed restaurant pages with menu display and review submission

**Phase 3: Order & Admin Systems**
- Implemented checkout with delivery zone pricing and pay-on-delivery
- Built order creation API (`api/create_order.php`) with cart-to-order conversion
- Developed admin dashboard with Chart.js revenue and status charts
- Built product management with stock toggle and JSON sync
- Implemented user activity tracking with online user detection

**Phase 4: UI & Integration**
- Rebuilt home page with region-based restaurant discovery flow
- Replaced all emoji icons with Remix Icons (CDN)
- Fixed BASE_PATH calculation for correct AJAX URLs across all subdirectories
- Standardized all Add-to-Cart buttons to use `data-add-to-cart` attributes

**Phase 5: Testing & Refinement**
- Tested cart functionality across all page depths
- Verified BASE_PATH resolution at `localhost/final-year-project/`
- Tested order placement, status updates, and reorder flow
- Validated admin product sync between MySQL and `products.json`

### 6.3 Data Collection Methods

- **Product Data:** Manually curated 30 menu items across 8 restaurants with descriptions, prices (UGX), and food photography
- **Restaurant Data:** Seeded via SQL with real Ugandan city addresses and phone numbers
- **Delivery Zones:** 7 zones mapped to Uganda's 4 regions with realistic UGX delivery fees
- **User Testing:** Functional testing across customer and admin roles

---

## 7. SYSTEM ARCHITECTURE

### 7.1 Architecture Overview

SmartDine Hub uses a classic three-tier web architecture:

```
[Browser / Client]
        ↓ HTTP
[PHP Application Layer]
  - Page controllers (home.php, pages/*, admin/*)
  - API endpoints (api/*)
  - Session management
  - Business logic
        ↓ PDO
[MySQL Database]
  - users, products, orders, order_items
  - reviews, restaurants, delivery_zones
  - user_activity, user_logins
```

Product data is additionally cached in `data/products.json` for fast frontend rendering without database queries on every page load.

### 7.2 Directory Structure

```
final-year-project/
├── index.php                  # Landing page (unauthenticated)
├── home.php                   # Main hub (authenticated, region browser)
├── setup.php                  # Database setup utility
├── includes/
│   ├── connect.php            # PDO database connection
│   ├── header.php             # Global nav, BASE_PATH, Remix Icons CDN
│   ├── footer.php             # Global footer, script.js loader
│   └── email_functions.php    # Email utilities
├── pages/
│   ├── auth/                  # login, register, logout, forgot/reset password
│   ├── user/                  # cart, checkout, profile, search
│   ├── orders/                # orders list, order detail
│   ├── categories/            # appetizers, main-courses, desserts, beverages
│   ├── restaurant.php         # Restaurant detail + menu + reviews
│   ├── about.php              # About & contact
│   └── terms.php              # Terms & privacy policy
├── admin/
│   ├── index.php              # Dashboard with Chart.js analytics
│   ├── manage_orders.php      # Order status management
│   ├── manage_products.php    # Product CRUD + stock toggle + JSON sync
│   ├── manage_users.php       # User role management
│   ├── user_activity.php      # Online users, login history
│   └── order_detail.php       # Individual order view
├── api/
│   ├── add_to_cart.php        # POST: add/increment cart item
│   ├── remove_from_cart.php   # POST: remove cart item
│   ├── update_cart_quantity.php # POST: set item quantity
│   ├── get_cart_count.php     # GET: cart item count for badge
│   ├── create_order.php       # POST: checkout → create order
│   └── submit_review.php      # POST: submit restaurant review
├── assets/
│   ├── css/style.css          # Base styles
│   ├── css/elegant-theme.css  # Component library (cards, buttons, forms)
│   ├── js/script.js           # Cart logic, BASE_PATH AJAX, Remix Icons
│   └── images/food pics/      # 65 food photographs (.jpg)
└── data/
    ├── products.json          # Product catalog (30 items, source of truth for frontend)
    ├── smartdine.sql          # Base schema
    ├── missing_features.sql   # Extended schema (reviews, restaurants, delivery_zones, activity)
    └── *.sql                  # Migration scripts
```

### 7.3 Database Schema

| Table | Purpose |
|---|---|
| `users` | Authentication, roles (user/admin), profile |
| `products` | Menu items with category, restaurant, price, stock |
| `orders` | Customer orders with delivery info and status |
| `order_items` | Line items per order |
| `reviews` | Star ratings and comments per restaurant |
| `restaurants` | Restaurant metadata (description, hours, region, image) |
| `delivery_zones` | Zone names, regions, and UGX delivery fees |
| `user_activity` | Page visit tracking per user (last_seen) |
| `user_logins` | Login event log with IP and timestamp |

### 7.4 Key System Modules

**1. Authentication Module**
- Registration with username, email, password (bcrypt)
- Login with session creation and login event logging
- Forgot password with token-based reset flow
- Profile editing (username, email, phone)
- Password change with current password verification

**2. Restaurant Discovery Module**
- Home page with hero section and region-based browsing
- 4 regions: Central, Eastern, Western, Northern
- 8 restaurants with images, descriptions, and cuisine types
- Click region → restaurants appear → click restaurant → full menu

**3. Product Catalog Module**
- 30 products across 4 categories loaded from `products.json`
- Each product: name, price (UGX), image, description, in_stock flag
- Category pages: Appetizers, Main Courses, Desserts, Beverages
- Search page with filters: keyword, category, region, restaurant

**4. Cart Module (Session-Based)**
- `$_SESSION['cart']` array stores `[id, restaurant, quantity]`
- Add to cart increments quantity if item already exists
- Remove and quantity update via dedicated API endpoints
- Cart count badge updated via AJAX on every page
- BASE_PATH injected from PHP into `window.BASE_PATH` for correct AJAX URLs

**5. Checkout & Orders Module**
- Delivery zone selector with live fee calculation
- Pay-on-delivery only (no payment gateway)
- Order creation reads cart from session, writes to `orders` + `order_items`
- Order status timeline: Pending → Processing → Out for Delivery → Delivered
- Reorder: adds all items from a past order back to cart

**6. Review Module**
- 5-star rating with optional text comment per restaurant
- Average rating calculated and displayed on restaurant page
- One review per user per restaurant (upsert on duplicate)

**7. Admin Module**
- Dashboard: total orders, users, products, revenue; Chart.js bar + doughnut charts
- Order management: status updates (pending/processing/out_for_delivery/completed/cancelled)
- Product management: add with image upload, delete, toggle in_stock, sync to JSON
- User management: role promotion/demotion, deletion
- User activity: online users (last 15 min), login history, per-user stats

### 7.5 Security Architecture

- **Authentication:** PHP sessions with bcrypt password hashing
- **Authorization:** Role-based access control (`$_SESSION['role']`) on all admin pages
- **Input Sanitization:** `htmlspecialchars()` on all output, PDO prepared statements for all queries
- **CSRF Protection:** Form-based actions use POST with session validation
- **Output Buffering:** `ob_start()`/`ob_clean()` on all API endpoints to prevent JSON corruption
- **Path Traversal:** BASE_PATH calculated server-side via regex stripping of known subdirectories

---

## 8. PROJECT TIMELINE

| Phase | Duration | Key Milestones |
|---|---|---|
| Phase 1: Requirements & Design | Weeks 1–2 | Database schema, wireframes, restaurant/product data |
| Phase 2: Authentication & Catalog | Weeks 3–4 | Login/register, product JSON, category pages |
| Phase 3: Cart & Checkout | Weeks 5–6 | Session cart, AJAX APIs, pay-on-delivery checkout |
| Phase 4: Orders & Reviews | Weeks 7–8 | Order history, status timeline, reorder, review system |
| Phase 5: Admin Dashboard | Weeks 9–10 | Charts, product/order/user management, activity tracking |
| Phase 6: UI Polish & Integration | Weeks 11–12 | Remix Icons, BASE_PATH fix, responsive design, testing |

---

## 9. EXPECTED OUTCOMES AND DELIVERABLES

### 9.1 Functional System

A fully operational web-based multi-restaurant ordering platform with:
- 8 restaurants across 4 Ugandan regions
- 30+ menu items with images, descriptions, and UGX pricing
- Complete order lifecycle from browsing to delivery confirmation
- Admin tools for full platform management
- User activity monitoring for business intelligence

### 9.2 Performance Targets

- Cart operations complete in under 500ms via AJAX
- Product catalog loads from JSON without database queries on frontend pages
- Admin dashboard renders charts from aggregated SQL queries
- Session-based cart supports concurrent users without database contention
- All pages mobile-responsive via CSS Grid and Flexbox

### 9.3 Documentation Deliverables

- Project Proposal (this document)
- Database schema (`data/smartdine.sql`, `data/missing_features.sql`)
- API endpoint documentation (inline code comments)
- Setup guide (`documents/QUICK_SETUP.txt`)
- Feature documentation (`docs/FEATURES.md`)
- Payment system documentation (`docs/PAYMENT_SYSTEM.md`)
- Code documentation (`docs/CODE_DOCUMENTATION.md`)

---

## 10. RISK ANALYSIS AND MITIGATION

| Risk | Probability | Impact | Mitigation |
|---|---|---|---|
| BASE_PATH miscalculation breaking AJAX | Medium | High | Server-side regex calculation of web root; `console.log` debug output |
| MySQL key length errors (VARCHAR > 191) | Medium | Medium | Limit UNIQUE KEY columns to `VARCHAR(191)` for utf8mb4 compatibility |
| JSON/DB product sync drift | Medium | Medium | `syncProductsToJson()` called after every admin product change |
| Session cart lost on server restart | Low | Medium | Checkout validates cart before order creation; empty cart redirects gracefully |
| Image upload path issues | Low | Medium | Absolute server paths used in upload handler; fallback placeholder SVG in JS |
| Admin access without authentication | Low | Critical | Role check (`$_SESSION['role'] === 'admin'`) on every admin page and API |
| XSS via user-submitted content | Medium | High | All output wrapped in `htmlspecialchars()`; no raw HTML stored from user input |
| SQL injection | Low | Critical | All queries use PDO prepared statements with bound parameters |

---

## 11. SUSTAINABILITY AND MAINTENANCE

### 11.1 System Maintenance Plan

**Corrective Maintenance:**
- Bug fixes via the existing GitHub repository
- JSON/DB sync verification after bulk product updates
- Session configuration tuning for production environments

**Adaptive Maintenance:**
- Adding new restaurants requires: SQL INSERT into `restaurants`, products added to `products.json` and `products` table, food images added to `assets/images/food pics/`
- New delivery zones added via SQL INSERT into `delivery_zones`
- New admin features follow the existing admin page pattern

**Perfective Maintenance:**
- UI improvements via `assets/css/elegant-theme.css`
- Additional Chart.js visualizations in `admin/index.php`
- Extended search filters in `pages/user/search.php`

### 11.2 Long-term Sustainability

- **Modular architecture:** Each feature is a self-contained PHP file with clear separation of concerns
- **JSON product cache:** Reduces database load and enables fast frontend rendering
- **Documented SQL migrations:** All schema changes tracked in `data/` directory
- **Docker support:** `Dockerfile` enables consistent deployment across environments
- **Extensibility:** Pay-on-delivery can be replaced with a payment gateway (Flutterwave/MTN Mobile Money) by modifying `api/create_order.php` and `pages/user/checkout.php`

---

## 12. CONCLUSION

SmartDine Hub represents a practical, locally-relevant solution to the fragmented food ordering experience in Uganda. By aggregating 8 restaurants across 4 regions under a single web platform, the system eliminates the friction of multi-site browsing while providing restaurant administrators with the tools they need to manage their digital presence.

The platform is built on proven, accessible technologies (PHP, MySQL, vanilla JavaScript) that are maintainable by local developers without specialized infrastructure. The pay-on-delivery model removes the barrier of payment gateway integration while remaining appropriate for the Ugandan market where cash transactions remain dominant.

Key technical achievements include:
- A robust session-based cart with correct AJAX URL resolution across all page depths
- A dual-source product catalog (MySQL + JSON) that balances admin flexibility with frontend performance
- A comprehensive admin suite with real-time analytics, user activity monitoring, and product lifecycle management
- A clean, icon-based UI using Remix Icons that works consistently across all browsers

The system is ready for pilot deployment and provides a solid foundation for future enhancements including mobile money payment integration, SMS order notifications, and native mobile applications.

---

## 13. REFERENCES

1. Buhalis, D., & Law, R. (2008). Progress in information technology and tourism management: 20 years on and 10 years after the Internet. *Tourism Management, 29*(4), 609–623.

2. Cheng, S. I. (2019). Comparisons of competing models between attitudinal loyalty and behavioral loyalty. *International Journal of Business and Social Science, 10*(1), 13–24.

3. Gregoire, M. B., Shanklin, C. W., & Greathouse, K. R. (2011). Foodservice management applications of lean principles. *Journal of Foodservice Business Research, 14*(2), 92–107.

4. Law, R., Leung, R., & Buhalis, D. (2009). Information technology applications in hospitality and tourism: A review of publications from 2005 to 2007. *Journal of Travel & Tourism Marketing, 26*(5–6), 599–623.

5. Paraskevas, A., & Buhalis, D. (2002). Outsourcing IT for small hotels: The opportunities and challenges of using application service providers. *Cornell Hotel and Restaurant Administration Quarterly, 43*(2), 27–39.

6. Raab, C., Mayer, K., Shoemaker, S., & Ng, S. (2009). Activity-based pricing: Can it be applied in restaurants? *International Journal of Contemporary Hospitality Management, 21*(4), 393–410.

7. Siguaw, J. A., Enz, C. A., & Namasivayam, K. (2000). Adoption of information technology in US hotels: Strategically driven objectives. *Journal of Travel Research, 39*(2), 192–201.

---

*SmartDine Hub — Final Year Project*  
*Department of Computer Science*  
*Academic Year 2025/2026*
