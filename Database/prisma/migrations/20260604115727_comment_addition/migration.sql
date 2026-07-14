/*
  Warnings:

  - You are about to drop the column `totalPrice` on the `Cart` table. All the data in the column will be lost.
  - You are about to drop the column `totalPrice` on the `Orders` table. All the data in the column will be lost.

*/
-- AlterTable
ALTER TABLE "Cart" DROP COLUMN "totalPrice";

-- AlterTable
ALTER TABLE "Orders" DROP COLUMN "totalPrice";
