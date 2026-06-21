# Bona Markets – Multi-Vendor Marketplace

## Team

| Member    | Role                          |
| --------- | ----------------------------- |
| Lesiamo   | Project Manager / Frontend UI |
| Gabrielle | Database                      |
| Timothy   | Authentication                |
| Karabelo  | Vendor Module                 |
| Amogelang | Product Management            |
| Bianca    | Buyer Marketplace             |
| Amanda    | Shopping Cart                 |
| Molemo    | Admin Dashboard               |
| Dan       | Testing                       |
| Omphile   | Documentation                 |

---

# Setup

1. Clone the repository.
2. Open in browser:

```text
/public/index.php
```

---

# Bona Markets – Week 3 Checklist

**Week 3 Focus:** Product Management + Buyer Marketplace

**Dates:** [Start Date] to [End Date]

**Status:** ⬜ In Progress | ✅ Complete | ❌ Blocked

---

# Project Manager / Frontend UI

| Task                                               | Status | Notes |
| -------------------------------------------------- | ------ | ----- |
| Run daily standups at [time]                       | ⬜      |       |
| Update project board with Week 3 tasks             | ⬜      |       |
| Review and merge pull requests                     | ⬜      |       |
| Coordinate between Product and Catalogue teams     | ⬜      |       |
| Ensure all product pages are mobile responsive     | ⬜      |       |
| Update README with product management instructions | ⬜      |       |

---

# Database & Backend Foundation

| Task                                          | Status | Notes |
| --------------------------------------------- | ------ | ----- |
| Ensure products table has image_url column    | ⬜      |       |
| Create categories table if not exists         | ⬜      |       |
| Seed categories (Electronics, Clothing, etc.) | ⬜      |       |
| Test product queries with sample data         | ⬜      |       |
| Help Product team with database queries       | ⬜      |       |

---

# Authentication & Authorization

| Task                                                    | Status | Notes |
| ------------------------------------------------------- | ------ | ----- |
| Add role checks to product pages (vendor only for CRUD) | ⬜      |       |
| Test that non-vendors cannot access product creation    | ⬜      |       |
| Ensure session persists across all pages                | ⬜      |       |
| Fix any session-related bugs                            | ⬜      |       |

## Route Protection

| Page                       | Who Can Access          | Status |
| -------------------------- | ----------------------- | ------ |
| vendor/products/create.php | Vendor only             | ⬜      |
| vendor/products/edit.php   | Vendor only (their own) | ⬜      |
| vendor/products/delete.php | Vendor only (their own) | ⬜      |
| products/index.php         | Anyone (public)         | ⬜      |
| products/show.php          | Anyone (public)         | ⬜      |

---

# Vendor Module

| Task                                                        | Status | Notes |
| ----------------------------------------------------------- | ------ | ----- |
| Ensure vendor dashboard shows real product stats            | ⬜      |       |
| Link Add Product button to vendor/products/create.php       | ⬜      |       |
| Fix vendor dashboard product count                          | ⬜      |       |
| Test vendor flow: register → apply → approve → add products | ⬜      |       |
| Help Product team with vendor-side product listing          | ⬜      |       |

---

# Product Management ⭐ MAIN FOCUS

| Task                                                             | Status | Notes |
| ---------------------------------------------------------------- | ------ | ----- |
| Build vendor/products/create.php (Add Product with image upload) | ⬜      |       |
| Build vendor/products/index.php (List vendor products)           | ⬜      |       |
| Build vendor/products/edit.php (Edit product)                    | ⬜      |       |
| Build vendor/products/delete.php (Delete product)                | ⬜      |       |
| Add validation for product form (name, price, stock)             | ⬜      |       |
| Integrate image upload with server storage                       | ⬜      |       |
| Test CRUD operations                                             | ⬜      |       |

## Product CRUD Pages

| Page                       | Purpose                            | Status |
| -------------------------- | ---------------------------------- | ------ |
| vendor/products/create.php | Add new product with image upload  | ⬜      |
| vendor/products/index.php  | List all vendor products           | ⬜      |
| vendor/products/edit.php   | Edit existing product              | ⬜      |
| vendor/products/delete.php | Delete product (with confirmation) | ⬜      |

## Product Fields Checklist

| Field       | Type                             | Required | Status |
| ----------- | -------------------------------- | -------- | ------ |
| Name        | Text                             | Yes      | ⬜      |
| Description | Textarea                         | No       | ⬜      |
| Category    | Dropdown                         | No       | ⬜      |
| Price       | Number                           | Yes      | ⬜      |
| Stock       | Number                           | Yes      | ⬜      |
| Image       | File Upload                      | No       | ⬜      |
| Status      | Dropdown (active/draft/archived) | Yes      | ⬜      |

---

# Buyer Marketplace ⭐ MAIN FOCUS

| Task                                                          | Status | Notes |
| ------------------------------------------------------------- | ------ | ----- |
| Build products/index.php (Public catalogue with product grid) | ⬜      |       |
| Build products/show.php (Product details page)                | ⬜      |       |
| Add search functionality (by product name)                    | ⬜      |       |
| Add category filter (dropdown)                                | ⬜      |       |
| Add sort options (price, newest)                              | ⬜      |       |
| Make product grid responsive (1→2→3→4 columns)                | ⬜      |       |
| Test catalogue with sample products                           | ⬜      |       |

## Catalogue Features

| Feature                                   | Status | Notes |
| ----------------------------------------- | ------ | ----- |
| Product grid (with images, names, prices) | ⬜      |       |
| Search bar (search by name)               | ⬜      |       |
| Category filter                           | ⬜      |       |
| Sort by price (low-high, high-low)        | ⬜      |       |
| Sort by newest                            | ⬜      |       |
| Product details page                      | ⬜      |       |

---

# Shopping Cart (Prep for Week 4)

| Task                                                    | Status | Notes |
| ------------------------------------------------------- | ------ | ----- |
| Confirm cart placeholders exist                         | ⬜      |       |
| Research Stripe test mode setup                         | ⬜      |       |
| Document cart data structure (session vs database)      | ⬜      |       |
| Start building cart UI (static HTML)                    | ⬜      |       |
| Coordinate with Buyer Marketplace on Add to Cart button | ⬜      |       |

---

# Admin Dashboard (Prep for Week 4)

| Task                                                        | Status | Notes |
| ----------------------------------------------------------- | ------ | ----- |
| Confirm admin placeholders exist                            | ⬜      |       |
| Plan vendor approval interface                              | ⬜      |       |
| Define dashboard stats (vendors, products, orders, revenue) | ⬜      |       |
| Start building admin UI                                     | ⬜      |       |

## Admin Pages to Build (Week 4)

| Page                | Purpose                | Status |
| ------------------- | ---------------------- | ------ |
| admin/dashboard.php | Platform statistics    | ⬜      |
| admin/vendors.php   | Approve/reject vendors | ⬜      |
| admin/products.php  | View all products      | ⬜      |
| admin/orders.php    | View all orders        | ⬜      |

---

# Testing & QA

| Task                                            | Status | Notes |
| ----------------------------------------------- | ------ | ----- |
| Create Week 3 test cases (products + catalogue) | ⬜      |       |
| Execute product CRUD tests                      | ⬜      |       |
| Execute catalogue tests (search, filter, sort)  | ⬜      |       |
| Execute responsive design tests                 | ⬜      |       |
| Log bugs in GitHub Issues                       | ⬜      |       |
| Submit weekly test report (Friday)              | ⬜      |       |

## Test Cases to Run

| ID          | Description                          | Priority | Status |
| ----------- | ------------------------------------ | -------- | ------ |
| TC-PROD-001 | Add product with valid data          | High     | ⬜      |
| TC-PROD-002 | Add product without name             | High     | ⬜      |
| TC-PROD-003 | Add product with invalid price       | High     | ⬜      |
| TC-PROD-004 | Add product with image upload        | High     | ⬜      |
| TC-PROD-005 | Edit product                         | High     | ⬜      |
| TC-PROD-006 | Delete product                       | High     | ⬜      |
| TC-PROD-007 | Vendor sees only their products      | High     | ⬜      |
| TC-CAT-001  | Product catalogue displays correctly | High     | ⬜      |
| TC-CAT-002  | Search by product name               | Medium   | ⬜      |
| TC-CAT-003  | Filter by category                   | Medium   | ⬜      |
| TC-CAT-004  | Sort by price                        | Medium   | ⬜      |
| TC-CAT-005  | Product details page                 | High     | ⬜      |
| TC-CAT-006  | Mobile responsiveness                | Medium   | ⬜      |

---

# Documentation & Demo Video Support

| Task                                               | Status | Notes |
| -------------------------------------------------- | ------ | ----- |
| Create docs/week3-tasks.md (this file)             | ⬜      |       |
| Document product management flow                   | ⬜      |       |
| Document buyer catalogue flow                      | ⬜      |       |
| Update setup guide with product image instructions | ⬜      |       |
| Collect UI screenshots for final report            | ⬜      |       |

---

# Week 3 Dependencies

| Task               | Depends On                | Owner     | Status |
| ------------------ | ------------------------- | --------- | ------ |
| Product CRUD       | Database ready (Person 2) | Person 5  | ⬜      |
| Product Images     | Upload folder created     | Person 5  | ⬜      |
| Catalogue Page     | Products in database      | Person 6  | ⬜      |
| Product UI Styling | Person 5 + Person 6       | Person 10 | ⬜      |
| Testing            | Person 5 + Person 6       | Person 9  | ⬜      |

---

# Week 3 Success Criteria

By the end of Week 3:

* [ ] Vendor can add a product with image
* [ ] Vendor can edit a product
* [ ] Vendor can delete a product
* [ ] Vendor sees only their own products
* [ ] Buyer can see all products on homepage
* [ ] Buyer can search for products
* [ ] Buyer can filter by category
* [ ] Buyer can sort by price
* [ ] Buyer can view product details
* [ ] All product pages are mobile responsive

---

# Quick Reference: File Locations

| File                 | Location                               | Owner     |
| -------------------- | -------------------------------------- | --------- |
| Add Product          | public/vendor/products/create.php      | Person 5  |
| Edit Product         | public/vendor/products/edit.php        | Person 5  |
| Delete Product       | public/vendor/products/delete.php      | Person 5  |
| Vendor Products List | public/vendor/products/index.php       | Person 5  |
| Public Catalogue     | public/products/index.php              | Person 6  |
| Product Details      | public/products/show.php               | Person 6  |
| Product Styles       | public/assets/css/vendor-dashboard.css | Person 10 |
| Uploads Folder       | public/uploads/products/               | Person 5  |

---

# Blockers Log

| Date | Blocker | Affects | Status |
| ---- | ------- | ------- | ------ |
|      |         |         |        |

---

# Weekly Test Report (Due Friday)

| Metric               | Value |
| -------------------- | ----- |
| Total tests executed |       |
| Passed               |       |
| Failed               |       |
| Blocked              |       |
| Bugs logged          |       |
| Critical bugs        |       |

---

**Last Updated:** [Date]

**Next Standup:** [Time]
