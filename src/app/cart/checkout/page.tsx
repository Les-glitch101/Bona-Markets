import Link from "next/link";

export default function CheckoutPage() {
  return (
    <div className="container mx-auto px-4 py-12">
      <h1 className="text-3xl font-bold text-gray-800 mb-6">Checkout</h1>
      <div className="bg-white border border-gray-200 rounded-xl p-12 text-center text-gray-400">Checkout coming soon. <Link href="/cart" className="text-blue-600 hover:underline">Back to Cart</Link></div>
    </div>
  );
}
