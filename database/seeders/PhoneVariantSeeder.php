<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PhoneVariantSeeder extends Seeder
{
    public function run(): void
    {
        $brandIds = DB::table('phone_brands')
            ->pluck('id', 'name')
            ->toArray();

        $variants = [
            // Apple
            ['name' => 'iPhone 15 Pro Max', 'phone_brand_id' => $brandIds['Apple']],
            ['name' => 'iPhone 15 Pro', 'phone_brand_id' => $brandIds['Apple']],
            ['name' => 'iPhone 15 Plus', 'phone_brand_id' => $brandIds['Apple']],
            ['name' => 'iPhone 15', 'phone_brand_id' => $brandIds['Apple']],
            ['name' => 'iPhone 14 Pro Max', 'phone_brand_id' => $brandIds['Apple']],
            ['name' => 'iPhone 14 Pro', 'phone_brand_id' => $brandIds['Apple']],
            ['name' => 'iPhone 14', 'phone_brand_id' => $brandIds['Apple']],
            ['name' => 'iPhone 13 Pro Max', 'phone_brand_id' => $brandIds['Apple']],
            ['name' => 'iPhone 13 Pro', 'phone_brand_id' => $brandIds['Apple']],
            ['name' => 'iPhone 13', 'phone_brand_id' => $brandIds['Apple']],
            ['name' => 'iPhone 12 Pro Max', 'phone_brand_id' => $brandIds['Apple']],
            ['name' => 'iPhone 12 Pro', 'phone_brand_id' => $brandIds['Apple']],
            ['name' => 'iPhone 12', 'phone_brand_id' => $brandIds['Apple']],
            ['name' => 'iPhone 11 Pro Max', 'phone_brand_id' => $brandIds['Apple']],
            ['name' => 'iPhone 11 Pro', 'phone_brand_id' => $brandIds['Apple']],
            ['name' => 'iPhone 11', 'phone_brand_id' => $brandIds['Apple']],

            // Asus
            ['name' => 'ROG Phone 8 Pro', 'phone_brand_id' => $brandIds['Asus']],
            ['name' => 'ROG Phone 8', 'phone_brand_id' => $brandIds['Asus']],
            ['name' => 'ROG Phone 7 Ultimate', 'phone_brand_id' => $brandIds['Asus']],
            ['name' => 'Zenfone 10', 'phone_brand_id' => $brandIds['Asus']],
            ['name' => 'Zenfone 9', 'phone_brand_id' => $brandIds['Asus']],

            // Google
            ['name' => 'Pixel 9 Pro', 'phone_brand_id' => $brandIds['Google']],
            ['name' => 'Pixel 9', 'phone_brand_id' => $brandIds['Google']],
            ['name' => 'Pixel 8 Pro', 'phone_brand_id' => $brandIds['Google']],
            ['name' => 'Pixel 8', 'phone_brand_id' => $brandIds['Google']],
            ['name' => 'Pixel 7a', 'phone_brand_id' => $brandIds['Google']],
            ['name' => 'Pixel 7 Pro', 'phone_brand_id' => $brandIds['Google']],
            ['name' => 'Pixel 7', 'phone_brand_id' => $brandIds['Google']],

            // Honor
            ['name' => 'Magic 6 Pro', 'phone_brand_id' => $brandIds['Honor']],
            ['name' => 'Magic 6', 'phone_brand_id' => $brandIds['Honor']],
            ['name' => 'Magic 5 Pro', 'phone_brand_id' => $brandIds['Honor']],
            ['name' => 'Magic V2', 'phone_brand_id' => $brandIds['Honor']],

            // Huawei
            ['name' => 'P60 Pro', 'phone_brand_id' => $brandIds['Huawei']],
            ['name' => 'Mate 60 Pro', 'phone_brand_id' => $brandIds['Huawei']],
            ['name' => 'Mate X5', 'phone_brand_id' => $brandIds['Huawei']],

            // Lenovo
            ['name' => 'Legion Y70', 'phone_brand_id' => $brandIds['Lenovo']],
            ['name' => 'Legion Y90', 'phone_brand_id' => $brandIds['Lenovo']],
            ['name' => 'K14', 'phone_brand_id' => $brandIds['Lenovo']],

            // Motorola
            ['name' => 'Edge 50 Pro', 'phone_brand_id' => $brandIds['Motorola']],
            ['name' => 'Edge 40 Pro', 'phone_brand_id' => $brandIds['Motorola']],
            ['name' => 'Razr 40 Ultra', 'phone_brand_id' => $brandIds['Motorola']],
            ['name' => 'Razr 40', 'phone_brand_id' => $brandIds['Motorola']],
            ['name' => 'Moto G84', 'phone_brand_id' => $brandIds['Motorola']],
            ['name' => 'Moto G54', 'phone_brand_id' => $brandIds['Motorola']],
            ['name' => 'Moto G23', 'phone_brand_id' => $brandIds['Motorola']],

            // Nokia
            ['name' => 'G42 5G', 'phone_brand_id' => $brandIds['Nokia']],
            ['name' => 'G22', 'phone_brand_id' => $brandIds['Nokia']],
            ['name' => 'X30 5G', 'phone_brand_id' => $brandIds['Nokia']],

            // Nothing
            ['name' => 'Phone (2)', 'phone_brand_id' => $brandIds['Nothing']],
            ['name' => 'Phone (2a)', 'phone_brand_id' => $brandIds['Nothing']],
            ['name' => 'Phone (1)', 'phone_brand_id' => $brandIds['Nothing']],

            // OnePlus
            ['name' => 'OnePlus 12', 'phone_brand_id' => $brandIds['OnePlus']],
            ['name' => 'OnePlus 12R', 'phone_brand_id' => $brandIds['OnePlus']],
            ['name' => 'OnePlus Nord CE 3', 'phone_brand_id' => $brandIds['OnePlus']],
            ['name' => 'OnePlus Open', 'phone_brand_id' => $brandIds['OnePlus']],
            ['name' => 'OnePlus Nord N30', 'phone_brand_id' => $brandIds['OnePlus']],
            ['name' => 'OnePlus Nord CE 3 Lite', 'phone_brand_id' => $brandIds['OnePlus']],

            // Oppo
            ['name' => 'Find X7 Ultra', 'phone_brand_id' => $brandIds['Oppo']],
            ['name' => 'Find X7', 'phone_brand_id' => $brandIds['Oppo']],
            ['name' => 'Find N3 Flip', 'phone_brand_id' => $brandIds['Oppo']],
            ['name' => 'Find N3', 'phone_brand_id' => $brandIds['Oppo']],
            ['name' => 'Reno 11 Pro', 'phone_brand_id' => $brandIds['Oppo']],
            ['name' => 'Reno 11', 'phone_brand_id' => $brandIds['Oppo']],
            ['name' => 'A79 5G', 'phone_brand_id' => $brandIds['Oppo']],

            // Realme
            ['name' => 'GT 5 Pro', 'phone_brand_id' => $brandIds['Realme']],
            ['name' => 'GT5', 'phone_brand_id' => $brandIds['Realme']],
            ['name' => '12 Pro+', 'phone_brand_id' => $brandIds['Realme']],
            ['name' => 'Realme 12 Pro', 'phone_brand_id' => $brandIds['Realme']],
            ['name' => 'Realme 12', 'phone_brand_id' => $brandIds['Realme']],
            ['name' => 'Realme C67', 'phone_brand_id' => $brandIds['Realme']],
            ['name' => 'Narzo 60 Pro', 'phone_brand_id' => $brandIds['Realme']],

            // Samsung
            ['name' => 'Galaxy S24 Ultra', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy S24+', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy S24', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy S23 Ultra', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy S23+', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy S23', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy S22 Ultra', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy S22+', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy S22', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy S21 Ultra', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy S21+', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy S21', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy Z Fold5', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy Z Flip5', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy A54', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy A34', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy A14', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy M34', 'phone_brand_id' => $brandIds['Samsung']],
            ['name' => 'Galaxy M14', 'phone_brand_id' => $brandIds['Samsung']],

            // Sony
            ['name' => 'Xperia 1 V', 'phone_brand_id' => $brandIds['Sony']],
            ['name' => 'Xperia 5 V', 'phone_brand_id' => $brandIds['Sony']],
            ['name' => 'Xperia 10 V', 'phone_brand_id' => $brandIds['Sony']],

            // TCL
            ['name' => '40 XE 5G', 'phone_brand_id' => $brandIds['TCL']],
            ['name' => '40 X 5G', 'phone_brand_id' => $brandIds['TCL']],
            ['name' => '40 SE', 'phone_brand_id' => $brandIds['TCL']],
            ['name' => '30 XE 5G', 'phone_brand_id' => $brandIds['TCL']],

            // Tecno
            ['name' => 'Phantom V Fold', 'phone_brand_id' => $brandIds['Tecno']],
            ['name' => 'Phantom X2 Pro', 'phone_brand_id' => $brandIds['Tecno']],
            ['name' => 'Camon 20 Premier', 'phone_brand_id' => $brandIds['Tecno']],
            ['name' => 'Pova 5 Pro', 'phone_brand_id' => $brandIds['Tecno']],

            // Vivo
            ['name' => 'X100 Pro', 'phone_brand_id' => $brandIds['vivo']],
            ['name' => 'X100', 'phone_brand_id' => $brandIds['vivo']],
            ['name' => 'V29 Pro', 'phone_brand_id' => $brandIds['vivo']],
            ['name' => 'V29', 'phone_brand_id' => $brandIds['vivo']],
            ['name' => 'iQOO 12 Pro', 'phone_brand_id' => $brandIds['vivo']],
            ['name' => 'iQOO 12', 'phone_brand_id' => $brandIds['vivo']],
            ['name' => 'iQOO Neo 9 Pro', 'phone_brand_id' => $brandIds['vivo']],
            ['name' => 'Y100', 'phone_brand_id' => $brandIds['vivo']],

            // Xiaomi
            ['name' => '14 Ultra', 'phone_brand_id' => $brandIds['Xiaomi']],
            ['name' => '14 Pro', 'phone_brand_id' => $brandIds['Xiaomi']],
            ['name' => '14', 'phone_brand_id' => $brandIds['Xiaomi']],
            ['name' => 'Redmi Note 13 Pro+', 'phone_brand_id' => $brandIds['Xiaomi']],
            ['name' => 'Redmi Note 13 Pro', 'phone_brand_id' => $brandIds['Xiaomi']],
            ['name' => 'Redmi Note 13', 'phone_brand_id' => $brandIds['Xiaomi']],
            ['name' => 'Redmi 13C', 'phone_brand_id' => $brandIds['Xiaomi']],
            ['name' => 'POCO X6 Pro', 'phone_brand_id' => $brandIds['Xiaomi']],
            ['name' => 'POCO X6', 'phone_brand_id' => $brandIds['Xiaomi']],
            ['name' => 'POCO M6 Pro', 'phone_brand_id' => $brandIds['Xiaomi']],

            // ZTE
            ['name' => 'Axon 50 Ultra', 'phone_brand_id' => $brandIds['ZTE']],
            ['name' => 'Axon 40 Ultra', 'phone_brand_id' => $brandIds['ZTE']],
            ['name' => 'Blade V40 5G', 'phone_brand_id' => $brandIds['ZTE']],
        ];

        $usedSlugs = [];

        collect($variants)
            ->map(function ($variant) use (&$usedSlugs) {
                $baseSlug = Str::slug($variant['name']);
                $slug = $baseSlug;
                $counter = 1;

                while (in_array($slug, $usedSlugs)) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $usedSlugs[] = $slug;

                return array_merge($variant, [
                    'id' => Str::lower((string) Str::ulid()),
                    'slug' => $slug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            })
            ->pipe(fn ($variants) => DB::table('phone_variants')->insert($variants->toArray()));
    }
}
