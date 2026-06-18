import { PrismaClient } from "@prisma/client";

const prisma = new PrismaClient();

async function main() {
  const user = await prisma.users.create({
    data: {
      username: "Gabbi0304",
      email: "gabbi@email.com",
      password: "password1203040506!",
    },
  });

  const vendor = await prisma.vendors.create({
    data: { name: "Nike Clothing Store" },
  });

  const product1 = await prisma.products.create({
    data: { name: "Air Force 1 - 2026", price: 1333, vendorId: vendor.id },
  });

  const product2 = await prisma.products.create({
    data: { name: "Black JLO Hoodie", price: 1234, vendorId: vendor.id },
  });

  const order = await prisma.orders.create({
    data: { userId: user.id, totalPrice: 2567 },
  });

  await prisma.orderItems.createMany({
    data: [
      { orderId: order.id, productId: product1.id, quantity: 1 },
      { orderId: order.id, productId: product2.id, quantity: 1 },
    ],
  });

  const cart = await prisma.cart.create({
    data: { userId: user.id, totalPrice: 2567 },
  });

  await prisma.cartItems.createMany({
    data: [
      { cartId: cart.id, productId: product1.id, quantity: 1 },
      { cartId: cart.id, productId: product2.id, quantity: 1 },
    ],
  });

  console.log("Seed data inserted successfully");
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
