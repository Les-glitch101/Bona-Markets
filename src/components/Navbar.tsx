"use client";

import Link from "next/link";
import { useState } from "react";

export default function Navbar() {
  const [mobileOpen, setMobileOpen] = useState(false);

  return (
    <nav className="bg-white shadow-md sticky top-0 z-50">
      <div className="container mx-auto px-4 py-3">
        <div className="flex justify-between items-center">
          <Link href="/" className="text-2xl md:text-3xl font-bold text-blue-600 tracking-tight">Bona Markets</Link>

          <div className="hidden md:flex items-center space-x-8">
            <Link href="/" className="text-gray-600 hover:text-blue-600 font-medium">Shop</Link>
            <Link href="#" className="text-gray-600 hover:text-blue-600 font-medium">Categories</Link>
            <Link href="#" className="text-gray-600 hover:text-blue-600 font-medium">About</Link>
            <Link href="/contact" className="text-gray-600 hover:text-blue-600 font-medium">Contact</Link>
          </div>

          <div className="hidden md:flex items-center space-x-4">
            <Link href="/login" className="text-gray-600 hover:text-blue-600 font-medium">Login</Link>
            <Link href="/register" className="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">Sign Up</Link>
          </div>

          <button onClick={() => setMobileOpen(!mobileOpen)} className="md:hidden text-gray-600 focus:outline-none">
            <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>

        {mobileOpen && (
          <div className="md:hidden mt-4 pb-3 border-t pt-4 flex flex-col space-y-3">
            <Link href="/" className="text-gray-600 hover:text-blue-600 py-1">Shop</Link>
            <Link href="#" className="text-gray-600 hover:text-blue-600 py-1">Categories</Link>
            <Link href="#" className="text-gray-600 hover:text-blue-600 py-1">About</Link>
            <Link href="/contact" className="text-gray-600 hover:text-blue-600 py-1">Contact</Link>
            <div className="flex flex-col space-y-2 pt-2">
              <Link href="/login" className="text-gray-600 hover:text-blue-600 py-1">Login</Link>
              <Link href="/register" className="bg-blue-600 text-white text-center px-4 py-2 rounded-lg">Sign Up</Link>
            </div>
          </div>
        )}
      </div>
    </nav>
  );
}
