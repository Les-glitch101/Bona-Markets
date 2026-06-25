import Link from "next/link";

export default function Products() {
  const products = [
    { id: 1, name: "Shoes", category: "Clothing", price: 500 },
    { id: 2, name: "Laptop", category: "Electronics", price: 12000 },
    { id: 3, name: "Book", category: "Education", price: 200 },
    { id: 4, name: "Headphones", category: "Electronics", price: 1500 },
    { id: 5, name: "T-shirt", category: "Clothing", price: 250 },
  ];

  return (
    <div style={{ padding: "20px", fontFamily: "Arial" }}>
      <h1>Product Catalogue</h1>
      <ul>
        {products.map(p => (
          <li key={p.id}>
            <Link href={`/products/${p.id}`}>
              {p.name} - {p.category} - R{p.price}
            </Link>
          </li>
        ))}
      </ul>
    </div>
  );
}
