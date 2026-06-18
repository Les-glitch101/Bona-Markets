const orders = [
  { id: 1001, buyer_name: "Thabo Mokoena", buyer_email: "thabo@example.com", items: "Running Shoes, Water Bottle", item_count: 2, total: 849.0, status: "DELIVERED", date: "2025-06-14" },
  { id: 1002, buyer_name: "Lerato Dlamini", buyer_email: "lerato@example.com", items: "Handmade Beads Necklace", item_count: 1, total: 320.0, status: "PROCESSING", date: "2025-06-15" },
  { id: 1003, buyer_name: "Sipho Ndlovu", buyer_email: "sipho@example.com", items: "Cape Spice Pack, Chutney Set", item_count: 2, total: 215.5, status: "PENDING", date: "2025-06-16" },
  { id: 1004, buyer_name: "Ayanda Khumalo", buyer_email: "ayanda@example.com", items: "Wireless Earbuds", item_count: 1, total: 1199.0, status: "SHIPPED", date: "2025-06-13" },
];

const badgeColors: Record<string, string> = { DELIVERED: "bg-green-100 text-green-700", SHIPPED: "bg-blue-100 text-blue-700", PROCESSING: "bg-indigo-100 text-indigo-700", PENDING: "bg-amber-100 text-amber-700" };

export default function AdminOrdersPage() {
  return (
    <div className="container mx-auto px-4 py-8">
      <div className="mb-8"><h1 className="text-2xl font-bold text-gray-800">All Orders</h1><p className="text-gray-500 text-sm mt-1">{orders.length} orders</p></div>
      <div className="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table className="w-full text-sm">
          <thead><tr className="bg-gray-50 border-b border-gray-100"><th className="text-left px-5 py-3 font-medium text-gray-600">Order ID</th><th className="text-left px-5 py-3 font-medium text-gray-600">Buyer</th><th className="text-left px-5 py-3 font-medium text-gray-600">Items</th><th className="text-left px-5 py-3 font-medium text-gray-600">Total</th><th className="text-left px-5 py-3 font-medium text-gray-600">Status</th><th className="text-left px-5 py-3 font-medium text-gray-600">Date</th></tr></thead>
          <tbody className="divide-y divide-gray-100">{orders.map((o) => (
            <tr key={o.id} className="hover:bg-gray-50">
              <td className="px-5 py-3 font-mono text-gray-400 text-xs">#{String(o.id).padStart(6, "0")}</td>
              <td className="px-5 py-3"><p className="font-medium text-gray-800">{o.buyer_name}</p><p className="text-gray-400 text-xs">{o.buyer_email}</p></td>
              <td className="px-5 py-3 text-gray-500">{o.item_count} item{o.item_count !== 1 ? "s" : ""}</td>
              <td className="px-5 py-3 font-semibold text-gray-800">R{o.total.toFixed(2)}</td>
              <td className="px-5 py-3"><span className={`text-xs px-2 py-0.5 rounded-full font-medium ${badgeColors[o.status] || "bg-gray-100"}`}>{o.status.charAt(0) + o.status.slice(1).toLowerCase()}</span></td>
              <td className="px-5 py-3 text-gray-500">{new Date(o.date).toLocaleDateString("en-ZA", { day: "2-digit", month: "short", year: "numeric" })}</td>
            </tr>
          ))}</tbody>
        </table>
      </div>
    </div>
  );
}
