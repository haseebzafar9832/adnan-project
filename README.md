# BulkShare

Collaborative Sharing of Bulk Items Purchasing System. A Laravel web application for a Virtual University project: people pool quantities, the unit price drops on a discount ladder, and each member pays only their own share.

Supervisor: Umra Naeem · umra.naeem@vu.edu.pk

## Run it on a new laptop

Start MySQL in the XAMPP Control Panel. From this folder run `composer install`, then `php artisan migrate --seed` and `php artisan storage:link`. The `bulkshare` database is created during migration if it does not exist yet. With the project in `C:\xampp\htdocs\bulkshare` and Apache running, open http://localhost/bulkshare/public.

## Demo logins

Password for every account: `password`

| Role | Email |
| --- | --- |
| Admin | admin@bulkshare.com |
| Buyer | sara.malik@bulkshare.com |
| Buyer | bilal.hussain@bulkshare.com |
| Buyer | hina.qureshi@bulkshare.com |
| Buyer | usman.ali@bulkshare.com |

## What to show

- Home, product catalogue, and category filter. Search uses jQuery AJAX.
- Model Town rice pool: 24 bags pledged. Adding 1 more bag moves the live price from 5% to 10%.
- Campus earbuds batch: electronics products use their own ladder.
- F-8 detergent run: target reached, some members paid and some still pending.
- Family oil collective: fulfilled group with orders at different delivery stages.
- Semester paper pool: Usman's bank transfer is pending in Admin → Transactions.
- Hostel soap carton: closed group with a refunded payment.
- Group page chat is limited to members and refreshes with AJAX.
- Checkout accepts a simulated card, JazzCash, Easypaisa, or bank transfer. Card numbers are validated and not stored. Nothing is charged.

## Stack

HTML, CSS, JavaScript, Bootstrap 5, jQuery, AJAX, Laravel, MySQL.
