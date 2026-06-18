import Link from "next/link";

export default function VendorDashboardPage() {
  return (
    <div className="container mx-auto px-4 py-8">
      <div className="mb-8"><h1 className="text-2xl font-bold text-gray-800">Vendor Dashboard</h1><p className="text-gray-500 text-sm mt-1">Manage your store.</p></div>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div className="bg-white border border-gray-200 rounded-xl p-5"><p className="text-3xl font-bold text-blue-600">24</p><p className="text-sm text-gray-500 mt-1">Total Products</p></div>
        <div className="bg-white border border-gray-200 rounded-xl p-5"><p className="text-3xl font-bold text-green-600">12</p><p className="text-sm text-gray-500 mt-1">Total Orders</p></div>
        <div className="bg-white border border-gray-200 rounded-xl p-5"><p className="text-3xl font-bold text-amber-600">R4,592</p><p className="text-sm text-gray-500 mt-1">Total Revenue</p></div>
      </div>
      <div className="flex gap-4">
        <Link href="/vendor/products/create" className="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">Add Product</Link>
        <Link href="/vendor/products" className="bg-white border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition">View Products</Link>
      </div>
    </div>
  );
}
