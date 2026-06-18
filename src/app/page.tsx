import Link from "next/link";

const featuredProducts = [
  { id: 1, name: "Wireless Headphones", category: "Electronics", vendor: "TechStore", price: "R79.99", rating: 4, reviews: 24, image: "https://placehold.co/400x300/3B82F6/white?text=Headphones" },
  { id: 2, name: "Smart Watch Pro", category: "Accessories", vendor: "GadgetHub", price: "R199.99", rating: 5, reviews: 102, image: "https://placehold.co/400x300/10B981/white?text=Watch" },
  { id: 3, name: "Denim Jacket", category: "Clothing", vendor: "FashionHub", price: "R89.99", rating: 4, reviews: 56, image: "https://placehold.co/400x300/F59E0B/white?text=Jacket" },
  { id: 4, name: "Smart LED Lamp", category: "Home & Garden", vendor: "HomeStyle", price: "R34.99", rating: 3, reviews: 18, image: "https://placehold.co/400x300/EF4444/white?text=Lamp" },
];

function Stars({ count }: { count: number }) {
  return <span className="text-yellow-400">{'★'.repeat(count)}{'☆'.repeat(5 - count)}</span>;
}

export default function HomePage() {
  return (
    <>
      <section className="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
        <div className="container mx-auto px-4 py-12 md:py-16 text-center">
          <h1 className="text-3xl md:text-5xl font-bold mb-4">Welcome to Bona Markets</h1>
          <p className="text-lg md:text-xl opacity-90 max-w-2xl mx-auto">Discover unique products from trusted vendors around the world</p>
          <div className="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <Link href="#" className="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">Start Shopping</Link>
            <Link href="/vendor/apply" className="border border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">Become a Vendor</Link>
          </div>
        </div>
      </section>

      <div className="container mx-auto px-4 -mt-6">
        <div className="bg-white rounded-lg shadow-md p-4 flex flex-col md:flex-row gap-3">
          <input type="text" placeholder="Search products..." className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
          <select className="px-4 py-2 border border-gray-300 rounded-lg bg-white"><option>All Categories</option><option>Electronics</option><option>Clothing</option><option>Home & Garden</option><option>Accessories</option></select>
          <select className="px-4 py-2 border border-gray-300 rounded-lg bg-white"><option>Newest First</option><option>Price: Low to High</option><option>Price: High to Low</option></select>
          <button className="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Search</button>
        </div>
      </div>

      <div className="container mx-auto px-4 py-12">
        <div className="flex justify-between items-center mb-6">
          <h2 className="text-2xl md:text-3xl font-bold text-gray-800">Featured Products</h2>
          <Link href="#" className="text-blue-600 hover:underline">View All &rarr;</Link>
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          {featuredProducts.map((p) => (
            <div key={p.id} className="product-card bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
              <img src={p.image} alt={p.name} className="w-full h-48 object-cover" />
              <div className="p-4">
                <div className="text-xs text-gray-500 mb-1">{p.category}</div>
                <h3 className="font-semibold text-lg text-gray-800">{p.name}</h3>
                <p className="text-gray-500 text-sm mt-1">by {p.vendor}</p>
                <div className="flex items-center mt-2"><Stars count={p.rating} /><span className="text-xs text-gray-500 ml-2">({p.reviews})</span></div>
                <div className="flex justify-between items-center mt-3">
                  <span className="text-xl font-bold text-blue-600">{p.price}</span>
                  <button className="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition">Add to Cart</button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      <div className="bg-white py-12">
        <div className="container mx-auto px-4">
          <h2 className="text-2xl md:text-3xl font-bold text-gray-800 text-center mb-8">Shop by Category</h2>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            {[{ emoji: "📱", name: "Electronics", count: 48, bg: "bg-blue-50 hover:bg-blue-100" }, { emoji: "👕", name: "Clothing", count: 126, bg: "bg-green-50 hover:bg-green-100" }, { emoji: "🏠", name: "Home & Garden", count: 67, bg: "bg-yellow-50 hover:bg-yellow-100" }, { emoji: "⌚", name: "Accessories", count: 34, bg: "bg-purple-50 hover:bg-purple-100" }].map((cat) => (
              <div key={cat.name} className={`${cat.bg} rounded-xl p-6 text-center transition cursor-pointer`}>
                <div className="text-4xl mb-2">{cat.emoji}</div>
                <h3 className="font-semibold">{cat.name}</h3>
                <p className="text-sm text-gray-500">{cat.count} products</p>
              </div>
            ))}
          </div>
        </div>
      </div>

      <div className="container mx-auto px-4 py-12">
        <div className="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl text-white p-8 md:p-12 text-center">
          <h2 className="text-2xl md:text-3xl font-bold mb-3">Sell on Bona Markets</h2>
          <p className="text-lg opacity-90 max-w-2xl mx-auto mb-6">Join thousands of vendors who grow their business with us</p>
          <Link href="/vendor/apply" className="inline-block bg-white text-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">Apply as Vendor &rarr;</Link>
        </div>
      </div>
    </>
  );
}
