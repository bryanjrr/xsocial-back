<?php

namespace Database\Seeders;

use App\Models\AccountDetail;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()
            ->count(10)
            ->has(
                AccountDetail::factory()->count(1),
                'accountDetail'
            )
            ->create();
    }
}
