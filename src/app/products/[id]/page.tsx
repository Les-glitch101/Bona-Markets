import Link from "next/link";

export default async function ProductDetailPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  return (
    <div className="container mx-auto px-4 py-12">
      <h1 className="text-3xl font-bold text-gray-800 mb-6">Product #{id}</h1>
      <div className="bg-white border border-gray-200 rounded-xl p-12 text-center text-gray-400">Product detail coming soon. <Link href="/products" className="text-blue-600 hover:underline">All Products</Link></div>
    </div>
  );
}
