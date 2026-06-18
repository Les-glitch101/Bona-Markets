import Link from "next/link";

export default function CartSuccessPage() {
  return (
    <div className="container mx-auto px-4 py-12 text-center">
      <div className="max-w-md mx-auto bg-white rounded-2xl shadow-xl p-8">
        <div className="text-5xl mb-4">✅</div>
        <h1 className="text-2xl font-bold text-gray-800 mb-2">Order Placed!</h1>
        <p className="text-gray-500">Thank you for your purchase.</p>
        <Link href="/" className="inline-block mt-6 text-blue-600 hover:underline">Continue Shopping</Link>
      </div>
    </div>
  );
}
