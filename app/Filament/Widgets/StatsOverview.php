<?php

namespace App\Filament\Widgets;

use App\Models\Tag;
use App\Models\Post;
use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Categorias', Category::count()),
            Card::make('Postagens', Post::count()),
            Card::make('Tags', Tag::count()),
        ];
    }
}
