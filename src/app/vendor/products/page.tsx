"use client";

import { useState } from "react";
import Link from "next/link";

export default function VendorProductsPage() {
  const [products] = useState([
    { id: 1, name: "Air Force 1 - 2026", price: 1333, stock: 10 },
    { id: 2, name: "Black JLO Hoodie", price: 1234, stock: 5 },
  ]);

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold text-gray-800">My Products</h1>
        <Link href="/vendor/products/create" className="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">Add Product</Link>
      </div>
      <div className="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table className="w-full text-sm">
          <thead><tr className="bg-gray-50 border-b border-gray-100"><th className="text-left px-5 py-3 font-medium text-gray-600">Product</th><th className="text-left px-5 py-3 font-medium text-gray-600">Price</th><th className="text-left px-5 py-3 font-medium text-gray-600">Stock</th><th className="text-left px-5 py-3 font-medium text-gray-600">Actions</th></tr></thead>
          <tbody className="divide-y divide-gray-100">{products.map((p) => (
            <tr key={p.id} className="hover:bg-gray-50">
              <td className="px-5 py-3 font-medium text-gray-800">{p.name}</td>
              <td className="px-5 py-3 text-gray-500">R{p.price.toFixed(2)}</td>
              <td className="px-5 py-3 text-gray-500">{p.stock}</td>
              <td className="px-5 py-3 flex gap-2">
                <Link href="/vendor/products/edit" className="text-blue-600 hover:underline text-sm">Edit</Link>
                <Link href="/vendor/products/delete" className="text-red-600 hover:underline text-sm">Delete</Link>
              </td>
            </tr>
          ))}</tbody>
        </table>
      </div>
    </div>
  );
}
