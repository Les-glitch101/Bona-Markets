"use client";

import { useState } from "react";
import Link from "next/link";

export default function EditProductPage() {
  const [form, setForm] = useState({ name: "Air Force 1 - 2026", price: "1333", stock: "10" });

  return (
    <div className="container mx-auto px-4 py-8 max-w-2xl">
      <h1 className="text-2xl font-bold text-gray-800 mb-6">Edit Product</h1>
      <form onSubmit={(e) => { e.preventDefault(); alert("Product updated! (Demo)"); }} className="bg-white rounded-2xl shadow-lg p-8 space-y-6">
        <div><label className="block text-gray-700 font-medium mb-2">Product Name</label><input type="text" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" /></div>
        <div><label className="block text-gray-700 font-medium mb-2">Price (ZAR)</label><input type="number" step="0.01" value={form.price} onChange={(e) => setForm({ ...form, price: e.target.value })} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" /></div>
        <div><label className="block text-gray-700 font-medium mb-2">Stock</label><input type="number" value={form.stock} onChange={(e) => setForm({ ...form, stock: e.target.value })} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" /></div>
        <button type="submit" className="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">Save Changes</button>
        <Link href="/vendor/products" className="block text-center text-sm text-gray-500 hover:underline">Cancel</Link>
      </form>
    </div>
  );
}
