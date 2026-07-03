# Test Case Template

## Template

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-[MODULE]-[NUMBER] |
| **Feature** | [Feature name] |
| **Test Title** | [Brief description] |
| **Priority** | High / Medium / Low |
| **Preconditions** | [What must be true before test] |
| **Test Data** | [Input values needed] |
| **Test Steps** | 1. Step one<br>2. Step two<br>3. Step three |
| **Expected Result** | [What should happen] |
| **Actual Result** | [To be filled during test] |
| **Status** | Pass / Fail / Blocked |
| **Tester** | Person 9 |
| **Date** | YYYY-MM-DD |

## Example

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-AUTH-001 |
| **Feature** | Login |
| **Test Title** | Valid login with correct credentials |
| **Priority** | High |
| **Preconditions** | User account exists in database |
| **Test Data** | Email: test@test.com, Password: password123 |
| **Test Steps** | 1. Navigate to /login.php<br>2. Enter email<br>3. Enter password<br>4. Click Sign In |
| **Expected Result** | User redirected to homepage, navbar shows user email |
| **Actual Result** | |
| **Status** | |
| **Tester** | Person 9 |
| **Date** | |

## Test Case ID Naming Convention

| Prefix | Module |
|--------|--------|
| TC-AUTH | Authentication (login, register, logout) |
| TC-VENDOR | Vendor application, profile |
| TC-PROD | Product management (CRUD) |
| TC-CAT | Catalogue (search, filter, sort) |
| TC-CART | Shopping cart |
| TC-CHECKOUT | Checkout + Stripe |
| TC-ADMIN | Admin dashboard |
| TC-UI | UI/Responsive |
