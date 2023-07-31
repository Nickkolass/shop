<?php

namespace Database\Seeders\Components;

class SeederInitialData
{
    public static function getCategories(): array
    {
        return [
            ['title' => 'chokolate', 'title_rus' => 'Шоколад ручной работы'],
            ['title' => 'candle', 'title_rus' => 'Свечи ручной работы'],
            ['title' => 'soap', 'title_rus' => 'Мыло ручной работы'],
            ['title' => 'aroma', 'title_rus' => 'Ароматы для автомобиля'],
            ['title' => 'aromahome', 'title_rus' => 'Ароматы для дома'],
        ];
    }

    public static function getOptions(): array
    {
        return [
            ['title' => 'Цвет'],
            ['title' => 'Объем'],
            ['title' => 'Аромат'],
            ['title' => 'Размер'],
            ['title' => 'Материал'],
            ['title' => 'Упаковка'],
            ['title' => 'Габариты'],
        ];
    }
}
