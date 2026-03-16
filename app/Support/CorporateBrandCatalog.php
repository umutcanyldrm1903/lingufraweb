<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CorporateBrandCatalog
{
    public static function merge(Collection $brands, bool $absoluteAssetUrls = false): Collection
    {
        $normalizedBrands = $brands->map(function ($brand) {
            return (object) [
                'name' => (string) data_get($brand, 'name', ''),
                'image' => data_get($brand, 'image'),
                'url' => (string) data_get($brand, 'url', ''),
            ];
        });

        $existingNames = $normalizedBrands
            ->map(fn ($brand) => self::normalizeName($brand->name))
            ->filter()
            ->all();

        $supplementalBrands = collect(self::catalog($absoluteAssetUrls))
            ->map(fn (array $brand) => (object) $brand)
            ->reject(fn ($brand) => in_array(self::normalizeName($brand->name), $existingNames, true));

        return $normalizedBrands->concat($supplementalBrands)->values();
    }

    /**
     * @return array<int, array{name: string, image: string, url: string}>
     */
    private static function catalog(bool $absoluteAssetUrls): array
    {
        $assetPath = static fn (string $path): string => $absoluteAssetUrls ? asset($path) : $path;

        return [
            [
                'name' => 'CNN Turk',
                'image' => $assetPath('frontend/img/brand-logos/cnn-turk.svg'),
                'url' => 'https://www.cnnturk.com/',
            ],
            [
                'name' => 'Beyaz TV',
                'image' => $assetPath('frontend/img/brand-logos/beyaz-tv.svg'),
                'url' => 'https://www.beyaztv.com.tr/',
            ],
            [
                'name' => 'TV8',
                'image' => $assetPath('frontend/img/brand-logos/tv8.svg'),
                'url' => 'https://www.tv8.com.tr/',
            ],
        ];
    }

    private static function normalizeName(string $name): string
    {
        return Str::of($name)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/', '')
            ->value();
    }
}
