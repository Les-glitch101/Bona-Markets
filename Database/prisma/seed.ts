//I coded the base of this while following the prisma seeding tutorial found at: https://www.prisma.io/docs/orm/prisma-migrate/workflows/seeding. I changed some things, and added others through resaerch.

import "dotenv/config";
import { Pool } from "pg";
import { PrismaPg } from "@prisma/adapter-pg";
import { PrismaClient } from "../app/generated/prisma/client"; //the errors this gave me just to import....jeez.

//connections, unfortunately neccessary. and unneccessarily complicated.
const connectionString = `${process.env.DATABASE_URL}`;
const pool = new Pool({ connectionString });
const adapter = new PrismaPg(pool)
const prisma = new PrismaClient({ adapter });

//copy and paste rig so its easier than retyping each time:
//  const user_example = await prisma.users.upsert({
//        where: { uniquevariable: "" },
//        update: [],
//        create: {}
//            email:{} 
//    })

async function main() {
 // user example data, not sure how to example relations 
    const user_example = await prisma.users.upsert({
       where: { email: "gabbi@email.com" },
       update: {},
       create: {
           email: "gabbi@email.com",
           username: "Gabbi0304",
           password: "password1203040506!"
           
       } 
   })

   const vendor_example = await prisma.vendors.upsert({
       where: { name: "Nike Clothing Store"},
       update: {},
       create: {
        name: "Nike Clothing Store",
       }  
   })
   
   const product_example_1  = await prisma.products.upsert({
       where: { id: 1 },
       update: {},
       create: {
        name: "Air Force 1 - 2026",
        price: 1333,
        vendorId: vendor_example.id
       }
   })

   const product_example_2  = await prisma.products.upsert({
       where: { id: 2 },
       update: {},
       create: {
        name: "Black JLO Hoodie",
        price: 1234,
        vendorId: vendor_example.id

       }
   })

   const orders_example  = await prisma.orders.upsert({
       where: { id: 1 },
       update: {},
       create: {
        userId: user_example.id,
        totalPrice: product_example_1.price + product_example_2.price
       }
   })

    const orderitems_example_1 = await prisma.orderItems.upsert({
       where: { id: 1 },
       update: {},
       create: {
         orderId: orders_example.id,
         productId: product_example_1.id,
         quantity: 1,
        
       }
   })
    
    const orderitems_example_2 = await prisma.orderItems.upsert({
       where: { id: 1 },
       update: {},
       create: {
         orderId: orders_example.id,
         productId: product_example_2.id,
         quantity: 1,
        
       }
   })

   const cart_example  = await prisma.cart.upsert({
       where: { id: 1 },
       update: {},
       create: {
        userId: user_example.id,
        totalPrice: product_example_1.price + product_example_2.price
       }
   })

  const cartitems_example_1 = await prisma.cartItems.upsert({
       where: { id: 1 },
       update: {},
       create: {
         cartId: cart_example.id,
         productId: product_example_1.id,
         quantity: 1,
       }
   })

   const cartitems_example_2 = await prisma.cartItems.upsert({
       where: { id: 1 },
       update: {},
       create: {
         cartId: cart_example.id,
         productId: product_example_2.id,
         quantity: 1,
       }
   })
   console.log([user_example, vendor_example, product_example_1, product_example_2, orders_example, orderitems_example_1, orderitems_example_2, cart_example, cartitems_example_1, cartitems_example_2])
}

main()
.then(async () => {
    await prisma.$connect();
    await pool.end();
})
.catch(async (e) => {
    console.error(e);
    await prisma.$disconnect();
    await pool.end();
    process.exit(1);
});


