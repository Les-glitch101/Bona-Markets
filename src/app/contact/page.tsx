"use client";

import { useState } from "react";

export default function ContactPage() {
  const [form, setForm] = useState({ name: "", email: "", subject: "", message: "" });
  const [error, setError] = useState("");
  const [success, setSuccess] = useState(false);

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError(""); setSuccess(false);
    if (!form.name.trim()) { setError("Please enter your full name."); return; }
    if (!form.email.trim() || !form.email.includes("@")) { setError("Please enter a valid email address."); return; }
    if (!form.subject) { setError("Please select a subject."); return; }
    if (!form.message.trim() || form.message.trim().length < 10) { setError("Please enter a message (minimum 10 characters)."); return; }
    setSuccess(true);
  }

  return (
    <>
      <section className="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
        <div className="container mx-auto px-4 py-16 md:py-20 text-center">
          <h1 className="text-3xl md:text-5xl font-bold mb-4">Get in Touch</h1>
          <p className="text-lg md:text-xl opacity-90 max-w-2xl mx-auto">Have questions about Bona Markets? We&apos;d love to hear from you.</p>
        </div>
      </section>
      <div className="container mx-auto px-4 py-12 -mt-6">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div className="lg:col-span-2">
            <div className="bg-white rounded-2xl shadow-lg p-6 md:p-8">
              <h2 className="text-2xl font-bold text-gray-800 mb-2">Send Us a Message</h2>
              <p className="text-gray-500 text-sm mb-6">We&apos;ll get back to you within 24–48 hours.</p>
              <form onSubmit={handleSubmit}>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                  <div><label className="block text-gray-700 font-medium mb-2">Full Name <span className="text-red-500">*</span></label>
                    <input type="text" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Your name" required /></div>
                  <div><label className="block text-gray-700 font-medium mb-2">Email <span className="text-red-500">*</span></label>
                    <input type="email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="you@example.com" required /></div>
                </div>
                <div className="mb-4"><label className="block text-gray-700 font-medium mb-2">Subject <span className="text-red-500">*</span></label>
                  <select value={form.subject} onChange={(e) => setForm({ ...form, subject: e.target.value })} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">Select a topic...</option>
                    <option value="general">General Inquiry</option>
                    <option value="vendor">Become a Vendor</option>
                    <option value="order">Order Issue</option>
                    <option value="product">Product Question</option>
                    <option value="account">Account Help</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div className="mb-4"><label className="block text-gray-700 font-medium mb-2">Message <span className="text-red-500">*</span></label>
                  <textarea value={form.message} onChange={(e) => setForm({ ...form, message: e.target.value })} rows={5} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y" placeholder="Tell us how we can help..." required /></div>
                {error && <div className="bg-red-50 border border-red-200 rounded-lg p-3 mb-4"><p className="text-red-600 text-sm text-center">{error}</p></div>}
                {success && <div className="bg-green-50 border border-green-200 rounded-lg p-3 mb-4"><p className="text-green-600 text-sm text-center">Message sent! We&apos;ll get back to you soon.</p></div>}
                <button type="submit" className="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">Send Message</button>
              </form>
            </div>
          </div>
          <div className="lg:col-span-1 space-y-6">
            <div className="bg-white rounded-2xl shadow-lg p-6">
              <h3 className="text-lg font-bold text-gray-800 mb-4">Contact Information</h3>
              <div className="space-y-4">
                <p className="text-sm"><strong>Email:</strong> support@bonamarkets.com</p>
                <p className="text-sm"><strong>Phone:</strong> +27 12 345 6789</p>
                <p className="text-sm"><strong>Address:</strong> 12 Vilakazi Street, Soweto, Johannesburg, South Africa</p>
              </div>
            </div>
            <div className="bg-white rounded-2xl shadow-lg p-6">
              <h3 className="text-lg font-bold text-gray-800 mb-4">Business Hours</h3>
              <div className="space-y-2 text-sm">
                <div className="flex justify-between"><span>Monday – Friday</span><span className="font-medium">9:00 AM – 6:00 PM</span></div>
                <div className="flex justify-between"><span>Saturday</span><span className="font-medium">10:00 AM – 4:00 PM</span></div>
                <div className="flex justify-between"><span>Sunday</span><span className="text-gray-400">Closed</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <section className="bg-white py-12">
        <div className="container mx-auto px-4">
          <h2 className="text-2xl md:text-3xl font-bold text-gray-800 text-center mb-8">FAQ</h2>
          <div className="max-w-3xl mx-auto space-y-4">
            {[{ q: "How do I become a vendor?", a: 'Click "Become a Vendor" on our homepage and fill out the application.' },
              { q: "What payment methods do you accept?", a: "We accept Visa, Mastercard, and PayPal." },
              { q: "How long does shipping take?", a: "Most orders ship within 1-3 business days." }].map((faq) => (
              <details key={faq.q} className="border border-gray-200 rounded-lg p-4">
                <summary className="font-semibold text-gray-800 cursor-pointer">{faq.q}</summary>
                <p className="text-gray-600 text-sm mt-3">{faq.a}</p>
              </details>
            ))}
          </div>
        </div>
      </section>
    </>
  );
}
