"use client";

import { useState } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";

export default function RegisterPage() {
  const router = useRouter();
  const [form, setForm] = useState({ fullname: "Demo User", email: "demo@bonamarkets.com", role: "buyer", password: "demo123", confirmPassword: "demo123" });
  const [terms, setTerms] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirm, setShowConfirm] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState(false);

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError(""); setSuccess(false);
    if (!form.fullname || form.fullname.trim().length < 2) { setError("Please enter your full name"); return; }
    if (!form.email || !form.email.includes("@")) { setError("Please enter a valid email address"); return; }
    if (!form.password || form.password.length < 6) { setError("Password must be at least 6 characters"); return; }
    if (form.password !== form.confirmPassword) { setError("Passwords do not match"); return; }
    if (!terms) { setError("You must agree to the Terms of Service"); return; }
    setSuccess(true);
    setTimeout(() => router.push("/login"), 1500);
  }

  return (
    <main className="min-h-screen flex items-center justify-center py-12 px-4">
      <div className="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <div className="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-8 text-center">
          <div className="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
          </div>
          <h1 className="text-2xl font-bold text-white">Create Account</h1>
          <p className="text-blue-100 text-sm mt-1">Join Bona Markets today</p>
        </div>
        <div className="p-6 md:p-8">
          <form onSubmit={handleSubmit}>
            <div className="mb-4">
              <label className="block text-gray-700 font-medium mb-2">Full Name</label>
              <input type="text" value={form.fullname} onChange={(e) => setForm({ ...form, fullname: e.target.value })} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="John Doe" required />
            </div>
            <div className="mb-4">
              <label className="block text-gray-700 font-medium mb-2">Email Address</label>
              <input type="email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="you@example.com" required />
            </div>
            <div className="mb-4">
              <label className="block text-gray-700 font-medium mb-2">I want to...</label>
              <select value={form.role} onChange={(e) => setForm({ ...form, role: e.target.value })} className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                <option value="buyer">Shop as a Buyer</option>
                <option value="vendor">Sell as a Vendor</option>
              </select>
            </div>
            <div className="mb-4">
              <label className="block text-gray-700 font-medium mb-2">Password</label>
              <div className="relative">
                <input type={showPassword ? "text" : "password"} value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} className="w-full pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Create a password" required />
                <button type="button" onClick={() => setShowPassword(!showPassword)} className="absolute inset-y-0 right-0 pr-3 flex items-center">
                  <svg className="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                </button>
              </div>
            </div>
            <div className="mb-5">
              <label className="block text-gray-700 font-medium mb-2">Confirm Password</label>
              <div className="relative">
                <input type={showConfirm ? "text" : "password"} value={form.confirmPassword} onChange={(e) => setForm({ ...form, confirmPassword: e.target.value })} className="w-full pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Confirm password" required />
                <button type="button" onClick={() => setShowConfirm(!showConfirm)} className="absolute inset-y-0 right-0 pr-3 flex items-center">
                  <svg className="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                </button>
              </div>
            </div>
            <div className="flex items-start mb-6">
              <input type="checkbox" id="terms" checked={terms} onChange={(e) => setTerms(e.target.checked)} className="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mt-0.5" />
              <label htmlFor="terms" className="ml-2 text-sm text-gray-600">I agree to the <a href="#" className="text-blue-600 hover:underline">Terms of Service</a> and <a href="#" className="text-blue-600 hover:underline">Privacy Policy</a></label>
            </div>
            {error && <div className="bg-red-50 border border-red-200 rounded-lg p-3 mb-4"><p className="text-red-600 text-sm text-center">{error}</p></div>}
            {success && <div className="bg-green-50 border border-green-200 rounded-lg p-3 mb-4"><p className="text-green-600 text-sm text-center">Account created! Redirecting to login...</p></div>}
            <button type="submit" className="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">Create Account</button>
            <div className="relative my-6"><div className="absolute inset-0 flex items-center"><div className="w-full border-t border-gray-300" /></div><div className="relative flex justify-center text-sm"><span className="px-2 bg-white text-gray-500">Or sign up with</span></div></div>
            <div className="grid grid-cols-2 gap-3 mb-6">
              <button type="button" className="flex items-center justify-center gap-2 border border-gray-300 rounded-lg py-2 hover:bg-gray-50 transition">
                <svg className="h-5 w-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" /><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" /><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" /><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" /></svg><span className="text-sm">Google</span>
              </button>
              <button type="button" className="flex items-center justify-center gap-2 border border-gray-300 rounded-lg py-2 hover:bg-gray-50 transition">
                <svg className="h-5 w-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z" /></svg><span className="text-sm">Facebook</span>
              </button>
            </div>
            <p className="text-center text-gray-600 text-sm">Already have an account? <Link href="/login" className="text-blue-600 font-semibold hover:underline">Sign in</Link></p>
          </form>
        </div>
      </div>
    </main>
  );
}
