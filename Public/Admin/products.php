<?php
$pageTitle = 'All Products – Bona Markets Admin';
require_once '../../includes/header.php';

// fake data
$products = [
    [
        'id'       => 1,
        'name'     => 'Running Shoes',
        'vendor'   => 'Joburg Tech Store',
        'category' => 'Footwear',
        'price'    => 699.00,
        'stock'    => 14,
        'status'   => 'ACTIVE',
    ],
    [
        'id'       => 2,
        'name'     => 'Handmade Beads Necklace',
        'vendor'   => 'Zulu Crafts',
        'category' => 'Jewellery',
        'price'    => 320.00,
        'stock'    => 6,
        'status'   => 'ACTIVE',
    ],
    [
        'id'       => 3,
        'name'     => 'Cape Spice Pack',
        'vendor'   => 'Cape Spice Co.',
        'category' => 'Food',
        'price'    => 89.99,
        'stock'    => 0,
        'status'   => 'OUT OF STOCK',
    ],
    [
        'id'       => 4,
        'name'     => 'Wireless Earbuds',
        'vendor'   => 'Joburg Tech Store',
        'category' => 'Electronics',
        'price'    => 1199.00,
        'stock'    => 3,
        'status'   => 'ACTIVE',
    ],
    [
        'id'       => 5,
        'name'     => 'Urban Hoodie',
        'vendor'   => 'Soweto Threads',
        'category' => 'Clothing',
        'price'    => 450.00,
        'stock'    => 9,
        'status'   => 'ACTIVE',
    ],
];

function productStatusBadge(string $status): string {
    return match($status) {
        'ACTIVE'       => 'bg-green-100 text-green-700',
        'OUT OF STOCK' => 'bg-red-100 text-red-700',
        default        => 'bg-gray-100 text-gray-600',
    };
}
?>

<div class="container mx-auto px-4 py-8">

    // Page heading   
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">All Products</h1>
        <p class="text-gray-500 text-sm mt-1"><?= count($products) ?> products across all vendors</p>
    </div>

    <?php if (empty($products)): ?>
        <div class="bg-white border border-gray-200 rounded-xl p-12 text-center text-gray-400">
            No products listed yet.
        </div>
    <?php else: ?>
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3 font-medium text-gray-600">Product</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600">Vendor</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600">Category</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600">Price</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600">Stock</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($products as $product): ?>
                    <tr class="hover:bg-gray-50">

                        //Product name
                        <td class="px-5 py-3 font-medium text-gray-800">
                            <?= htmlspecialchars($product['name']) ?>
                        </td>

                        //Vendor
                        <td class="px-5 py-3 text-gray-500">
                            <?= htmlspecialchars($product['vendor']) ?>
                        </td>

                        // Category
                        <td class="px-5 py-3 text-gray-500">
                            <?= htmlspecialchars($product['category']) ?>
                        </td>

                        // Price
                        <td class="px-5 py-3 font-semibold text-gray-800">
                            R<?= number_format($product['price'], 2) ?>
                        </td>

                        // Stock count — red if zero
                        <td class="px-5 py-3 <?= $product['stock'] === 0 ? 'text-red-500 font-medium' : 'text-gray-500' ?>">
                            <?= $product['stock'] ?>
                        </td>

                        // Status badge
                        <td class="px-5 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium <?= productStatusBadge($product['status']) ?>">
                                <?= $product['status'] ?>
                            </span>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<?php require_once '../../includes/footer.php'; ?>
