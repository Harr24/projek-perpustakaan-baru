<?php

namespace Database\Seeders;
use App\Models\Major;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run(): void
{
    Major::create(['name' => 'RPL 1']);
    Major::create(['name' => 'RPL 2']);
    Major::create(['name' => 'DKV 1']);
    Major::create(['name' => 'TKJ 1']);
}
}
