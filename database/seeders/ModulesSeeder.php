<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB;

class ModulesSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $modules = array(
      [
        'name' => 'Dashboard',
        'slug' => 'dashboard',
        'menu_position' => 1,
        'icon' => 'far fa-circle',
        'created_at'=> date('Y-m-d H:i:s'),
        'updated_at'=> date('Y-m-d H:i:s'),
      ],
      [
        'name' => 'Settings',
        'slug' => 'settings',
        'menu_position' => 2,
        'icon' => 'fas fa-cog',
        'created_at'=> date('Y-m-d H:i:s'),
        'updated_at'=> date('Y-m-d H:i:s'),
      ],
      [
        'name' => 'Pages',
        'slug' => 'pages',
        'menu_position' => 4,
        'icon' => 'fas fa-file',
        'created_at'=> date('Y-m-d H:i:s'),
        'updated_at'=> date('Y-m-d H:i:s'),
      ],
      [
        'name' => 'Users',
        'slug' => 'users',
        'menu_position' => 5,
        'icon' => 'fas fa-user',
        'created_at'=> date('Y-m-d H:i:s'),
        'updated_at'=> date('Y-m-d H:i:s'),
      ],
      [
        'name' => 'Careers',
        'slug' => 'careers',
        'menu_position' => 7,
        'icon' => 'fas fa-users',
        'created_at'=> date('Y-m-d H:i:s'),
        'updated_at'=> date('Y-m-d H:i:s'),
      ],
      [
        'name' => 'Teams',
        'slug' => 'teams',
        'menu_position' => 8,
        'icon' => 'fas fa-user-friends',
        'created_at'=> date('Y-m-d H:i:s'),
        'updated_at'=> date('Y-m-d H:i:s'),
      ],
      [
        'name' => 'User Roles',
        'slug' => 'user_roles',
        'menu_position' => 6,
        'icon' => 'fas fa-dice',
        'created_at'=> date('Y-m-d H:i:s'),
        'updated_at'=> date('Y-m-d H:i:s'),
      ],
      [
        'name' => 'Forms',
        'slug' => 'forms',
        'menu_position' => 9,
        'icon' => 'fab fa-wpforms',
        'created_at'=> date('Y-m-d H:i:s'),
        'updated_at'=> date('Y-m-d H:i:s'),
      ],
      [
        'name' => 'Media',
        'slug' => 'media',
        'menu_position' => 3,
        'icon' => 'fas fa-photo-video',
        'created_at'=> date('Y-m-d H:i:s'),
        'updated_at'=> date('Y-m-d H:i:s'),
      ],
      [
        'name' => 'Menu',
        'slug' => 'menu',
        'menu_position' => 10,
        'icon' => 'fas fa-bars',
        'created_at'=> date('Y-m-d H:i:s'),
        'updated_at'=> date('Y-m-d H:i:s'),
      ],
    );
    DB::table('modules')->insert($modules);
  }
}
