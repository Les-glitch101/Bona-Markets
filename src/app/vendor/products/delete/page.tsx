"use client";

import { useRouter } from "next/navigation";
import Link from "next/link";

export default function DeleteProductPage() {
  const router = useRouter();

  return (
    <div className="container mx-auto px-4 py-16 text-center">
      <div className="max-w-md mx-auto bg-white rounded-2xl shadow-xl p-8">
        <div className="text-5xl mb-4">⚠️</div>
        <h1 className="text-2xl font-bold text-gray-800 mb-2">Delete Product</h1>
        <p className="text-gray-500 mb-6">Are you sure? This action cannot be undone.</p>
        <div className="flex gap-3 justify-center">
          <button onClick={() => { alert("Product deleted! (Demo)"); router.push("/vendor/products"); }} className="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">Delete</button>
          <Link href="/vendor/products" className="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">Cancel</Link>
        </div>
      </div>
    </div>
  );
}
