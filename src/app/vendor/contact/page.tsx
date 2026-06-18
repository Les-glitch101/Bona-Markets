"use client";

import { useState } from "react";

export default function VendorContactPage() {
  const [form, setForm] = useState({ name: "", email: "", subject: "", message: "" });
  const [error, setError] = useState("");
  const [success, setSuccess] = useState(false);

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError(""); setSuccess(false);
    if (!form.name.trim() || !form.email.includes("@") || !form.subject || !form.message.trim()) {
      setError("Please fill in all fields correctly."); return;
    }
    setSuccess(true);
  }

  return (
    <div className="container mx-auto px-4 py-12">
      <div className="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-8">
        <h1 className="text-2xl font-bold text-gray-800 mb-2">Vendor Support</h1>
        <p className="text-gray-500 text-sm mb-6">Questions about selling? We&apos;re here to help.</p>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label className="block text-gray-700 font-medium mb-2">Name</label><input type="text" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" /></div>
            <div><label className="block text-gray-700 font-medium mb-2">Email</label><input type="email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" /></div>
          </div>
          <div><label className="block text-gray-700 font-medium mb-2">Subject</label>
            <select value={form.subject} onChange={(e) => setForm({ ...form, subject: e.target.value })} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
              <option value="">Select...</option><option value="support">Vendor Support</option><option value="billing">Billing</option><option value="products">Product Help</option>
            </select>
          </div>
          <div><label className="block text-gray-700 font-medium mb-2">Message</label><textarea value={form.message} onChange={(e) => setForm({ ...form, message: e.target.value })} rows={5} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y" /></div>
          {error && <div className="bg-red-50 border border-red-200 rounded-lg p-3"><p className="text-red-600 text-sm text-center">{error}</p></div>}
          {success && <div className="bg-green-50 border border-green-200 rounded-lg p-3"><p className="text-green-600 text-sm text-center">Message sent!</p></div>}
          <button type="submit" className="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">Send</button>
        </form>
      </div>
    </div>
  );
}
