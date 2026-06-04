# Authentication Plan

## Pages (Week 2)
- /login.php
- /register.php
- /logout.php

## Session Variables
- $_SESSION['user_id']
- $_SESSION['role']
- $_SESSION['email']

## Role-Based Access
- Admin → /admin/*
- Vendor → /vendor/*
- Buyer → default

## Functions Needed (Week 2)
- requireLogin()
- requireVendor()
- requireAdmin()
