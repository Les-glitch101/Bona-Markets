import Layout from "../../components/Layout";
import { useCart } from "../../context/CartContext";
import products from "../../data/products";
import categories from "../../data/categories";
import Link from "next/link";   // ✅ Added missing import
import { useRouter } from "next/router";

export default function ProductDetail() {
  const router = useRouter();
  const { addToCart } = useCart();

  // ✅ Prevent hydration mismatch
  if (!router.isReady) return null;

  const { id } = router.query;
  const product = products.find((p) => p.id === parseInt(id));
  const category = product ? categories.find((c) => c.id === product.categoryId) : null;

  if (!product) {
    return (
      <Layout>
        <div style={{ padding: "20px" }}>
          <h1>Product not found</h1>
          <Link href="/products">Back to Catalogue</Link>
        </div>
      </Layout>
    );
  }

  return (
    <Layout>
      <div style={{ padding: "20px", fontFamily: "Arial", backgroundColor: "#ffffff", minHeight: "100vh" }}>
        <h1 style={{ color: "#333" }}>{product.name}</h1>
        <p><strong>Category:</strong> {category?.name}</p>
        <p><strong>Price:</strong> R{product.price}</p>
        <p><strong>Description:</strong> {product.description || "No description available."}</p>

        {/* Product Image */}
        {product.image && (
          <img
            src={product.image}
            alt={product.name}
            style={{ width: "300px", borderRadius: "8px", marginTop: "20px" }}
          />
        )}

        {/* Badges */}
        <div style={{ marginTop: "10px" }}>
          {product.isNew && (
            <span style={{ background: "#a3c9a8", padding: "5px 10px", borderRadius: "5px", marginRight: "10px" }}>
              NEW
            </span>
          )}
          {product.bestSeller && (
            <span style={{ background: "#f7e7ce", padding: "5px 10px", borderRadius: "5px", marginRight: "10px" }}>
              BEST SELLER
            </span>
          )}
          {product.stock !== undefined && product.stock < 5 && (
            <span style={{ background: "#d3d3d3", padding: "5px 10px", borderRadius: "5px" }}>
              LOW STOCK
            </span>
          )}
        </div>

        {/* Add to Cart */}
        <button
          onClick={() => addToCart(product)}
          style={{
            marginTop: "20px",
            padding: "10px 15px",
            background: "#a3c9a8",
            border: "none",
            borderRadius: "5px",
            cursor: "pointer"
          }}
        >
          Add to Cart
        </button>

        {/* Back to Catalogue */}
        <div style={{ marginTop: "20px" }}>
          <Link href="/products" style={{ color: "#333", textDecoration: "none", fontWeight: "bold" }}>
            ← Back to Catalogue
          </Link>
        </div>
      </div>
    </Layout>
  );
}
