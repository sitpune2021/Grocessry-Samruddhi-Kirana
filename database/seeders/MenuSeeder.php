<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('menu.sidebar') as $index => $menu) {

            $parent = Menu::create([
                'title' => $menu['title'],
                'icon'  => $menu['icon'] ?? null,
                'route' => $menu['route'] ?? null,
                'type'  => $menu['type'],
                'key'   => $menu['key'] ?? null,
                'order' => $index,
            ]);

            if ($menu['type'] === 'dropdown' && !empty($menu['children'])) {
                foreach ($menu['children'] as $i => $child) {
                    Menu::create([
                        'title'     => $child['title'],
                        'route'     => $child['route'],
                        'type'      => 'single',
                        'parent_id' => $parent->id,
                        'order'     => $i,
                    ]);
                }
            }
        }
    }
}
