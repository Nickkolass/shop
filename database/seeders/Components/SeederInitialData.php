<?php

namespace Database\Seeders\Components;

class SeederInitialData
{
    /** @return  array<array<string>> */
    public static function getCategories(): array
    {
        $res = [
            ['title' => 'chokolate', 'title_rus' => 'Шоколад ручной работы'],
            ['title' => 'candle', 'title_rus' => 'Свечи ручной работы'],
            ['title' => 'soap', 'title_rus' => 'Мыло ручной работы'],
            ['title' => 'aroma', 'title_rus' => 'Ароматы для автомобиля'],
            ['title' => 'aromahome', 'title_rus' => 'Ароматы для дома'],
        ];
        return app()->environment() === 'testing' ? array_slice($res, 2) : $res;
    }

    /** @return array<array<string>> */
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
