"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";

export default function LogoutPage() {
  const router = useRouter();
  useEffect(() => { router.push("/?logout=success"); }, [router]);
  return <div className="min-h-screen flex items-center justify-center"><p className="text-gray-500">Logging out...</p></div>;
}
