<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            // Weapons
            [
                'name' => 'Pedang Panjang Baja',
                'category' => 'weapon',
                'effect_type' => 'damage',
                'effect_value' => 8,
                'description' => 'Pedang tempaan pandai besi kerajaan, memberikan tebasan berbobot tinggi.',
                'icon' => 'fa-sword',
                'is_default' => true,
            ],
            [
                'name' => 'Tongkat Kayu Oak Bertuah',
                'category' => 'weapon',
                'effect_type' => 'damage',
                'effect_value' => 6,
                'description' => 'Fokus sihir bertatahkan kristal biru di ujungnya, memperkuat sihir elemen.',
                'icon' => 'fa-wand-magic-sparkles',
                'is_default' => true,
            ],
            [
                'name' => 'Busur Panjang Kayu Yew',
                'category' => 'weapon',
                'effect_type' => 'damage',
                'effect_value' => 7,
                'description' => 'Busur fleksibel dengan jangkauan tembak yang akurat dan tarikan mantap.',
                'icon' => 'fa-bow-arrow',
                'is_default' => true,
            ],
            [
                'name' => 'Sepasang Belati Baja Hitam',
                'category' => 'weapon',
                'effect_type' => 'damage',
                'effect_value' => 6,
                'description' => 'Belati tajam tanpa pantulan cahaya, sempurna untuk serangan mendadak (sneak attack).',
                'icon' => 'fa-dagger',
                'is_default' => true,
            ],
            [
                'name' => 'Gada Suci Fajar',
                'category' => 'weapon',
                'effect_type' => 'damage',
                'effect_value' => 7,
                'description' => 'Senjata tumpul suci bermotif matahari fajar yang mampu meremukkan tulang iblis.',
                'icon' => 'fa-hammer',
                'is_default' => true,
            ],

            // Armor
            [
                'name' => 'Perisai Besi Tempa',
                'category' => 'armor',
                'effect_type' => 'buff_ac',
                'effect_value' => 2,
                'description' => 'Perisai bundar kokoh penahan hantaman panah dan sabetan pedang.',
                'icon' => 'fa-shield-halved',
                'is_default' => true,
            ],
            [
                'name' => 'Jubah Pelindung Arcane',
                'category' => 'armor',
                'effect_type' => 'buff_ac',
                'effect_value' => 1,
                'description' => 'Jubah sutra tenunan penyihir yang dilapisi mantra peredam hawa dingin dan api.',
                'icon' => 'fa-vest',
                'is_default' => true,
            ],
            [
                'name' => 'Armor Kulit Rusa',
                'category' => 'armor',
                'effect_type' => 'buff_ac',
                'effect_value' => 2,
                'description' => 'Pelindung tubuh ringan dari kulit samak yang tidak menimbulkan suara derap langkah.',
                'icon' => 'fa-shirt',
                'is_default' => true,
            ],
            [
                'name' => 'Baju Zirah Rantai Suci',
                'category' => 'armor',
                'effect_type' => 'buff_ac',
                'effect_value' => 3,
                'description' => 'Anyaman cincin baja tebal yang didoakan oleh para pendeta kuil agung.',
                'icon' => 'fa-shield',
                'is_default' => true,
            ],

            // Potions
            [
                'name' => 'Ramuan Penyembuh (Health Potion)',
                'category' => 'potion',
                'effect_type' => 'heal',
                'effect_value' => 15,
                'description' => 'Cairan merah berkilau harum herbal yang memulihkan 15 poin HP saat diminum.',
                'icon' => 'fa-flask-round-potion',
                'is_default' => true,
            ],
            [
                'name' => 'Ramuan Penyembuh Berkat (Holy Potion)',
                'category' => 'potion',
                'effect_type' => 'heal',
                'effect_value' => 25,
                'description' => 'Ekstrak mawar suci dan air telaga suci yang mengembalikan 25 HP seketika.',
                'icon' => 'fa-flask-round-potion',
                'is_default' => true,
            ],
            [
                'name' => 'Elixir Keberuntungan Dadu',
                'category' => 'potion',
                'effect_type' => 'buff',
                'effect_value' => 5,
                'description' => 'Cairan keemasan berbuih yang memberi inspirasi dan ketenangan pikiran.',
                'icon' => 'fa-flask',
                'is_default' => false,
            ],

            // Equipment & Quest Items
            [
                'name' => 'Obor Api Abadi',
                'category' => 'equipment',
                'effect_type' => 'utility',
                'effect_value' => null,
                'description' => 'Obor kayu berlumur damar sihir yang menerangi kegelapan katakomba tanpa padam.',
                'icon' => 'fa-fire-flame-curved',
                'is_default' => true,
            ],
            [
                'name' => 'Tali Sutra 50 Kaki & Pengait Besi',
                'category' => 'equipment',
                'effect_type' => 'utility',
                'effect_value' => null,
                'description' => 'Tali tipis namun sangat kuat untuk memanjat tebing terjal dan menuruni jurang gua.',
                'icon' => 'fa-link',
                'is_default' => true,
            ],
            [
                'name' => 'Kunci Perak Kepala Gagak',
                'category' => 'quest',
                'effect_type' => 'key_item',
                'effect_value' => null,
                'description' => 'Kunci antik berukir burung gagak, konon membuka peti harta di ruang takhta Raja Terkutuk.',
                'icon' => 'fa-key',
                'is_default' => false,
            ],
        ];

        foreach ($items as $item) {
            Item::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                $item
            );
        }
    }
}
