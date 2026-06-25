import Link from "next/link";
import Image from "next/image";
import { useCart } from "../context/CartContext";

export default function Layout({ children }) {
  const { cart } = useCart();
  const itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);

  return (
    <div
      style={{
        fontFamily: "Arial, sans-serif",
        minHeight: "100vh",
        display: "flex",
        flexDirection: "column",
      }}
    >
      {/* Header with logo + title + navigation */}
      <header
        style={{
          display: "flex",
          justifyContent: "space-between",
          alignItems: "center",
          padding: "10px 20px",
          background: "#333",
          color: "#fff",
        }}
      >
        {/* Logo + Title */}
        <div style={{ display: "flex", alignItems: "center" }}>
          <Image src="/logo.png" alt="Marketplace Logo" width={40} height={40} />
          <h2 style={{ marginLeft: "10px" }}>Buyer Marketplace</h2>
        </div>

        {/* Navigation links */}
        <div>
          <Link href="/" style={{ marginRight: "20px", color: "#fff" }}>Home</Link>
          <Link href="/products" style={{ marginRight: "20px", color: "#fff" }}>Products</Link>
          <Link href="/about" style={{ marginRight: "20px", color: "#fff" }}>About</Link>
          <Link href="/cart" style={{ color: "#fff", textDecoration: "none" }}>
            Cart ({itemCount})
          </Link>
        </div>
      </header>

      {/* Page content */}
      <main style={{ flex: 1, padding: "20px" }}>
        {children}
      </main>

      {/* Footer */}
      <footer
        style={{
          background: "#333",
          color: "#fff",
          textAlign: "center",
          padding: "10px",
        }}
      >
        <p>© {new Date().getFullYear()} Buyer Marketplace. Built with Next.js.</p>
      </footer>
    </div>
  );
}
