

## Setup before you can use the databases/prisma. this is important.

# Requirements

Install Node.js, use the ready coded one:
https://nodejs.org/

---

## Setup

1. Download the project (the entire file called DATABASE):

 - Open the folder in terminal by typing this:
  cd Database

2. Install dependencies by typing this in terminal:

npm install

3. Set up environment variables (ask me for more clarification on this, i can make an explanation video):

- Copy .env.example
- Rename it to .env
- Update DATABASE_URL

Example:
DATABASE_URL="postgresql://postgres:password@localhost:5432/postgres"

---

## Database Setup

Run, in your terminal:

npx prisma generate
npx prisma migrate dev
npx prisma db seed

---

## Run Project in terminal

npm run dev

---

## ERD

See ERD DIAGRAM.png for database structure
