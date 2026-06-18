"use client";

import { useState } from "react";
import Link from "next/link";

export default function CreateProductPage() {
  const [submitted, setSubmitted] = useState(false);
  if (submitted) return (
    <div className="container mx-auto px-4 py-16 text-center">
      <div className="max-w-md mx-auto bg-white rounded-2xl shadow-xl p-8">
        <div className="text-5xl mb-4">🎉</div>
        <h1 className="text-2xl font-bold text-gray-800 mb-2">Product Created!</h1>
        <p className="text-gray-500">Your product has been listed.</p>
        <Link href="/vendor/products" className="inline-block mt-6 text-blue-600 hover:underline">Back to Products</Link>
      </div>
    </div>
  );

  return (
    <div className="container mx-auto px-4 py-8 max-w-2xl">
      <h1 className="text-2xl font-bold text-gray-800 mb-6">Add Product</h1>
      <form onSubmit={(e) => { e.preventDefault(); setSubmitted(true); }} className="bg-white rounded-2xl shadow-lg p-8 space-y-6">
        <div><label className="block text-gray-700 font-medium mb-2">Product Name</label><input type="text" required className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" /></div>
        <div><label className="block text-gray-700 font-medium mb-2">Price (ZAR)</label><input type="number" step="0.01" required className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" /></div>
        <div><label className="block text-gray-700 font-medium mb-2">Stock</label><input type="number" required className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" /></div>
        <div><label className="block text-gray-700 font-medium mb-2">Description</label><textarea rows={4} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y" /></div>
        <button type="submit" className="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">Create Product</button>
      </form>
    </div>
  );
}
