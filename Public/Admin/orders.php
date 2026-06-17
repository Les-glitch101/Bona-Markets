<?php
$pageTitle = 'All Orders – Bona Markets Admin';
require_once '../../includes/header.php';

//fake data 
$orders = [
    [
        'id'           => 1001,
        'buyer_name'   => 'Thabo Mokoena',
        'buyer_email'  => 'thabo@example.com',
        'items'        => 'Running Shoes, Water Bottle',
        'item_count'   => 2,
        'total'        => 849.00,
        'status'       => 'DELIVERED',
        'date'         => '2025-06-14',
    ],
    [
        'id'           => 1002,
        'buyer_name'   => 'Lerato Dlamini',
        'buyer_email'  => 'lerato@example.com',
        'items'        => 'Handmade Beads Necklace',
        'item_count'   => 1,
        'total'        => 320.00,
        'status'       => 'PROCESSING',
        'date'         => '2025-06-15',
    ],
    [
        'id'           => 1003,
        'buyer_name'   => 'Sipho Ndlovu',
        'buyer_email'  => 'sipho@example.com',
        'items'        => 'Cape Spice Pack, Chutney Set',
        'item_count'   => 2,
        'total'        => 215.50,
        'status'       => 'PENDING',
        'date'         => '2025-06-16',
    ],
    [
        'id'           => 1004,
        'buyer_name'   => 'Ayanda Khumalo',
        'buyer_email'  => 'ayanda@example.com',
        'items'        => 'Wireless Earbuds',
        'item_count'   => 1,
        'total'        => 1199.00,
        'status'       => 'SHIPPED',
        'date'         => '2025-06-13',
    ],
];

// Badge colour per status
function orderStatusBadge(string $status): string {
    return match($status) {
        'DELIVERED'  => 'bg-green-100 text-green-700',
        'SHIPPED'    => 'bg-blue-100 text-blue-700',
        'PROCESSING' => 'bg-indigo-100 text-indigo-700',
        'CANCELLED'  => 'bg-red-100 text-red-700',
        default      => 'bg-amber-100 text-amber-700', // PENDING
    };
}
?>

<div class="container mx-auto px-4 py-8">

    //Page heading
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">All Orders</h1>
        <p class="text-gray-500 text-sm mt-1"><?= count($orders) ?> orders across the platform</p>
    </div>

    <?php if (empty($orders)): ?>
        <div class="bg-white border border-gray-200 rounded-xl p-12 text-center text-gray-400">
            No orders yet.
        </div>
    <?php else: ?>
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3 font-medium text-gray-600">Order ID</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600">Buyer</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600">Items</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600">Total</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600">Status</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($orders as $order): ?>
                    <tr class="hover:bg-gray-50">

                        //Order ID 
                        <td class="px-5 py-3 font-mono text-gray-400 text-xs">
                            #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>
                        </td>

                        //Buyer
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800"><?= htmlspecialchars($order['buyer_name']) ?></p>
                            <p class="text-gray-400 text-xs"><?= htmlspecialchars($order['buyer_email']) ?></p>
                        </td>

                        // Items
                        <td class="px-5 py-3 text-gray-500">
                            <?= $order['item_count'] ?> item<?= $order['item_count'] !== 1 ? 's' : '' ?>
                            <p class="text-xs text-gray-400 truncate max-w-[160px]">
                                <?= htmlspecialchars($order['items']) ?>
                            </p>
                        </td>

                        //Total 
                        <td class="px-5 py-3 font-semibold text-gray-800">
                            R<?= number_format($order['total'], 2) ?>
                        </td>

                       //Status badge
                        <td class="px-5 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium <?= orderStatusBadge($order['status']) ?>">
                                <?= ucfirst(strtolower($order['status'])) ?>
                            </span>
                        </td>

                        //Date 
                        <td class="px-5 py-3 text-gray-500">
                            <?= date('d M Y', strtotime($order['date'])) ?>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<?php require_once '../../includes/footer.php'; ?>
