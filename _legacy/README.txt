FASHION CORNER — ADMIN WEBSITE
================================

START THE WEBSITE

1. Double-click: START FASHION CORNER.cmd
2. Your admin page opens at: http://127.0.0.1:3000/admin
3. The customer store is at: http://127.0.0.1:3000
4. Keep the black launcher window open while using the website.
5. Close that window, or press Ctrl+C inside it, to stop the website.


FIRST-TIME ADMIN LOGIN

The first time you open the admin page, you will create your own:

- Admin username
- Private password (at least 8 characters)

Your password is protected and is not stored as readable text.
There is no preset or shared password.


MANAGE PRODUCTS

From the Products page you can:

- Add, edit, search and delete products
- Upload up to 20 photos for each product
- Change the price and optional original price
- Select a category
- Show or hide a product in Hot Selling
- Show or hide a product in You May Also Like

The first uploaded photo is the main photo. All uploaded photos can be
swiped in the product popup on the customer website.


MANAGE CATEGORIES

Open Categories to create a new category.
A category can only be deleted when it contains no products.


BACKUP YOUR STORE

Open Backup & Security and select Download backup.
The downloaded JSON file contains:

- All products
- All categories
- All uploaded product photos

Keep this backup file in a safe place. You can restore it later from the
same admin page. Restoring a backup replaces the current catalogue but
does not replace your admin password.


IMPORTANT FOR PUTTING THE WEBSITE ONLINE

This package works immediately on this computer. For customers to visit
it on the internet, upload the complete folder to a hosting service that
supports Node.js and persistent file storage. Start it with:

  node server.js

The hosting service should provide a permanent storage folder so uploaded
photos and catalogue changes are retained. The admin page should be used
over HTTPS when hosted online.


FILES TO KEEP

Do not delete these:

- server.js
- package.json
- public folder
- data folder

Your uploaded pictures are stored in public/uploads.
Your catalogue is stored in data/products.json and data/categories.json.
