import Layout from "../components/Layout";
import Link from "next/link";
import products from "../data/products";
import categories from "../data/categories";

export default function Home() {
  const bestSellers = products.filter((p) => p.bestSeller);
  const newArrivals = products.filter((p) => p.isNew);

  // Simplified pastel theme
  const categoryColors = {
    Electronics: "#a3c9a8",   // pastel green
    Clothing: "#c8b7a6",      // pastel brown
    "Home & Garden": "#d3d3d3", // soft gray
    Accessories: "#a3c9a8",   // green
    "Beauty & Wellness": "#c8b7a6", // brown
    Handcrafts: "#d3d3d3",    // gray
    "Food & Spices": "#a3c9a8", // green
    "Art & Decor": "#c8b7a6", // brown
    Jewellery: "#d3d3d3"      // gray
  };

  return (
    <Layout>
      <div style={{ backgroundColor: "#ffffff", minHeight: "100vh", color: "#333" }}>
        {/* Hero Banner */}
        <section
          style={{
            background: "linear-gradient(135deg, #c8b7a6, #a3c9a8)",
            color: "#333",
            padding: "40px",
            borderRadius: "10px",
            textAlign: "center",
            marginBottom: "40px"
          }}
        >
          <h1 style={{ fontSize: "2.5rem", marginBottom: "10px" }}>
            Welcome to Buyer Marketplace
          </h1>
          <p style={{ fontSize: "1.2rem", marginBottom: "20px" }}>
            Shop the best deals on electronics, fashion, and more!
          </p>
          <Link
            href="/products"
            style={{
              background: "#a3c9a8", // pastel green
              color: "#333",
              padding: "12px 20px",
              borderRadius: "5px",
              fontWeight: "bold"
            }}
          >
            Shop Now
          </Link>
        </section>

        {/* Categories Section */}
        <section style={{ marginBottom: "40px" }}>
          <h2>Shop by Category</h2>
          <div
            style={{
              display: "grid",
              gridTemplateColumns: "repeat(auto-fill, minmax(150px, 1fr))",
              gap: "20px",
              marginTop: "20px"
            }}
          >
            {categories.map((c) => (
              <Link key={c.id} href={`/products?category=${c.name}`}>
                <div
                  style={{
                    flex: 1,
                    background: categoryColors[c.name] || "#f0f0f0",
                    padding: "20px",
                    borderRadius: "8px",
                    textAlign: "center",
                    color: "#333",
                    fontWeight: "bold",
                    cursor: "pointer"
                  }}
                >
                  {c.name}
                </div>
              </Link>
            ))}
          </div>
        </section>

        {/* Best Sellers Section */}
        <section style={{ marginBottom: "40px" }}>
          <h2>Best Sellers</h2>
          <div
            style={{
              display: "grid",
              gridTemplateColumns: "repeat(auto-fill, minmax(220px, 1fr))",
              gap: "20px",
              marginTop: "20px"
            }}
          >
            {bestSellers.map((product) => (
              <div
                key={product.id}
                style={{
                  border: "1px solid #ddd",
                  borderRadius: "10px",
                  padding: "15px",
                  textAlign: "center",
                  background: "#fff",
                  boxShadow: "0 2px 6px rgba(0,0,0,0.1)"
                }}
              >
                <img
                  src={product.image}
                  alt={product.name}
                  style={{
                    width: "100%",
                    height: "180px",
                    objectFit: "cover",
                    borderRadius: "8px"
                  }}
                />
                <h3 style={{ margin: "10px 0" }}>{product.name}</h3>
                <p style={{ color: "#777" }}>
                  {categories.find((c) => c.id === product.categoryId)?.name}
                </p>
                <p style={{ fontWeight: "bold", fontSize: "18px", color: "#333" }}>
                  R{product.price}
                </p>
                <Link href={`/products/${product.id}`} style={{ color: "#0070f3" }}>
                  View Details
                </Link>
              </div>
            ))}
          </div>
        </section>

        {/* New Arrivals Section */}
        <section>
          <h2>New Arrivals</h2>
          <div
            style={{
              display: "grid",
              gridTemplateColumns: "repeat(auto-fill, minmax(220px, 1fr))",
              gap: "20px",
              marginTop: "20px"
            }}
          >
            {newArrivals.map((product) => (
              <div
                key={product.id}
                style={{
                  border: "1px solid #ddd",
                  borderRadius: "10px",
                  padding: "15px",
                  textAlign: "center",
                  background: "#fff",
                  boxShadow: "0 2px 6px rgba(0,0,0,0.1)"
                }}
              >
                <img
                  src={product.image}
                  alt={product.name}
                  style={{
                    width: "100%",
                    height: "180px",
                    objectFit: "cover",
                    borderRadius: "8px"
                  }}
                />
                <h3 style={{ margin: "10px 0" }}>{product.name}</h3>
                <p style={{ color: "#777" }}>
                  {categories.find((c) => c.id === product.categoryId)?.name}
                </p>
                <p style={{ fontWeight: "bold", fontSize: "18px", color: "#333" }}>
                  R{product.price}
                </p>
                <Link href={`/products/${product.id}`} style={{ color: "#0070f3" }}>
                  View Details
                </Link>
              </div>
            ))}
          </div>
        </section>
      </div>
    </Layout>
  );
}
