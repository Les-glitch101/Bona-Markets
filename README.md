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

# Bona Markets – Week 4 Plan

**Week:** 4  
**Focus:** Shopping Cart + Checkout + Admin Dashboard  
**Start Date:** [29/06/2026]  
**End Date:** [03/07/2026]

---

# Week 3 Completion Check

Before starting Week 4, confirm these were delivered:

| Person | Week 3 Deliverable | Status |
|----------|----------|----------|
| Person 5 (Amogelang) | Product CRUD (Create, Read, Update, Delete) working | ☐ |
| Person 6 (Bianca) | Public catalogue with search/filter/sort | ☐ |
| Person 10 (Lesiamo) | Product pages styled and responsive | ☐ |
| Person 9 (Dan) | Product CRUD tests executed | ☐ |

---

# Week 4 Focus

Shopping Cart + Checkout + Admin Dashboard

This week is about:

- Buyers can add products to cart, update quantities, and checkout
- Admins can approve vendors and manage the platform
- Vendors see orders for their products

---

# Person-by-Person Week 4 Tasks

## Person 1 – Lesiamo (Project Manager / Frontend UI)

| Task | Deadline |
|----------|----------|
| Run daily standups at [time] | Daily |
| Update project board with Week 4 tasks | Monday |
| Review and merge pull requests | As needed |
| Coordinate between Person 7 (Cart) and Person 8 (Admin) | Ongoing |
| Ensure cart and admin pages are mobile responsive | Friday |
| Update README with cart and admin instructions | Friday |

---

## Person 2 – Gabrielle (Database & Backend Foundation)

| Task | Deadline |
|----------|----------|
| Ensure cart table exists with correct columns | Monday |
| Ensure orders and order_items tables exist | Monday |
| Add stripe_session_id to orders table | Monday |
| Help Person 7 with database queries for cart/checkout | As needed |
| Help Person 8 with admin dashboard queries | As needed |

### SQL To Run (If Not Already Done)

```sql
-- Ensure cart table exists
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Ensure orders table has stripe_session_id
ALTER TABLE orders
ADD COLUMN IF NOT EXISTS stripe_session_id VARCHAR(255) NULL;

-- Ensure order_items table exists
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

---

## Person 3 – Timothy (Authentication & Authorization)

| Task | Deadline |
|----------|----------|
| Ensure session persists across cart/checkout pages | Monday |
| Add role checks to admin pages (admin only) | Tuesday |
| Test that non-admin users cannot access admin panel | Wednesday |
| Add guest cart handling (optional) | Thursday |
| Fix any session-related bugs | Ongoing |

### Route Protection To Add

| Page | Who Can Access |
|----------|----------|
| admin/* | Admin only |
| cart/* | Logged-in users only |
| orders/* | Logged-in users only |
| checkout/* | Logged-in users only |

---

## Person 4 – Karabelo (Vendor Module)

| Task | Deadline |
|----------|----------|
| Ensure vendor dashboard shows order stats correctly | Monday |
| Link Orders tab to vendor product orders | Tuesday |
| Test full vendor order flow | Wednesday |
| Add order status update functionality | Thursday |
| Help Person 7 with vendor notifications | Friday |

### Vendor Order Stats

- Total Orders
- Pending Orders
- Shipped Orders
- Delivered Orders

---

## Person 5 – Amogelang (Product Management)

| Task | Deadline |
|----------|----------|
| Ensure stock updates after checkout | Tuesday |
| Add Out of Stock badge | Wednesday |
| Help Person 7 with cart product data | As needed |
| Test product availability during checkout | Thursday |

---

## Person 6 – Bianca (Buyer Marketplace)

| Task | Deadline |
|----------|----------|
| Add Add-to-Cart functionality | Tuesday |
| Enable Add-to-Cart from catalogue grid | Wednesday |
| Add stock validation | Wednesday |
| Help Person 7 integrate cart UI | As needed |

---

# Person 7 – Amanda (Shopping Cart & Orders) ⭐ MAIN FOCUS

| Task | Deadline |
|----------|----------|
| Build cart/index.php | Tuesday |
| Build cart/checkout.php | Wednesday |
| Build cart/success.php | Thursday |
| Build orders/index.php | Thursday |
| Implement Stripe test payment | Thursday |
| Create order after successful payment | Thursday |
| Add order items to database | Thursday |
| Test full cart → checkout → order flow | Friday |

### Pages To Create

| Page | Purpose |
|----------|----------|
| public/cart/index.php | Cart page |
| public/cart/checkout.php | Checkout page |
| public/cart/success.php | Order confirmation |
| public/orders/index.php | Buyer order history |

### Cart Features

- Add to Cart
- Update Quantity
- Remove Item
- Calculate Totals
- Stock Validation
- Cart Persistence

### Checkout Features

- Shipping Address Form
- Stripe Test Payment
- Create Order
- Create Order Items
- Clear Cart After Checkout

### Stripe Test Card

```text
Card Number: 4242 4242 4242 4242
Expiry: Any Future Date
CVC: Any 3 Digits
```

---

# Person 8 – Molemo (Admin Dashboard) ⭐ MAIN FOCUS

| Task | Deadline |
|----------|----------|
| Build admin/dashboard.php | Tuesday |
| Build admin/vendors.php | Wednesday |
| Build admin/products.php | Wednesday |
| Build admin/orders.php | Thursday |
| Vendor approval system | Thursday |
| Test admin approval flow | Friday |

### Admin Pages To Create

| Page | Purpose |
|----------|----------|
| admin/dashboard.php | Platform overview |
| admin/vendors.php | Vendor approval |
| admin/products.php | All products |
| admin/orders.php | All orders |

### Dashboard Statistics

- Total Vendors
- Total Products
- Total Orders
- Total Revenue
- Recent Orders
- Recent Vendor Applications

### Vendor Approval Flow

```text
Vendor Applies
      ↓
Status = Pending
      ↓
Admin Reviews
      ↓
Approve / Reject
      ↓
Vendor Gains Access
```

---

## Person 9 – Dan (Testing & QA)

| Task | Deadline |
|----------|----------|
| Create Week 4 test cases | Monday |
| Execute cart tests | Tuesday |
| Execute checkout tests | Wednesday |
| Execute admin tests | Thursday |
| Log bugs in GitHub Issues | As Found |
| Submit weekly test report | Friday |

### Test Cases

| ID | Description | Priority |
|----------|----------|----------|
| TC-CART-001 | Add product to cart | High |
| TC-CART-002 | Update quantity | High |
| TC-CART-003 | Remove item | High |
| TC-CART-004 | Cart persists after login/logout | Medium |
| TC-CHECKOUT-001 | Valid Stripe checkout | High |
| TC-CHECKOUT-002 | Empty cart checkout | High |
| TC-CHECKOUT-003 | Insufficient stock | High |
| TC-CHECKOUT-004 | Order created after payment | High |
| TC-ADMIN-001 | Dashboard stats display | High |
| TC-ADMIN-002 | Approve vendor | High |
| TC-ADMIN-003 | Reject vendor | High |
| TC-ADMIN-004 | View all orders | High |

---

## Person 10 – Lesiamo (Frontend UI / Responsive Design)

| Task | Deadline |
|----------|----------|
| Style cart page | Tuesday |
| Style checkout page | Wednesday |
| Style order history page | Wednesday |
| Style admin dashboard | Thursday |
| Style vendor approval page | Thursday |
| Ensure responsive design | Friday |

### Design Guidelines

| Page | Style |
|----------|----------|
| Cart | Table + Summary Card |
| Checkout | Form + Stripe Card |
| Orders | Table Layout |
| Admin | Dashboard Cards + Tables |

---

## Person 11 – Omphile (Documentation & Demo Video Support)

| Task | Deadline |
|----------|----------|
| Create docs/week4-checklist.md | Monday |
| Document checkout flow | Wednesday |
| Document admin dashboard | Thursday |
| Update setup guide with Stripe instructions | Thursday |
| Collect screenshots | Ongoing |
| Assist PM with documentation | As Needed |

---

# Week 4 Dependencies

| Task | Depends On | Owner |
|----------|----------|----------|
| Cart Functionality | Database Ready | Person 7 |
| Checkout / Stripe | Cart Working | Person 7 |
| Order Creation | Checkout Working | Person 7 |
| Admin Dashboard | Database Ready | Person 8 |
| Vendor Approval | Apply Page Working | Person 8 |
| Cart UI Styling | Person 7 PHP Structure | Person 10 |
| Admin UI Styling | Person 8 PHP Structure | Person 10 |
| Testing | Person 7 + Person 8 | Person 9 |

---

# Week 4 Success Criteria

## Cart & Checkout

- [ ] Buyer can add products to cart
- [ ] Buyer can update quantities
- [ ] Buyer can remove products
- [ ] Cart calculates totals correctly
- [ ] Buyer can proceed to checkout
- [ ] Buyer can enter shipping address
- [ ] Stripe payment works
- [ ] Order created successfully
- [ ] Order items saved
- [ ] Cart cleared after checkout
- [ ] Buyer can view order history

## Admin Dashboard

- [ ] Admin can view platform statistics
- [ ] Admin can view vendor applications
- [ ] Admin can approve vendors
- [ ] Admin can reject vendors
- [ ] Admin can view products
- [ ] Admin can view orders
- [ ] Vendor gains access after approval

## Vendor Orders

- [ ] Vendor can view orders
- [ ] Vendor can update order status

---

# Weekly Test Report (Due Friday)

| Metric | Value |
|----------|----------|
| Total Tests Executed | |
| Passed | |
| Failed | |
| Blocked | |
| Bugs Logged | |
| Critical Bugs | |

---

# Quick Reference: File Locations

| File | Location | Owner |
|----------|----------|----------|
| Cart Page | public/cart/index.php | Person 7 |
| Checkout Page | public/cart/checkout.php | Person 7 |
| Success Page | public/cart/success.php | Person 7 |
| Order History | public/orders/index.php | Person 7 |
| Admin Dashboard | public/admin/dashboard.php | Person 8 |
| Admin Vendors | public/admin/vendors.php | Person 8 |
| Admin Products | public/admin/products.php | Person 8 |
| Admin Orders | public/admin/orders.php | Person 8 |
| Cart Styles | public/assets/css/vendor-dashboard.css | Person 10 |
| Cart JS | public/assets/js/vendor-dashboard.js | Person 10 |

---
**Last Updated:** [2026/06/15]
