<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PhoneBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            // A
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'Apple', 'slug' => 'apple'],
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'Asus', 'slug' => 'asus'],

            // G-H
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'Google', 'slug' => 'google'],
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'Honor', 'slug' => 'honor'],
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'Huawei', 'slug' => 'huawei'],

            // L-N
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'Lenovo', 'slug' => 'lenovo'],
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'Motorola', 'slug' => 'motorola'],
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'Nokia', 'slug' => 'nokia'],
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'Nothing', 'slug' => 'nothing'],

            // O
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'OnePlus', 'slug' => 'oneplus'],
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'Oppo', 'slug' => 'oppo'],

            // R-S
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'Realme', 'slug' => 'realme'],
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'Samsung', 'slug' => 'samsung'],
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'Sony', 'slug' => 'sony'],

            // T-Z
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'TCL', 'slug' => 'tcl'],
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'Tecno', 'slug' => 'tecno'],
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'vivo', 'slug' => 'vivo'],
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'Xiaomi', 'slug' => 'xiaomi'],
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'ZTE', 'slug' => 'zte'],

            // Other
            ['id' => Str::lower((string) Str::ulid()), 'name' => 'Other', 'slug' => 'other'],
        ];

        collect($brands)
            ->map(fn ($brand) => array_merge($brand, [
                'created_at' => now(),
                'updated_at' => now(),
            ]))
            ->pipe(fn ($brands) => DB::table('phone_brands')->insert($brands->toArray()));
    }
}
