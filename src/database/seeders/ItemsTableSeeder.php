<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userIds = \App\Models\User::pluck('id')->toArray();
        $items = [
        [
            'user_id'     => 1,
            'name'        => '腕時計',
            'price'       => 15000,
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'condition'   => '1',
            'img_url'     => 'item_images/image1.jpg',
            'brand_name'  => 'Rolax'
        ],
        [
            'user_id'     => 1,
            'name'        => 'HDD',
            'price'       => 5000,
            'description' => '高速で信頼性の高いハードディスク',
            'condition'   => '2',
            'img_url'     => 'item_images/image2.jpg',
            'brand_name'  => '西芝'
        ],
        [
            'user_id'     => 1,
            'name'        => '玉ねぎ3束',
            'price'       => 300,
            'description' => '新鮮な玉ねぎ3束のセット',
            'condition'   => '3',
            'img_url'     => 'item_images/image3.jpg',
            'brand_name'  => 'なし'
        ],
        [
            'user_id'     => 1,
            'name'        => '革靴',
            'price'       => 4000,
            'description' => 'クラシックなデザインの革靴',
            'condition'   => '4',
            'img_url'     => 'item_images/image4.jpg',
            'brand_name'  => ''
        ],
        [
            'user_id'     => 1,
            'name'        => 'ノートPC',
            'price'       => 45000,
            'description' => '高性能なノートパソコン',
            'condition'   => '1',
            'img_url'     => 'item_images/image5.jpg',
            'brand_name'  => ''
        ],
        [
            'user_id'     => 1,
            'name'        => 'マイク',
            'price'       => 8000,
            'description' => '高音質のレコーディング用マイク',
            'condition'   => '2',
            'img_url'     => 'item_images/image6.jpg',
            'brand_name'  => 'なし'
        ],
        [
            'user_id'     => 1,
            'name'        => 'ショルダーバッグ',
            'price'       => 3500,
            'description' => 'おしゃれなショルダーバッグ',
            'condition'   => '3',
            'img_url'     => 'item_images/image7.jpg',
            'brand_name'  => ''
        ],
        [
            'user_id'     => 1,
            'name'        => 'タンブラー',
            'price'       => 500,
            'description' => '使いやすいタンブラー',
            'condition'   => '4',
            'img_url'     => 'item_images/image8.jpg',
            'brand_name'  => 'なし'
        ],
        [
            'user_id'     => 1,
            'name'        => 'コーヒーミル',
            'price'       => 4000,
            'description' => '手動のコーヒーミル',
            'condition'   => '1',
            'img_url'     => 'item_images/image9.jpg',
            'brand_name'  => 'Starbacks'
        ],
        [
            'user_id'     => 1,
            'name'        => 'メイクセット',
            'price'       => 2500,
            'description' => '便利なメイクアップセット',
            'condition'   => '2',
            'img_url'     => 'item_images/image10.jpg',
            'brand_name'  => ''
        ],
        ];

        foreach ($items as $item){
            $item['user_id'] = $userIds[array_rand($userIds)];
            Item::create($item);
        }
    }
}
