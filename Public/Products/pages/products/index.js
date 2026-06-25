import Layout from "../../components/Layout";
import { useCart } from "../../context/CartContext";
import products from "../../data/products";
import categories from "../../data/categories";
import Link from "next/link";
import { useState, useEffect } from "react";
import { useRouter } from "next/router";

export default function Products() {
  const { addToCart } = useCart();
  const router = useRouter();

  const [search, setSearch] = useState("");
  const [categoryFilter, setCategoryFilter] = useState("");
  const [priceRange, setPriceRange] = useState([0, 20000]);

  useEffect(() => {
    if (router.query.category) {
      setCategoryFilter(router.query.category);
    }
  }, [router.query.category]);

  // Filtering logic
  const filtered = products.filter((p) => {
    const matchesSearch = p.name.toLowerCase().includes(search.toLowerCase());
    const categoryName = categories.find((c) => c.id === p.categoryId)?.name;
    const matchesCategory = categoryFilter ? categoryName === categoryFilter : true;
    const matchesPrice = p.price >= priceRange[0] && p.price <= priceRange[1];
    return matchesSearch && matchesCategory && matchesPrice;
  });

  // Pastel theme colors
  const categoryColors = {
    Electronics: "#a3c9a8",
    Clothing: "#c8b7a6",
    "Home & Garden": "#d3d3d3",
    Accessories: "#f9d5a7",
    "Beauty & Wellness": "#f7e7ce",
    Handcrafts: "#c8b7a6",
    "Food & Spices": "#a3c9a8",
    "Art & Decor": "#f9d5a7",
    Jewellery: "#d3d3d3"
  };

  return (
    <Layout>
      <div style={{ backgroundColor: "#ffffff", minHeight: "100vh", padding: "20px", fontFamily: "Arial" }}>
        <h1 style={{ color: "#333" }}>Product Catalogue</h1>

        {/* Search + Price Filter */}
        <div style={{ display: "flex", gap: "20px", marginBottom: "20px" }}>
          <input
            type="text"
            placeholder="Search products..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            style={{ flex: 1, padding: "10px", border: "1px solid #d3d3d3" }}
          />
          <div>
            <label>Price up to R{priceRange[1]}</label>
            <input
              type="range"
              min="0"
              max="20000"
              value={priceRange[1]}
              onChange={(e) => setPriceRange([0, parseInt(e.target.value)])}
            />
          </div>
        </div>

        {/* Category Buttons */}
        <div style={{ display: "flex", gap: "15px", marginBottom: "30px" }}>
          <button onClick={() => setCategoryFilter("")}>All</button>
          {categories.map((c) => (
            <button
              key={c.id}
              onClick={() => setCategoryFilter(c.name)}
              style={{
                background: categoryColors[c.name] || "#eee",
                padding: "8px 12px",
                border: "none",
                borderRadius: "5px",
                cursor: "pointer",
                fontWeight: "bold"
              }}
            >
              {c.name}
            </button>
          ))}
        </div>

        {/* Product Grid */}
        <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(200px, 1fr))", gap: "20px" }}>
          {filtered.map((product) => {
            const category = categories.find((c) => c.id === product.categoryId);
            return (
              <div key={product.id} style={{ border: "1px solid #ddd", borderRadius: "8px", padding: "15px", background: "#fafafa", position: "relative" }}>
                
                {/* Badges */}
                {product.isNew && (
                  <span style={{ background: "#a3c9a8", padding: "4px 8px", borderRadius: "5px", position: "absolute", top: "10px", left: "10px" }}>
                    NEW
                  </span>
                )}
                {product.bestSeller && (
                  <span style={{ background: "#f7e7ce", padding: "4px 8px", borderRadius: "5px", position: "absolute", top: "10px", right: "10px" }}>
                    BEST SELLER
                  </span>
                )}
                {product.stock !== undefined && product.stock < 5 && (
                  <span style={{ background: "#d3d3d3", padding: "4px 8px", borderRadius: "5px", position: "absolute", bottom: "10px", right: "10px" }}>
                    LOW STOCK
                  </span>
                )}

                {/* Product Image */}
                {product.image && (
                  <img
                    src={product.image}
                    alt={product.name}
                    style={{ width: "100%", height: "180px", objectFit: "cover", borderRadius: "5px" }}
                  />
                )}

                {/* Product Info */}
                <h3 style={{ margin: "10px 0" }}>{product.name}</h3>
                <p style={{ color: "#777" }}>{category?.name}</p>
                <p style={{ fontWeight: "bold", fontSize: "18px" }}>R{product.price}</p>

                {/* Add to Cart */}
                <button
                  onClick={() => addToCart(product)}
                  style={{
                    marginTop: "10px",
                    background: "#a3c9a8",
                    padding: "10px 15px",
                    border: "none",
                    borderRadius: "5px",
                    cursor: "pointer"
                  }}
                >
                  Add to Cart
                </button>

                {/* View Details Link */}
                <div style={{ marginTop: "10px" }}>
                  <Link href={`/products/${product.id}`} style={{ color: "#333", textDecoration: "none", fontWeight: "bold" }}>
                    View Details
                  </Link>
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </Layout>
  );
}
