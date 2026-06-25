import Link from "next/link";
import { useCart } from "../context/CartContext";

export default function Navbar() {
  const { cart } = useCart();
  const itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);

  return (
    <nav style={{ background: "#333", padding: "10px" }}>
      <Link href="/" style={{ color: "#fff", marginRight: "15px" }}>
        Home
      </Link>
      <Link href="/products" style={{ color: "#fff", marginRight: "15px" }}>
        Products
      </Link>
      <Link href="/about" style={{ color: "#fff", marginRight: "15px" }}>
        About
      </Link>
      <Link href="/cart" style={{ color: "#fff" }}>
        Cart ({itemCount})
      </Link>
    </nav>
  );
}

