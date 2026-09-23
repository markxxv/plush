<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductCollection;
use App\Models\ProductSize;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class ImportProductsFromFeed extends Command
{
    protected $signature = 'products:import-feed
        {--base=https://maison-plush.com/wp-json/zm-catalog-feed/v1/products : Feed base URL}
        {--token=CHANGE_ME_LONG_RANDOM_TOKEN : Feed token}
        {--per-page=40 : Products per page}
        {--limit= : Import only N products}
        {--wp-id= : Import one product by source.wp_product_id_en or source.wp_product_id_fr}
        {--slug= : Import one product by slug_en or slug_fr}
        {--fresh-media : Clear product gallery before importing images}
        {--dry-run : Do not write anything}';

    protected $description = 'Import products from WooCommerce JSON feed into Laravel catalog.';

    public function handle(): int
    {
        $perPage = max(1, (int) $this->option('per-page'));
        $limit = $this->option('limit') ? max(1, (int) $this->option('limit')) : null;
        $wpId = $this->option('wp-id') ? (int) $this->option('wp-id') : null;
        $slug = $this->option('slug') ? trim((string) $this->option('slug')) : null;
        $dryRun = (bool) $this->option('dry-run');

        $page = 1;
        $imported = 0;
        $matched = 0;
        $bar = null;

        $this->info('Starting product import...');

        while (true) {
            $payload = $this->fetchPage($page, $perPage);

            $items = $payload['data'] ?? [];

            if (! is_array($items) || count($items) === 0) {
                break;
            }

            if ($bar === null) {
                $feedTotal = (int) data_get($payload, 'meta.total', count($items));

                $targetTotal = $wpId || $slug
                    ? 1
                    : ($limit ?: $feedTotal);

                $bar = $this->output->createProgressBar($targetTotal);
                $bar->start();
            }

            foreach ($items as $item) {
                if (! $this->matchesFilter($item, $wpId, $slug)) {
                    continue;
                }

                $matched++;

                if (! $dryRun) {
                    $this->importProduct($item);
                }

                $imported++;
                $bar?->advance();

                if ($wpId || $slug) {
                    break 2;
                }

                if ($limit !== null && $imported >= $limit) {
                    break 2;
                }
            }

            $totalPages = (int) data_get($payload, 'meta.total_pages', 0);

            if ($totalPages > 0 && $page >= $totalPages) {
                break;
            }

            if (count($items) < $perPage) {
                break;
            }

            $page++;
        }

        $bar?->finish();
        $this->newLine(2);

        if (($wpId || $slug) && $matched === 0) {
            $this->warn('Product not found in feed.');
            return self::FAILURE;
        }

        $this->info($dryRun
            ? "Dry run finished. Matched: {$imported}"
            : "Import finished. Imported/updated: {$imported}"
        );

        return self::SUCCESS;
    }

    private function fetchPage(int $page, int $perPage): array
    {
        $response = Http::retry(3, 500)
            ->timeout(90)
            ->get((string) $this->option('base'), [
                'token' => $this->option('token'),
                'page' => $page,
                'per_page' => $perPage,
            ]);

        if (! $response->successful()) {
            $this->error("Feed request failed. HTTP {$response->status()}");
            exit(self::FAILURE);
        }

        return $response->json() ?? [];
    }

    private function matchesFilter(array $item, ?int $wpId, ?string $slug): bool
    {
        if ($wpId) {
            return (int) data_get($item, 'source.wp_product_id_en') === $wpId
                || (int) data_get($item, 'source.wp_product_id_fr') === $wpId;
        }

        if ($slug) {
            return ($item['slug_en'] ?? null) === $slug
                || ($item['slug_fr'] ?? null) === $slug;
        }

        return true;
    }

    private function importProduct(array $item): Product
    {
        $slugEn = trim((string) ($item['slug_en'] ?? ''));

        if ($slugEn === '') {
            throw new \RuntimeException('Product skipped: slug_en is empty.');
        }

        $createdAt = $this->date($item['created_at'] ?? null);
        $updatedAt = $this->date($item['updated_at'] ?? null) ?? $createdAt;

        $product = Product::updateOrCreate(
            ['slug_en' => $slugEn],
            [
                'title_en' => $item['title_en'] ?? null,
                'title_fr' => $item['title_fr'] ?? null,

                'slug_fr' => $item['slug_fr'] ?? null,

                'description_en' => $item['description_en'] ?? null,
                'description_fr' => $item['description_fr'] ?? null,

                'meta_title_en' => $item['meta_title_en'] ?? null,
                'meta_title_fr' => $item['meta_title_fr'] ?? null,

                'meta_description_en' => $item['meta_description_en'] ?? null,
                'meta_description_fr' => $item['meta_description_fr'] ?? null,

                'price' => $this->price($item['price'] ?? null),
                'availability' => (bool) ($item['availability'] ?? true),

                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]
        );

        $this->syncSizes($product, $item['attributes'] ?? []);
        $this->syncCollections($product, $item['collections'] ?? []);
        $this->syncMedia($product, $item['images'] ?? []);

        return $product;
    }

    private function syncSizes(Product $product, array $attributes): void
    {
        $ids = [];

        foreach ($attributes as $attribute) {
            $slug = $attribute['slug'] ?? null;
            $name = strtolower((string) ($attribute['name_en'] ?? ''));

            if ($slug !== 'pa_size' && $name !== 'size') {
                continue;
            }

            foreach (($attribute['values_en'] ?? []) as $position => $value) {
                $sizeValue = trim((string) ($value['name'] ?? ''));

                if ($sizeValue === '') {
                    continue;
                }

                $size = ProductSize::updateOrCreate(
                    ['value' => $sizeValue],
                    ['position' => $position]
                );

                $ids[] = $size->id;
            }
        }

        $product->sizes()->sync(array_values(array_unique($ids)));
    }

    private function syncCollections(Product $product, array $collections): void
    {
        $ids = [];

        foreach ($collections as $position => $item) {
            $slugEn = trim((string) ($item['slug_en'] ?? ''));

            if ($slugEn === '') {
                $slugEn = Str::slug((string) ($item['name_en'] ?? 'collection'));
            }

            $collection = ProductCollection::updateOrCreate(
                ['slug_en' => $slugEn],
                [
                    'slug_fr' => $item['slug_fr'] ?? null,
                    'name_en' => $item['name_en'] ?? $slugEn,
                    'name_fr' => $item['name_fr'] ?? null,
                    'path_en' => $item['path_en'] ?? null,
                    'path_fr' => $item['path_fr'] ?? null,
                    'position' => $position,
                ]
            );

            $ids[] = $collection->id;
        }

        $product->collections()->sync(array_values(array_unique($ids)));
    }

    private function syncMedia(Product $product, array $images): void
    {
        if ((bool) $this->option('fresh-media')) {
            $product->clearMediaCollection('gallery');
        }

        usort($images, fn ($a, $b) => ((int) ($a['position'] ?? 0)) <=> ((int) ($b['position'] ?? 0)));

        foreach ($images as $position => $image) {
            $url = $image['url'] ?? null;

            if (! $url) {
                continue;
            }

            $media = $product->getMedia('gallery')->first(function ($media) use ($image, $url) {
                return (string) $media->getCustomProperty('source_id') === (string) ($image['id'] ?? '')
                    || $media->getCustomProperty('source_url') === $url;
            });

            if (! $media) {
                try {
                    $media = $product
                        ->addMediaFromUrl($url)
                        ->usingName($image['title'] ?: basename(parse_url($url, PHP_URL_PATH)))
                        ->withCustomProperties([
                            'source_id' => $image['id'] ?? null,
                            'source_url' => $url,
                            'alt' => $image['alt'] ?? null,
                            'is_featured' => (bool) ($image['is_featured'] ?? false),
                        ])
                        ->toMediaCollection('gallery');
                } catch (Throwable $e) {
                    $this->newLine();
                    $this->warn("Image skipped: {$url}");
                    $this->warn($e->getMessage());
                    continue;
                }
            }

            $media->order_column = $position + 1;
            $media->save();
        }
    }

    private function price(mixed $price): string
    {
        if (is_array($price)) {
            $price = $price['current'] ?? $price['regular'] ?? 0;
        }

        return number_format((float) $price, 2, '.', '');
    }

    private function date(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}