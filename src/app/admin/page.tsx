import Link from "next/link";

export default function AdminDashboardPage() {
  const stats = { pendingVendors: 3, totalVendors: 12, totalProducts: 48, totalOrders: 27 };
  return (
    <div className="container mx-auto px-4 py-8">
      <div className="mb-8"><h1 className="text-2xl font-bold text-gray-800">Admin Dashboard</h1><p className="text-gray-500 text-sm mt-1">Welcome back.</p></div>
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        <Link href="/admin/vendors" className="block bg-amber-50 border border-amber-300 rounded-xl p-5 hover:shadow-md transition">
          <p className="text-3xl font-bold text-amber-600">{stats.pendingVendors}</p>
          <p className="text-sm text-amber-700 mt-1 font-medium">Pending Applications</p>
          <span className="inline-block mt-2 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Needs attention</span>
        </Link>
        <Link href="/admin/vendors" className="block bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition">
          <p className="text-3xl font-bold text-blue-600">{stats.totalVendors}</p>
          <p className="text-sm text-gray-500 mt-1">Total Vendors</p>
        </Link>
        <Link href="/admin/products" className="block bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition">
          <p className="text-3xl font-bold text-blue-600">{stats.totalProducts}</p>
          <p className="text-sm text-gray-500 mt-1">Total Products</p>
        </Link>
        <Link href="/admin/orders" className="block bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition">
          <p className="text-3xl font-bold text-blue-600">{stats.totalOrders}</p>
          <p className="text-sm text-gray-500 mt-1">Total Orders</p>
        </Link>
      </div>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Link href="/admin/vendors" className="block bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition">
          <h2 className="font-semibold text-gray-800 mb-1">Vendor Applications</h2>
          <p className="text-sm text-gray-500">Review, approve or reject vendors.</p>
          <span className="inline-block mt-3 text-sm text-blue-600 font-medium">Go to vendors &rarr;</span>
        </Link>
        <Link href="/admin/orders" className="block bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition">
          <h2 className="font-semibold text-gray-800 mb-1">Order Management</h2>
          <p className="text-sm text-gray-500">View all orders across the platform.</p>
          <span className="inline-block mt-3 text-sm text-blue-600 font-medium">Go to orders &rarr;</span>
        </Link>
        <Link href="/admin/products" className="block bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition">
          <h2 className="font-semibold text-gray-800 mb-1">All Products</h2>
          <p className="text-sm text-gray-500">Browse every product listed on the platform.</p>
          <span className="inline-block mt-3 text-sm text-blue-600 font-medium">Go to products &rarr;</span>
        </Link>
      </div>
    </div>
  );
}
