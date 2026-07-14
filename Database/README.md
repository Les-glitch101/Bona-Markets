

## Setup before you can use the databases/prisma. This is important.

# Requirements

Install Node.js, use the prebuilt, easy-to-install one:
[https://nodejs.org/](https://nodejs.org/en/download)

---

# Setup

1. Download the project ( You do this by downloading the repository zip file, and then moving the entire file called DATABASE to your desktop):

 - Open the folder in terminal (Command Prompt/CMD) by typing this:
  cd Desktop\Database

2. Install dependencies by typing this in that same terminal:

- npm install

3. Set up environment variables (ask me for more clarification on this, i can make an explanation video):

- Copy .env.example
- Rename it to .env
-Keep DATABASEURL the same as it is in the .env file.

---

# Database Setup

Run, in your terminal:

- npx prisma generate
- npx prisma migrate dev
- npx prisma db seed (this seeds the database with 1 set of temporary data)

---

# Run Project in terminal

- npm run dev

---

# ERD

See ERD DIAGRAM.png for database structure
