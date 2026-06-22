<?php
// ============================================================
// APPLY PAGE – Become a Vendor
// ============================================================

session_start();

// If user is not logged in, redirect to login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user info
$isLoggedIn = isset($_SESSION['user_id']);
$userFullName = $isLoggedIn ? $_SESSION['fullname'] : '';
$userEmail = $isLoggedIn ? $_SESSION['email'] : '';
$userRole = $isLoggedIn ? $_SESSION['role'] : '';

// If user is already a vendor with approved profile, redirect to dashboard
if ($userRole === 'vendor') {
    // Check if vendor has an approved profile
    require_once '../config/database.php';
    $stmt = $pdo->prepare("SELECT * FROM vendor_profiles WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $vendor = $stmt->fetch();
    
    if ($vendor && $vendor['approved'] == 1) {
        header('Location: vendor/dashboard.php');
        exit;
    }
}

// Include database connection
require_once '../config/database.php';

$error = '';
$success = '';
$pendingApplication = false;

// Check if user already has a pending application
$stmt = $pdo->prepare("SELECT * FROM vendor_profiles WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$existing = $stmt->fetch();

if ($existing) {
    if ($existing['approved'] == 1) {
        // Already approved vendor
        header('Location: vendor/dashboard.php');
        exit;
    } else {
        $pendingApplication = true;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$pendingApplication) {
    $businessName = trim($_POST['business_name'] ?? '');
    $ownerName = trim($_POST['owner_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $description = trim($_POST['business_description'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $bankName = trim($_POST['bank_name'] ?? '');
    $accountNumber = trim($_POST['account_number'] ?? '');
    
    // Validate
    if (empty($businessName) || empty($ownerName) || empty($phone) || empty($email) || empty($description) || empty($city) || empty($country)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Insert application
        $stmt = $pdo->prepare("
            INSERT INTO vendor_profiles 
            (user_id, business_name, description, bank_details, approved, applied_at) 
            VALUES (?, ?, ?, ?, 0, NOW())
        ");
        
        $bankDetails = "Bank: $bankName, Account: $accountNumber";
        $stmt->execute([$_SESSION['user_id'], $businessName, $description, $bankDetails]);
        
        $success = 'Your application has been submitted successfully! We will review it within 3-5 business days.';
        
        // Update user role to vendor (pending approval)
        $pdo->prepare("UPDATE users SET role = 'vendor' WHERE id = ?")->execute([$_SESSION['user_id']]);
        
        // Refresh page to show pending status
        header('refresh:2;url=apply.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Apply to Sell | Bona Markets</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #fdf8f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        h1, h2, h3, h4 {
            font-family: 'Syne', sans-serif;
        }
        .btn-primary {
            background: #e8952a;
            color: #1a1208;
        }
        .btn-primary:hover {
            background: #f5b952;
        }
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #e8952a;
            box-shadow: 0 0 0 3px rgba(232,149,42,0.12);
        }
    </style>
</head>
<body>

    <!-- ============================================================ -->
    <!-- TOPBAR -->
    <!-- ============================================================ -->
    <header style="background:#1a1208;padding:0 2rem;height:58px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;">
        <a href="index.php" style="font-family:'Syne',sans-serif;font-size:1.35rem;font-weight:800;color:white;text-decoration:none;display:flex;align-items:center;gap:.5rem;">
            <span style="width:8px;height:8px;background:#e8952a;border-radius:50%;display:inline-block;"></span>
            Bona<span style="color:#e8952a;">Markets</span>
        </a>
        <div style="display:flex;align-items:center;gap:1rem;">
            <span style="background:#e8952a;color:#1a1208;font-size:.7rem;font-weight:700;padding:.2rem .55rem;border-radius:20px;font-family:'Syne',sans-serif;text-transform:uppercase;">Apply</span>
            <a href="logout.php" style="color:#9c8f7a;text-decoration:none;font-size:.85rem;">Logout</a>
        </div>
    </header>

    <!-- ============================================================ -->
    <!-- MAIN CONTENT -->
    <!-- ============================================================ -->
    <main style="flex:1;display:flex;align-items:center;justify-content:center;padding:2rem 1.5rem;">

        <div style="max-width:820px;width:100%;">

            <!-- Pending Application Status -->
            <?php if ($pendingApplication): ?>
                <div style="background:#fff8ed;border:1px solid #e8952a;border-radius:16px;padding:2rem;text-align:center;">
                    <div style="font-size:3rem;margin-bottom:1rem;">⏳</div>
                    <h1 style="font-size:1.5rem;font-weight:700;color:#1a1208;margin-bottom:.5rem;">Application Under Review</h1>
                    <p style="color:#7a6e5f;margin-bottom:1rem;">
                        Your vendor application is currently being reviewed by our team.
                        We'll get back to you within <strong>3-5 business days</strong>.
                    </p>
                    <p style="color:#7a6e5f;font-size:.85rem;">
                        If you have any questions, please contact us at
                        <a href="mailto:vendor@bonamarkets.com" style="color:#e8952a;text-decoration:underline;">vendor@bonamarkets.com</a>
                    </p>
                    <a href="index.php" style="display:inline-block;margin-top:1.5rem;background:#e8952a;color:#1a1208;padding:.6rem 2rem;border-radius:10px;font-weight:600;text-decoration:none;font-family:'Inter',sans-serif;">
                        Back to Home
                    </a>
                </div>
            <?php else: ?>

                <!-- Application Form -->
                <div style="background:white;border-radius:16px;box-shadow:0 2px 12px rgba(26,18,8,.08);overflow:hidden;">

                    <!-- Header -->
                    <div style="background:linear-gradient(135deg,#e8952a 0%,#c04b1e 100%);padding:2rem;text-align:center;color:white;">
                        <h1 style="font-size:1.8rem;font-weight:700;margin-bottom:.5rem;">Apply to Become a Vendor</h1>
                        <p style="opacity:.9;max-width:500px;margin:0 auto;">
                            Join Bona Markets and reach thousands of customers across Africa.
                        </p>
                    </div>

                    <!-- Form Body -->
                    <div style="padding:2rem;">

                        <?php if ($error): ?>
                            <div style="background:#fdecea;border:1px solid #f5c6c0;border-radius:10px;padding:.8rem 1rem;margin-bottom:1.5rem;">
                                <p style="color:#c04b1e;font-size:.88rem;text-align:center;"><?= htmlspecialchars($error) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div style="background:#e8f5ee;border:1px solid #3a7d5e;border-radius:10px;padding:.8rem 1rem;margin-bottom:1.5rem;">
                                <p style="color:#3a7d5e;font-size:.88rem;text-align:center;"><?= htmlspecialchars($success) ?></p>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" style="display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;">

                            <!-- Business Name -->
                            <div style="grid-column:1/-1;">
                                <label style="font-size:.82rem;font-weight:600;color:#1a1208;display:block;margin-bottom:.4rem;">
                                    Business / Store Name <span style="color:#c04b1e;">*</span>
                                </label>
                                <input type="text" name="business_name" required
                                       style="width:100%;padding:.6rem .85rem;border:1.5px solid #e5daca;border-radius:10px;font-size:.9rem;font-family:'Inter',sans-serif;"
                                       placeholder="e.g. Amara's Artisan Collective">
                            </div>

                            <!-- Owner Full Name -->
                            <div>
                                <label style="font-size:.82rem;font-weight:600;color:#1a1208;display:block;margin-bottom:.4rem;">
                                    Owner Full Name <span style="color:#c04b1e;">*</span>
                                </label>
                                <input type="text" name="owner_name" required
                                       style="width:100%;padding:.6rem .85rem;border:1.5px solid #e5daca;border-radius:10px;font-size:.9rem;font-family:'Inter',sans-serif;"
                                       placeholder="Karabelo Osei" value="<?= htmlspecialchars($userFullName) ?>">
                            </div>

                            <!-- Phone -->
                            <div>
                                <label style="font-size:.82rem;font-weight:600;color:#1a1208;display:block;margin-bottom:.4rem;">
                                    Phone Number <span style="color:#c04b1e;">*</span>
                                </label>
                                <input type="tel" name="phone" required
                                       style="width:100%;padding:.6rem .85rem;border:1.5px solid #e5daca;border-radius:10px;font-size:.9rem;font-family:'Inter',sans-serif;"
                                       placeholder="+27 82 456 7890">
                            </div>

                            <!-- Email -->
                            <div style="grid-column:1/-1;">
                                <label style="font-size:.82rem;font-weight:600;color:#1a1208;display:block;margin-bottom:.4rem;">
                                    Email Address <span style="color:#c04b1e;">*</span>
                                </label>
                                <input type="email" name="email" required
                                       style="width:100%;padding:.6rem .85rem;border:1.5px solid #e5daca;border-radius:10px;font-size:.9rem;font-family:'Inter',sans-serif;"
                                       placeholder="hello@amaracrafts.co.za" value="<?= htmlspecialchars($userEmail) ?>">
                            </div>

                            <!-- Business Description -->
                            <div style="grid-column:1/-1;">
                                <label style="font-size:.82rem;font-weight:600;color:#1a1208;display:block;margin-bottom:.4rem;">
                                    Business Description <span style="color:#c04b1e;">*</span>
                                </label>
                                <textarea name="business_description" rows="4" required
                                          style="width:100%;padding:.6rem .85rem;border:1.5px solid #e5daca;border-radius:10px;font-size:.9rem;font-family:'Inter',sans-serif;resize:vertical;min-height:90px;"
                                          placeholder="Tell us about your business, products, and story..."></textarea>
                            </div>

                            <!-- City -->
                            <div>
                                <label style="font-size:.82rem;font-weight:600;color:#1a1208;display:block;margin-bottom:.4rem;">
                                    City / Town <span style="color:#c04b1e;">*</span>
                                </label>
                                <input type="text" name="city" required
                                       style="width:100%;padding:.6rem .85rem;border:1.5px solid #e5daca;border-radius:10px;font-size:.9rem;font-family:'Inter',sans-serif;"
                                       placeholder="Johannesburg">
                            </div>

                            <!-- Country -->
                            <div>
                                <label style="font-size:.82rem;font-weight:600;color:#1a1208;display:block;margin-bottom:.4rem;">
                                    Country <span style="color:#c04b1e;">*</span>
                                </label>
                                <select name="country" required
                                        style="width:100%;padding:.6rem .85rem;border:1.5px solid #e5daca;border-radius:10px;font-size:.9rem;font-family:'Inter',sans-serif;background:white;">
                                    <option value="">Select country</option>
                                    <option value="South Africa" selected>South Africa</option>
                                    <option value="Ghana">Ghana</option>
                                    <option value="Kenya">Kenya</option>
                                    <option value="Nigeria">Nigeria</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <!-- Bank Name -->
                            <div>
                                <label style="font-size:.82rem;font-weight:600;color:#1a1208;display:block;margin-bottom:.4rem;">
                                    Bank Name
                                </label>
                                <input type="text" name="bank_name"
                                       style="width:100%;padding:.6rem .85rem;border:1.5px solid #e5daca;border-radius:10px;font-size:.9rem;font-family:'Inter',sans-serif;"
                                       placeholder="e.g. FNB, Standard Bank">
                            </div>

                            <!-- Account Number -->
                            <div>
                                <label style="font-size:.82rem;font-weight:600;color:#1a1208;display:block;margin-bottom:.4rem;">
                                    Account Number
                                </label>
                                <input type="text" name="account_number"
                                       style="width:100%;padding:.6rem .85rem;border:1.5px solid #e5daca;border-radius:10px;font-size:.9rem;font-family:'Inter',sans-serif;"
                                       placeholder="1234567890">
                            </div>

                            <!-- Submit -->
                            <div style="grid-column:1/-1;display:flex;gap:1rem;flex-wrap:wrap;margin-top:.5rem;">
                                <button type="submit"
                                        style="flex:1;background:#e8952a;color:#1a1208;padding:.7rem 1.5rem;border-radius:10px;font-size:.9rem;font-weight:600;font-family:'Inter',sans-serif;border:none;cursor:pointer;transition:all .18s;">
                                    Submit Application
                                </button>
                                <a href="index.php"
                                   style="flex:0;background:transparent;color:#1a1208;padding:.7rem 1.5rem;border-radius:10px;font-size:.9rem;font-weight:600;font-family:'Inter',sans-serif;border:1.5px solid #e5daca;text-decoration:none;cursor:pointer;text-align:center;">
                                    Cancel
                                </a>
                            </div>

                        </form>

                        <div style="text-align:center;margin-top:1.5rem;color:#7a6e5f;font-size:.82rem;">
                            Applications are reviewed within 3–5 business days.<br>
                            Approved vendors get immediate access to the full Vendor Portal.
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- ============================================================ -->
    <!-- FOOTER -->
    <!-- ============================================================ -->
    <footer style="background:#1a1208;color:#9c8f7a;text-align:center;padding:1.5rem;font-size:.82rem;border-top:1px solid #2a1e10;">
        <p>&copy; 2025 Bona Markets. All rights reserved.</p>
    </footer>

</body>
</html>
