"use client";

import { useState } from "react";

export default function VendorProfilePage() {
  const [form, setForm] = useState({ name: "My Store", email: "vendor@bonamarkets.com", phone: "+27 12 345 6789" });

  return (
    <div className="container mx-auto px-4 py-8 max-w-2xl">
      <h1 className="text-2xl font-bold text-gray-800 mb-6">Vendor Profile</h1>
      <form onSubmit={(e) => { e.preventDefault(); alert("Profile updated! (Demo)"); }} className="bg-white rounded-2xl shadow-lg p-8 space-y-6">
        <div><label className="block text-gray-700 font-medium mb-2">Store Name</label><input type="text" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" /></div>
        <div><label className="block text-gray-700 font-medium mb-2">Email</label><input type="email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" /></div>
        <div><label className="block text-gray-700 font-medium mb-2">Phone</label><input type="tel" value={form.phone} onChange={(e) => setForm({ ...form, phone: e.target.value })} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" /></div>
        <button type="submit" className="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">Save Profile</button>
      </form>
    </div>
  );
}
