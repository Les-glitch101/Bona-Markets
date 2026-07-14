/*
  Warnings:

  - A unique constraint covering the columns `[name]` on the table `Vendors` will be added. If there are existing duplicate values, this will fail.

*/
-- CreateIndex
CREATE UNIQUE INDEX "Vendors_name_key" ON "Vendors"("name");
