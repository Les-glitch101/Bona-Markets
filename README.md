# Bona Markets – Multi-Vendor Marketplace

## Team
- Lesiamo   : Project Manager
- Gabrielle : Database
- Timothy   : Authentication
- Karabelo  : Vendor Module
- Amogelang : Product Management
- Bianca    : Buyer Marketplace
- Amanda    : Shopping Cart
- Molemo    : Admin Dashboard
- Lesiamo   : Testing
- Zithobile : Frontend UI
- Omphile   : Documentation

## Setup
1. Clone the repo
2. Open in browser: `/public/index.php`

## Status
Week 1 – Foundation complete

# Authentication Plan

## Pages (Week 2)
- /login.php
- /register.php
- /logout.php

## Session Variables
- $_SESSION['user_id']
- $_SESSION['role']
- $_SESSION['email']

## Role-Based Access
- Admin → /admin/*
- Vendor → /vendor/*
- Buyer → default

## Functions Needed (Week 2)
- requireLogin()
- requireVendor()
- requireAdmin()

# Vendor Module Plan

## Pages (Week 2)
- /vendor/apply.php
- /vendor/profile.php
- /vendor/dashboard.php

## Application Fields
- Business name
- Logo upload
- Description
- Bank details

## Approval Flow
1. Submit → approved = FALSE
2. Admin approves → approved = TRUE
3. Vendor can add products

# Product Management Plan

## Pages (Week 3)
- /vendor/products/index.php
- /vendor/products/create.php
- /vendor/products/edit.php
- /vendor/products/delete.php

## Fields
- Product name, description, price
- Category dropdown
- Image upload (Cloudinary)
- Stock quantity

# Buyer Marketplace Plan

## Pages (Week 3)
- /index.php (homepage)
- /products/index.php (catalogue)
- /products/show.php (details)

## Features
- Search, category filter, sort
- Responsive grid (1→2→3→4 columns)

# Shopping Cart Plan

## Pages (Week 4)
- /cart/index.php
- /cart/checkout.php
- /cart/success.php
- /orders/index.php

## Features
- Add/remove/update quantity
- Stripe test payment
- Order creation

# Admin Dashboard Plan

## Pages (Week 4)
- /admin/dashboard.php
- /admin/vendors.php
- /admin/products.php
- /admin/orders.php

## Permissions
- Approve vendors
- View/delete products
- View all orders

# Test Plan – Bona Markets

## Test Environments
- Browsers: Chrome, Firefox, Safari
- Devices: Desktop, tablet, mobile

## Test Phases
| Week | Focus |
|------|-------|
| 2 | Auth + Vendor |
| 3 | Products + Catalogue |
| 4 | Cart + Admin |
| 5 | Full regression |

## Templates
- Test case: ID, Steps, Expected, Actual, Status
- Bug report: ID, Description, Severity, Steps, Status

# Setup Guide – Week 1

## View the Webpage
1. Download ZIP from GitHub
2. Extract folder
3. Open `public/index.php` in browser

## Run with PHP Server (Optional)
```bash
php -S localhost:8000 -t public/
