<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = config('menu.sidebar');

        if (!is_array($menus)) {
            throw new \Exception('menu.sidebar config missing or invalid');
        }

        foreach ($menus as $index => $menu) {

           

            $parent = Menu::updateOrCreate(
                [
                    // UNIQUE CONDITION
                    'key'   => $menu['key'] ?? null,
                    'title' => $menu['title'],
                    'parent_id' => null,
                ],
                [
                    // DATA TO UPDATE
                    'icon'  => $menu['icon'] ?? null,
                    'route' => $menu['route'] ?? null,
                    'type'  => $menu['type'],
                    'order' => $index,
                ]
            );

            /* ===============================
             |  CHILD MENUS (DROPDOWN)
             |===============================*/

            if (
                $menu['type'] === 'dropdown'
                && isset($menu['children'])
                && is_array($menu['children'])
            ) {
                foreach ($menu['children'] as $i => $child) {

                    Menu::updateOrCreate(
                        [
                            // UNIQUE CONDITION
                            'parent_id' => $parent->id,
                            'title'     => $child['title'],
                        ],
                        [
                            // DATA TO UPDATE
                            'route' => $child['route'] ?? null,
                            'type'  => 'single',
                            'order' => $i,
                        ]
                    );
                }
            }
        }
    }
}
