const products = [
  { id: 1, name: "Running Shoes", vendor: "Joburg Tech Store", category: "Footwear", price: 699.0, stock: 14, status: "ACTIVE" },
  { id: 2, name: "Handmade Beads Necklace", vendor: "Zulu Crafts", category: "Jewellery", price: 320.0, stock: 6, status: "ACTIVE" },
  { id: 3, name: "Cape Spice Pack", vendor: "Cape Spice Co.", category: "Food", price: 89.99, stock: 0, status: "OUT OF STOCK" },
  { id: 4, name: "Wireless Earbuds", vendor: "Joburg Tech Store", category: "Electronics", price: 1199.0, stock: 3, status: "ACTIVE" },
  { id: 5, name: "Urban Hoodie", vendor: "Soweto Threads", category: "Clothing", price: 450.0, stock: 9, status: "ACTIVE" },
];

export default function AdminProductsPage() {
  return (
    <div className="container mx-auto px-4 py-8">
      <div className="mb-8"><h1 className="text-2xl font-bold text-gray-800">All Products</h1><p className="text-gray-500 text-sm mt-1">{products.length} products</p></div>
      <div className="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table className="w-full text-sm">
          <thead><tr className="bg-gray-50 border-b border-gray-100"><th className="text-left px-5 py-3 font-medium text-gray-600">Product</th><th className="text-left px-5 py-3 font-medium text-gray-600">Vendor</th><th className="text-left px-5 py-3 font-medium text-gray-600">Category</th><th className="text-left px-5 py-3 font-medium text-gray-600">Price</th><th className="text-left px-5 py-3 font-medium text-gray-600">Stock</th><th className="text-left px-5 py-3 font-medium text-gray-600">Status</th></tr></thead>
          <tbody className="divide-y divide-gray-100">{products.map((p) => (
            <tr key={p.id} className="hover:bg-gray-50">
              <td className="px-5 py-3 font-medium text-gray-800">{p.name}</td>
              <td className="px-5 py-3 text-gray-500">{p.vendor}</td>
              <td className="px-5 py-3 text-gray-500">{p.category}</td>
              <td className="px-5 py-3 font-semibold text-gray-800">R{p.price.toFixed(2)}</td>
              <td className={`px-5 py-3 ${p.stock === 0 ? "text-red-500 font-medium" : "text-gray-500"}`}>{p.stock}</td>
              <td className="px-5 py-3"><span className={`text-xs px-2 py-0.5 rounded-full font-medium ${p.status === "ACTIVE" ? "bg-green-100 text-green-700" : "bg-red-100 text-red-700"}`}>{p.status}</span></td>
            </tr>
          ))}</tbody>
        </table>
      </div>
    </div>
  );
}
