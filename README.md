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

# Bona Markets – Week 2 Checklist

## Week 2 Focus: Authentication + Vendor Application

**Dates:** [Start date] to [End date]  
**Status:** ⬜ In Progress | ✅ Complete | ❌ Blocked

---

## Project Manager

| Task | Status | Notes |
|------|--------|-------|
| Run daily standups at [time] | ⬜ | |
| Create Week 2 project board | ⬜ | |
| Review and merge pull requests | ⬜ | |
| Update README with DB setup instructions | ⬜ | |

---

## Database & Backend Foundation

| Task | Status | Notes |
|------|--------|-------|
| Create `config/database.php` (PDO connection) | ⬜ | |
| Insert seed data (users, categories, products) | ⬜ | |
| Share database credentials via `.env` | ⬜ | |

---

## Authentication & Authorization

| Task | Status | Notes |
|------|--------|-------|
| Add `session_start()` to all pages | ⬜ | |
| Implement `register.php` (insert user) | ⬜ | |
| Implement `login.php` (verify + session) | ⬜ | |
| Implement `logout.php` (destroy session) | ⬜ | |
| Add role-based redirects | ⬜ | |
| Update header for session menu (coordinate with Person 10) | ⬜ | |

---

## Vendor Module

| Task | Status | Notes |
|------|--------|-------|
| Build `vendor/apply.php` form | ⬜ | |
| Insert application into `vendor_profiles` table | ⬜ | |
| Build `vendor/dashboard.php` (pending approval message) | ⬜ | |
| Build `vendor/profile.php` (edit profile) | ⬜ | |
| Add route protection (vendor only) | ⬜ | |

---

## Product Management (Prep for Week 3)

| Task | Status | Notes |
|------|--------|-------|
| Confirm placeholder files exist in `vendor/products/` | ⬜ | |
| Review `products` table schema | ⬜ | |
| Document product form fields | ⬜ | |
| Coordinate UI design with Person 10 | ⬜ | |

---

## Buyer Marketplace (Prep for Week 3)

| Task | Status | Notes |
|------|--------|-------|
| Confirm placeholder files exist in `products/` | ⬜ | |
| Plan search/filter/sort functionality | ⬜ | |
| Coordinate UI design with Person 10 | ⬜ | |

---

## Shopping Cart (Prep for Week 4)

| Task | Status | Notes |
|------|--------|-------|
| Confirm placeholder files exist in `cart/` and `orders/` | ⬜ | |
| Plan cart data structure (session vs database) | ⬜ | |
| Research Stripe test mode setup | ⬜ | |
| Document checkout flow | ⬜ | |

---

## Admin Dashboard (Prep for Week 4)

| Task | Status | Notes |
|------|--------|-------|
| Confirm placeholder files exist in `admin/` | ⬜ | |
| Plan vendor approval interface | ⬜ | |
| Define dashboard stats (vendors, products, orders, revenue) | ⬜ | |

---

## Testing & QA

| Task | Status | Notes |
|------|--------|-------|
| Create Week 2 test cases (auth + vendor) | ⬜ | |
| Execute login/register tests | ⬜ | |
| Execute vendor application tests | ⬜ | |
| Log bugs in GitHub Issues | ⬜ | |
| Submit weekly test report (Friday) | ⬜ | |

### Test Cases to Run

| ID | Description | Status |
|----|-------------|--------|
| TC-AUTH-001 | Valid registration | ⬜ |
| TC-AUTH-002 | Duplicate email registration | ⬜ |
| TC-AUTH-003 | Valid login | ⬜ |
| TC-AUTH-004 | Invalid password | ⬜ |
| TC-AUTH-005 | Non-existent email | ⬜ |
| TC-AUTH-006 | Logout destroys session | ⬜ |
| TC-AUTH-007 | Protected routes redirect to login | ⬜ |
| TC-VENDOR-001 | Submit vendor application | ⬜ |
| TC-VENDOR-002 | View pending approval message | ⬜ |
| TC-VENDOR-003 | Edit vendor profile | ⬜ |

---

## Frontend UI / Responsive Design

| Task | Status | Notes |
|------|--------|-------|
| Update `header.php` with session-aware menu structure | ⬜ | |
| Style `vendor/apply.php` (vendor application form) | ⬜ | |
| Style `vendor/dashboard.php` | ⬜ | |
| Style `vendor/profile.php` | ⬜ | |
| Add loading states + error message styling | ⬜ | |
| Mobile responsive check on all new pages | ⬜ | |
| Coordinate with Person 4 on form UI | ⬜ | |

---

## Documentation & Demo Video Support

| Task | Status | Notes |
|------|--------|-------|
| Update `docs/setup-guide.md` with DB instructions | ⬜ | |
| Create `docs/week2-checklist.md` (this file) | ⬜ | |
| Document authentication flow for team | ⬜ | |
| Collect UI screenshots for final report | ⬜ | |

---

## Week 2 Dependencies

| Task | Depends On | Owner | Status |
|------|------------|-------|--------|
| Database installation | None | Person 2 | ⬜ |
| Authentication (login/register) | Database ready | Person 3 | ⬜ |
| Vendor application (PHP) | Database ready | Person 4 | ⬜ |
| Vendor forms (UI) | Person 4 (PHP structure) | Person 10 | ⬜ |
| Header session menu | Person 3 (session logic) | Person 10 | ⬜ |
| Testing | Person 3 + Person 4 | Person 9 | ⬜ |

---

## Week 2 Success Criteria

By end of Week 2, the following must work:

- [ ] User can register an account
- [ ] User can login with correct credentials
- [ ] User cannot login with wrong password
- [ ] After login, navbar shows user email and logout button
- [ ] After logout, navbar shows login/signup buttons
- [ ] User can submit vendor application
- [ ] Vendor dashboard shows "pending approval" message
- [ ] All pages are mobile responsive

---

## Blockers Log

| Date | Blocker | Affects | Status |
|------|---------|---------|--------|
| | | | |

---

## Weekly Test Report (Due Friday)

| Metric | Value |
|--------|-------|
| Total tests executed | |
| Passed | |
| Failed | |
| Blocked | |
| Bugs logged | |
| Critical bugs | |

---

**Last Updated:** [Date]  
**Next Standup:** [Time]
php -S localhost:8000 -t public/
