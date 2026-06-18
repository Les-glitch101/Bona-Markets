"use client";

import { useState } from "react";
import Link from "next/link";

export default function VendorApplyPage() {
  const [submitted, setSubmitted] = useState(false);
  if (submitted) return (
    <div className="container mx-auto px-4 py-16 text-center">
      <div className="max-w-md mx-auto bg-white rounded-2xl shadow-xl p-8">
        <div className="text-5xl mb-4">🎉</div>
        <h1 className="text-2xl font-bold text-gray-800 mb-2">Application Submitted!</h1>
        <p className="text-gray-500">We&apos;ll review it within 2-3 business days.</p>
        <Link href="/" className="inline-block mt-6 text-blue-600 hover:underline">Back to Home</Link>
      </div>
    </div>
  );

  return (
    <div className="container mx-auto px-4 py-12">
      <div className="max-w-2xl mx-auto">
        <h1 className="text-3xl font-bold text-gray-800 mb-2">Become a Vendor</h1>
        <p className="text-gray-500 mb-8">Fill out the form to apply as a vendor on Bona Markets.</p>
        <form onSubmit={(e) => { e.preventDefault(); setSubmitted(true); }} className="bg-white rounded-2xl shadow-lg p-8 space-y-6">
          <div><label className="block text-gray-700 font-medium mb-2">Business Name</label><input type="text" required className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" /></div>
          <div><label className="block text-gray-700 font-medium mb-2">Email</label><input type="email" required className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" /></div>
          <div><label className="block text-gray-700 font-medium mb-2">Phone</label><input type="tel" required className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" /></div>
          <div><label className="block text-gray-700 font-medium mb-2">Description</label><textarea rows={4} required className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y" /></div>
          <button type="submit" className="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">Submit Application</button>
        </form>
      </div>
    </div>
  );
}
