<?php

namespace Database\Seeders;

use App\Models\BulkGroup;
use App\Models\Category;
use App\Models\DiscountTier;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\GroupService;
use Illuminate\Database\Seeder;

class BulkShareSeeder extends Seeder
{
    public function run(): void
    {
        $groups = app(GroupService::class);

        $admin = $this->user('System Admin', 'admin@bulkshare.com', '03001112233', 'Lahore', 'VU Project Office, Lahore', 'admin');
        $sara = $this->user('Sara Malik', 'sara.malik@bulkshare.com', '03018451220', 'Lahore', 'House 18, Block C, Model Town, Lahore');
        $bilal = $this->user('Bilal Hussain', 'bilal.hussain@bulkshare.com', '03219887711', 'Karachi', 'Flat 4B, Gulshan-e-Iqbal, Karachi');
        $hina = $this->user('Hina Qureshi', 'hina.qureshi@bulkshare.com', '03335550918', 'Islamabad', 'Street 12, F-8/3, Islamabad');
        $usman = $this->user('Usman Ali', 'usman.ali@bulkshare.com', '03007654321', 'Faisalabad', '142 D-Ground, Faisalabad');

        $categories = [
            'groceries' => $this->category('Groceries', 'groceries', 'basket3', 'Rice, oil, flour, and pantry staples bought by the carton.'),
            'electronics' => $this->category('Electronics', 'electronics', 'phone', 'Small electronics that suppliers discount only in larger lots.'),
            'household' => $this->category('Household', 'household', 'house', 'Cleaning and home supplies for families sharing one delivery.'),
            'personal-care' => $this->category('Personal Care', 'personal-care', 'droplet', 'Soap, shampoo, and everyday care products.'),
            'office' => $this->category('Office & Stationery', 'office', 'pencil-square', 'Paper, pens, and supplies for offices and study groups.'),
        ];

        $catalog = [
            ['groceries', 'Super Basmati Rice 5kg', 'super-basmati-rice-5kg', 'Aged basmati in a 5kg family bag.', 'One household rarely needs enough rice to reach a wholesale slab. A street, hostel, or office can. The bag is sealed, long-grain, and priced here at the single-buyer rate.', 'bag', 2200],
            ['groceries', 'Sunflower Cooking Oil 5L', 'sunflower-cooking-oil-5l', 'Five-litre tin of refined sunflower oil.', 'Retail tins stay expensive until a distributor sees a full case order. Members each take the tins they need and pay only that share.', 'tin', 2850],
            ['groceries', 'White Sugar 1kg', 'white-sugar-1kg', 'One kilogram pack of refined sugar.', 'A simple staple. The discount appears once the group crosses the first quantity tier.', 'pack', 180],
            ['groceries', 'Wheat Flour 10kg', 'wheat-flour-10kg', 'Fine wheat flour in a 10kg sack.', 'Useful for households that share a monthly pantry order and want the mill rate instead of the shop rate.', 'sack', 1450],
            ['groceries', 'Ceylon Tea 900g', 'ceylon-tea-900g', 'Loose Ceylon tea in a 900g pack.', 'Tea is a natural group buy: the carton price is lower, and each member still chooses how many packs to take.', 'pack', 1650],
            ['electronics', 'Wireless Earbuds', 'wireless-earbuds', 'In-ear earbuds with a charging case.', 'Electronics suppliers open a better slab once a group commits to a small batch. This product uses its own discount ladder.', 'piece', 4500],
            ['electronics', 'LED Bulb 12W, pack of 4', 'led-bulb-12w-pack', 'Four warm-white 12W LED bulbs.', 'A practical group buy for a new flat or a small office refit.', 'pack', 1200],
            ['electronics', 'Power Bank 20000mAh', 'power-bank-20000mah', '20000mAh power bank with dual output.', 'Bought together, the unit price moves down the electronics ladder instead of the general grocery ladder.', 'piece', 3800],
            ['household', 'Dishwash Liquid 1L', 'dishwash-liquid-1l', 'One-litre lemon dishwash liquid.', 'Light, easy to split, and quick to reach a modest bulk tier.', 'bottle', 420],
            ['household', 'Laundry Detergent 3kg', 'laundry-detergent-3kg', 'Three-kilogram bag of laundry powder.', 'A heavy staple. Once the target is met, every unpaid member still sees the same live unit price.', 'bag', 980],
            ['household', 'Facial Tissue Carton', 'facial-tissue-carton', 'Carton of facial tissue boxes.', 'Useful for a hostel floor or a clinic reception that wants the carton price.', 'carton', 750],
            ['personal-care', 'Herbal Shampoo 400ml', 'herbal-shampoo-400ml', '400ml herbal shampoo bottle.', 'Personal care still clears a bulk tier when enough people add a bottle each.', 'bottle', 890],
            ['personal-care', 'Bathing Soap, pack of 6', 'bathing-soap-pack-6', 'Six-bar family pack of bathing soap.', 'A small pack size makes it easy for students to join without overbuying.', 'pack', 540],
            ['office', 'A4 Paper, 500 sheets', 'a4-paper-ream', 'One ream of 80gsm A4 paper.', 'Offices and semester groups can pool a print run and unlock the stationery slab.', 'ream', 950],
            ['office', 'Ballpoint Pens, box of 50', 'ballpoint-pens-box-50', 'A box of 50 blue ballpoint pens.', 'Meant for a department or a class representative buying once for everyone.', 'box', 700],
        ];

        $products = [];
        foreach ($catalog as $row) {
            $products[$row[2]] = Product::query()->create([
                'category_id' => $categories[$row[0]]->id,
                'name' => $row[1],
                'slug' => $row[2],
                'summary' => $row[3],
                'description' => $row[4],
                'unit' => $row[5],
                'base_price' => $row[6],
                'is_active' => true,
            ]);
        }

        foreach ([
            ['Circle', 10, 5],
            ['Bulk', 25, 10],
            ['Wholesale', 50, 15],
            ['Supplier', 100, 22],
        ] as [$name, $min, $percent]) {
            DiscountTier::query()->create([
                'product_id' => null,
                'name' => $name,
                'min_quantity' => $min,
                'discount_percent' => $percent,
            ]);
        }

        foreach (['wireless-earbuds', 'led-bulb-12w-pack', 'power-bank-20000mah'] as $slug) {
            foreach ([
                ['Team', 5, 8],
                ['Department', 15, 15],
                ['Office-wide', 30, 24],
            ] as [$name, $min, $percent]) {
                DiscountTier::query()->create([
                    'product_id' => $products[$slug]->id,
                    'name' => $name,
                    'min_quantity' => $min,
                    'discount_percent' => $percent,
                ]);
            }
        }

        $rice = $this->openGroup($groups, $sara, $products['super-basmati-rice-5kg'], 'Model Town rice pool', 'A neighbourhood pool for the monthly rice run. One more bag unlocks the next discount.', 10, 40, 14);
        $this->join($groups, $bilal, $rice, 8);
        $this->join($groups, $hina, $rice, 6);

        $earbuds = $this->openGroup($groups, $bilal, $products['wireless-earbuds'], 'Campus earbuds batch', 'Students splitting a small electronics batch. The product has its own price ladder.', 2, 15, 9);
        $this->join($groups, $usman, $earbuds, 2);
        $this->join($groups, $sara, $earbuds, 1);

        $detergent = $this->openGroup($groups, $hina, $products['laundry-detergent-3kg'], 'F-8 detergent run', 'Target is the wholesale carton. Pay your share so the supplier can be booked.', 8, 25, 6);
        $this->join($groups, $sara, $detergent, 6);
        $this->join($groups, $usman, $detergent, 6);
        $this->join($groups, $bilal, $detergent, 5);
        $this->pay($groups, $hina, $detergent, 'jazzcash');
        $this->pay($groups, $usman, $detergent, 'easypaisa');

        $oil = $this->openGroup($groups, $sara, $products['sunflower-cooking-oil-5l'], 'Family oil collective', 'A completed group. Every member paid their own tin and the orders are in different delivery stages.', 5, 20, 5);
        $this->join($groups, $bilal, $oil, 5);
        $this->join($groups, $hina, $oil, 5);
        $this->join($groups, $usman, $oil, 5);
        $this->pay($groups, $sara, $oil, 'card');
        $this->pay($groups, $bilal, $oil, 'jazzcash');
        $this->pay($groups, $hina, $oil, 'easypaisa');
        $this->pay($groups, $usman, $oil, 'card');
        $oil->refresh();

        $this->markOrder($oil, $sara, 'delivered', 'Delivered in Model Town by TCS. Consignment 4412901.');
        $this->markOrder($oil, $bilal, 'shipped', 'Handed to Leopards courier. On the way to Gulshan.');
        $this->markOrder($oil, $hina, 'processing', 'Packed at the supplier warehouse in Lahore.');
        $this->markOrder($oil, $usman, 'confirmed', 'Payment confirmed. Waiting for the supplier dispatch slot.');

        $oil->forceFill([
            'deadline' => now()->subDays(2),
            'created_at' => now()->subDays(18),
        ])->save();

        $paper = $this->openGroup($groups, $usman, $products['a4-paper-ream'], 'Semester paper pool', 'A study group buying A4 reams. The organiser submitted a bank transfer that still needs confirmation.', 6, 30, 20);
        $this->pay($groups, $usman, $paper, 'bank', 'HBL-88231');

        $soap = $this->openGroup($groups, $hina, $products['bathing-soap-pack-6'], 'Hostel soap carton', 'A small group that missed its deadline. One payment was refunded after the supplier dropped the lot.', 4, 12, 3);
        $this->join($groups, $sara, $soap, 3);
        $this->pay($groups, $sara, $soap, 'jazzcash');
        $payment = Order::query()->where('user_id', $sara->id)->where('bulk_group_id', $soap->id)->firstOrFail()->payment;
        $groups->refund($payment);
        $soap->forceFill(['deadline' => now()->subDay()])->save();
        $groups->closeExpired();

        $this->say($rice, $sara, 'If one more bag comes in, we leave the 5% tier and open 10%.');
        $this->say($rice, $bilal, 'I can add another bag if the price actually moves. Paying only my own share.');
        $this->say($rice, $hina, 'I am in for six bags. I will pay once the next tier is visible.');
        $this->say($detergent, $hina, 'Target is complete. Please pay your own bags so the order can be placed.');
        $this->say($detergent, $usman, 'Paid on Easypaisa. The reference is on my order.');

        unset($admin);
    }

    private function user(string $name, string $email, string $phone, string $city, string $address, string $role = 'user'): User
    {
        return User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => 'password',
            'phone' => $phone,
            'city' => $city,
            'address' => $address,
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function category(string $name, string $slug, string $icon, string $description): Category
    {
        return Category::query()->create([
            'name' => $name,
            'slug' => $slug,
            'icon' => $icon,
            'description' => $description,
            'is_active' => true,
        ]);
    }

    private function openGroup(GroupService $groups, User $owner, Product $product, string $title, string $description, int $mine, int $target, int $days): BulkGroup
    {
        return $groups->create($owner, [
            'product_id' => $product->id,
            'title' => $title,
            'description' => $description,
            'my_quantity' => $mine,
            'target_quantity' => $target,
            'deadline' => now()->addDays($days),
        ]);
    }

    private function join(GroupService $groups, User $user, BulkGroup $group, int $quantity): void
    {
        $groups->join($user, $group, $quantity);
    }

    private function pay(GroupService $groups, User $user, BulkGroup $group, string $method, ?string $hint = null): void
    {
        $member = GroupMember::query()
            ->where('bulk_group_id', $group->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $groups->settlePayment($user, $member, [
            'method' => $method,
            'shipping_address' => $user->address,
            'city' => $user->city,
            'payer_hint' => $hint ?: $user->phone,
        ]);
    }

    private function markOrder(BulkGroup $group, User $user, string $status, string $note): void
    {
        Order::query()
            ->where('bulk_group_id', $group->id)
            ->where('user_id', $user->id)
            ->update([
                'status' => $status,
                'tracking_note' => $note,
            ]);
    }

    private function say(BulkGroup $group, User $user, string $body): void
    {
        GroupMessage::query()->create([
            'bulk_group_id' => $group->id,
            'user_id' => $user->id,
            'body' => $body,
        ]);
    }
}
