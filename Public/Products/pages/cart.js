import Layout from "../components/Layout";
import { useCart } from "../context/CartContext";

export default function Cart() {
  const { cart, removeFromCart, decreaseQuantity, addToCart, clearCart, getTotalPrice } = useCart();

  return (
    <Layout>
      <h1>Your Cart</h1>
      {cart.length === 0 ? (
        <p>No items in cart.</p>
      ) : (
        <>
          <table style={{ width: "100%", borderCollapse: "collapse", marginTop: "20px" }}>
            <thead>
              <tr style={{ background: "#f0f0f0" }}>
                <th style={{ border: "1px solid #ccc", padding: "10px" }}>Image</th>
                <th style={{ border: "1px solid #ccc", padding: "10px" }}>Product</th>
                <th style={{ border: "1px solid #ccc", padding: "10px" }}>Category</th>
                <th style={{ border: "1px solid #ccc", padding: "10px" }}>Price</th>
                <th style={{ border: "1px solid #ccc", padding: "10px" }}>Quantity</th>
                <th style={{ border: "1px solid #ccc", padding: "10px" }}>Actions</th>
              </tr>
            </thead>
            <tbody>
              {cart.map((item) => (
                <tr key={item.id}>
                  {/* Product image */}
                  <td style={{ border: "1px solid #ccc", padding: "10px" }}>
                    <img
                      src={item.image || "/placeholder.png"} // make sure your products have an image field
                      alt={item.name}
                      style={{ width: "60px", height: "60px", objectFit: "cover" }}
                    />
                  </td>
                  <td style={{ border: "1px solid #ccc", padding: "10px" }}>{item.name}</td>
                  <td style={{ border: "1px solid #ccc", padding: "10px" }}>{item.category}</td>
                  <td style={{ border: "1px solid #ccc", padding: "10px" }}>R{item.price}</td>
                  <td style={{ border: "1px solid #ccc", padding: "10px" }}>{item.quantity}</td>
                  <td style={{ border: "1px solid #ccc", padding: "10px" }}>
                    <button
                      onClick={() => decreaseQuantity(item.id)}
                      style={{ background: "#ff9800", color: "#fff", marginRight: "5px", padding: "5px 10px", border: "none", cursor: "pointer" }}
                    >
                      -
                    </button>
                    <button
                      onClick={() => addToCart(item)}
                      style={{ background: "#4caf50", color: "#fff", marginRight: "5px", padding: "5px 10px", border: "none", cursor: "pointer" }}
                    >
                      +
                    </button>
                    <button
                      onClick={() => removeFromCart(item.id)}
                      style={{ background: "#f44336", color: "#fff", padding: "5px 10px", border: "none", cursor: "pointer" }}
                    >
                      Remove
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>

          <h2 style={{ marginTop: "20px" }}>Total: R{getTotalPrice()}</h2>

          {/* Clear Cart button */}
          <button
            onClick={clearCart}
            style={{
              marginTop: "10px",
              background: "red",
              color: "#fff",
              padding: "10px 20px",
              border: "none",
              cursor: "pointer",
            }}
          >
            Clear Cart
          </button>
        </>
      )}
    </Layout>
  );
}
