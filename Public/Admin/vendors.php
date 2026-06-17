<?php
$pageTitle = 'Vendor Applications – Bona Markets Admin';
require_once '../../includes/header.php';

//fake data
$vendors = [
    [
        'id'            => 1,
        'business_name' => 'Zulu Crafts',
        'email'         => 'zulu@example.com',
        'description'   => 'Handmade traditional crafts from KZN.',
        'status'        => 'PENDING',
        'applied_date'  => '2025-06-10',
    ],
    [
        'id'            => 2,
        'business_name' => 'Cape Spice Co.',
        'email'         => 'cape@example.com',
        'description'   => 'Authentic Cape Malay spice blends.',
        'status'        => 'PENDING',
        'applied_date'  => '2025-06-12',
    ],
    [
        'id'            => 3,
        'business_name' => 'Joburg Tech Store',
        'email'         => 'jts@example.com',
        'description'   => 'Electronics and accessories.',
        'status'        => 'APPROVED',
        'applied_date'  => '2025-06-01',
    ],
    [
        'id'            => 4,
        'business_name' => 'Soweto Threads',
        'email'         => 'soweto@example.com',
        'description'   => 'Urban fashion and streetwear.',
        'status'        => 'REJECTED',
        'applied_date'  => '2025-06-05',
    ],
];

// Split into groups
$pending  = array_filter($vendors, fn($v) => $v['status'] === 'PENDING');
$reviewed = array_filter($vendors, fn($v) => $v['status'] !== 'PENDING');

// Helper: badge colour based on status
function statusBadge(string $status): string {
    return match($status) {
        'APPROVED' => 'bg-green-100 text-green-700',
        'REJECTED' => 'bg-red-100 text-red-700',
        default    => 'bg-amber-100 text-amber-700',
    };
}
?>

<div class="container mx-auto px-4 py-8">

    // Page heading
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Vendor Applications</h1>
        <p class="text-gray-500 text-sm mt-1">
            <?= count($pending) ?> pending &middot; <?= count($reviewed) ?> reviewed
        </p>
    </div>

    // ── PENDING SECTION ───────────────────────────────────────────────── 
    <section class="mb-10">
        <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-4">
            Pending Review (<?= count($pending) ?>)
        </h2>

        <?php if (empty($pending)): ?>
            <div class="bg-white border border-gray-200 rounded-xl p-10 text-center text-gray-400 text-sm">
                No pending applications. All caught up!
            </div>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($pending as $vendor): ?>
                <div class="bg-white border border-amber-200 rounded-xl p-5 flex flex-col md:flex-row md:items-center gap-4">

                    // Avatar / initials
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-blue-600 font-bold text-lg">
                            <?= strtoupper(substr($vendor['business_name'], 0, 1)) ?>
                        </span>
                    </div>

                    // Info
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($vendor['business_name']) ?></p>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($vendor['email']) ?></p>
                        <p class="text-sm text-gray-400 mt-1"><?= htmlspecialchars($vendor['description']) ?></p>
                        <p class="text-xs text-gray-400 mt-1">Applied: <?= $vendor['applied_date'] ?></p>
                    </div>

                    // Action buttons
                    <div class="flex gap-2 flex-shrink-0">
                        <form method="POST" action="/admin/vendors.php">
                            <input type="hidden" name="vendor_id" value="<?= $vendor['id'] ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                Approve
                            </button>
                        </form>
                        <form method="POST" action="/admin/vendors.php">
                            <input type="hidden" name="vendor_id" value="<?= $vendor['id'] ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit"
                                class="px-4 py-2 border border-red-300 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition">
                                Reject
                            </button>
                        </form>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    // ── REVIEWED SECTION ────────────────────────────────────────────────
    <section>
        <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-4">
            Reviewed (<?= count($reviewed) ?>)
        </h2>

        <?php if (empty($reviewed)): ?>
            <div class="bg-white border border-gray-200 rounded-xl p-10 text-center text-gray-400 text-sm">
                No reviewed applications yet.
            </div>
        <?php else: ?>
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-5 py-3 font-medium text-gray-600">Business</th>
                            <th class="text-left px-5 py-3 font-medium text-gray-600">Email</th>
                            <th class="text-left px-5 py-3 font-medium text-gray-600">Applied</th>
                            <th class="text-left px-5 py-3 font-medium text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($reviewed as $vendor): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-800">
                                <?= htmlspecialchars($vendor['business_name']) ?>
                            </td>
                            <td class="px-5 py-3 text-gray-500"><?= htmlspecialchars($vendor['email']) ?></td>
                            <td class="px-5 py-3 text-gray-500"><?= $vendor['applied_date'] ?></td>
                            <td class="px-5 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium <?= statusBadge($vendor['status']) ?>">
                                    <?= ucfirst(strtolower($vendor['status'])) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

</div>

<?php require_once '../../includes/footer.php'; ?>
