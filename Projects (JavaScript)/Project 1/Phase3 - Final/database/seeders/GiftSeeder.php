<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gift;

class GiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $gifts=[
            ['number' => 'K8-Y7-GG-67', 'discount' => 15],
            ['number' => 'HH-YU-5T-6U', 'discount' => 25],
            ['number' => 'F4-T5-WW-XX', 'discount' => 30],
            ['number' => 'NA-NA-67-I3', 'discount' => 50],
            ['number' => 'D3-Y8-W1-11', 'discount' => 75],
            ['number' => 'DU-T5-T5-V1', 'discount' => 5],
            ['number' => 'AA-BB-I9-10', 'discount' => 10]
        ];

        foreach ($gifts as $gift)
        {
            Gift::create($gift);
        }
    }
}