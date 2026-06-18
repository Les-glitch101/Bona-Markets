"use client";

import { useState } from "react";

const vendorsData = [
  { id: 1, business_name: "Zulu Crafts", email: "zulu@example.com", description: "Handmade traditional crafts from KZN.", status: "PENDING", applied_date: "2025-06-10" },
  { id: 2, business_name: "Cape Spice Co.", email: "cape@example.com", description: "Authentic Cape Malay spice blends.", status: "PENDING", applied_date: "2025-06-12" },
  { id: 3, business_name: "Joburg Tech Store", email: "jts@example.com", description: "Electronics and accessories.", status: "APPROVED", applied_date: "2025-06-01" },
  { id: 4, business_name: "Soweto Threads", email: "soweto@example.com", description: "Urban fashion and streetwear.", status: "REJECTED", applied_date: "2025-06-05" },
];

export default function AdminVendorsPage() {
  const [vendors, setVendors] = useState(vendorsData);
  const pending = vendors.filter((v) => v.status === "PENDING");
  const reviewed = vendors.filter((v) => v.status !== "PENDING");

  function handleAction(id: number, action: "approve" | "reject") {
    setVendors(vendors.map((v) => v.id === id ? { ...v, status: action === "approve" ? "APPROVED" : "REJECTED" } : v));
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="mb-8"><h1 className="text-2xl font-bold text-gray-800">Vendor Applications</h1><p className="text-gray-500 text-sm mt-1">{pending.length} pending | {reviewed.length} reviewed</p></div>
      <section className="mb-10">
        <h2 className="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-4">Pending Review ({pending.length})</h2>
        {pending.length === 0 ? <div className="bg-white border border-gray-200 rounded-xl p-10 text-center text-gray-400 text-sm">No pending applications.</div> : (
          <div className="space-y-3">{pending.map((v) => (
            <div key={v.id} className="bg-white border border-amber-200 rounded-xl p-5 flex flex-col md:flex-row md:items-center gap-4">
              <div className="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0"><span className="text-blue-600 font-bold text-lg">{v.business_name[0]}</span></div>
              <div className="flex-1"><p className="font-semibold text-gray-800">{v.business_name}</p><p className="text-sm text-gray-500">{v.email}</p><p className="text-sm text-gray-400 mt-1">{v.description}</p></div>
              <div className="flex gap-2">
                <button onClick={() => handleAction(v.id, "approve")} className="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">Approve</button>
                <button onClick={() => handleAction(v.id, "reject")} className="px-4 py-2 border border-red-300 text-red-600 text-sm rounded-lg hover:bg-red-50">Reject</button>
              </div>
            </div>
          ))}</div>
        )}
      </section>
      <section>
        <h2 className="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-4">Reviewed ({reviewed.length})</h2>
        <div className="bg-white border border-gray-200 rounded-xl overflow-hidden">
          <table className="w-full text-sm">
            <thead><tr className="bg-gray-50 border-b border-gray-100"><th className="text-left px-5 py-3 font-medium text-gray-600">Business</th><th className="text-left px-5 py-3 font-medium text-gray-600">Email</th><th className="text-left px-5 py-3 font-medium text-gray-600">Applied</th><th className="text-left px-5 py-3 font-medium text-gray-600">Status</th></tr></thead>
            <tbody className="divide-y divide-gray-100">{reviewed.map((v) => (
              <tr key={v.id} className="hover:bg-gray-50">
                <td className="px-5 py-3 font-medium text-gray-800">{v.business_name}</td>
                <td className="px-5 py-3 text-gray-500">{v.email}</td>
                <td className="px-5 py-3 text-gray-500">{v.applied_date}</td>
                <td className="px-5 py-3"><span className={`text-xs px-2 py-0.5 rounded-full font-medium ${v.status === "APPROVED" ? "bg-green-100 text-green-700" : "bg-red-100 text-red-700"}`}>{v.status.toLowerCase()}</span></td>
              </tr>
            ))}</tbody>
          </table>
        </div>
      </section>
    </div>
  );
}
