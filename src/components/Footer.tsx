import Link from "next/link";

export default function Footer() {
  return (
    <footer className="bg-gray-800 text-white pt-12 pb-6">
      <div className="container mx-auto px-4">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <h3 className="text-xl font-bold mb-3">Bona Markets</h3>
            <p className="text-gray-400 text-sm">Your trusted multi-vendor marketplace</p>
          </div>
          <div>
            <h4 className="font-semibold mb-3">Shop</h4>
            <ul className="space-y-2 text-gray-400 text-sm">
              <li><Link href="/" className="hover:text-white">All Products</Link></li>
              <li><Link href="#" className="hover:text-white">Categories</Link></li>
              <li><Link href="#" className="hover:text-white">Best Sellers</Link></li>
            </ul>
          </div>
          <div>
            <h4 className="font-semibold mb-3">Sell</h4>
            <ul className="space-y-2 text-gray-400 text-sm">
              <li><Link href="/vendor/apply" className="hover:text-white">Become a Vendor</Link></li>
              <li><Link href="/vendor/dashboard" className="hover:text-white">Vendor Dashboard</Link></li>
            </ul>
          </div>
          <div>
            <h4 className="font-semibold mb-3">Support</h4>
            <ul className="space-y-2 text-gray-400 text-sm">
              <li><Link href="/" className="hover:text-white">Home</Link></li>
              <li><Link href="/products" className="hover:text-white">Shop</Link></li>
              <li><Link href="/contact" className="hover:text-white">Contact Us</Link></li>
            </ul>
          </div>
        </div>
        <div className="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400 text-sm">
          <p>&copy; 2025 Bona Markets. All rights reserved.</p>
        </div>
      </div>
    </footer>
  );
}
