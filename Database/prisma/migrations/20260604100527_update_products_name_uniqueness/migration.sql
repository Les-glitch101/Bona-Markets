/*
  Warnings:

  - A unique constraint covering the columns `[name,vendorId]` on the table `Products` will be added. If there are existing duplicate values, this will fail.

*/
-- CreateIndex
CREATE UNIQUE INDEX "Products_name_vendorId_key" ON "Products"("name", "vendorId");
