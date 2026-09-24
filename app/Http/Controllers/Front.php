<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductCollection;
use Wamania\Snowball\StemmerFactory;

class Front extends Controller
{
    public function home()
    {
        $topProductIds = [29, 28, 27, 26];
        $topFashionIds = [37, 36, 35, 34, 33, 32, 31, 30, 24];

        $featuredProducts = Product::query()
            ->forCard()
            ->whereIn(
                'products.id',
                array_values(array_unique([...$topProductIds, ...$topFashionIds]))
            )
            ->get()
            ->keyBy('id');

        $topProducts = collect($topProductIds)
            ->map(fn (int $id) => $featuredProducts->get($id))
            ->filter()
            ->values();

        $topFashop = collect($topFashionIds)
            ->map(fn (int $id) => $featuredProducts->get($id))
            ->filter()
            ->values();

        $collection = Product::query()
            ->forCard()
            ->where('products.active', true)
            ->whereHas('collections', fn ($query) => $query
                ->where('product_collections.id', 3)
            )
            ->latest('products.id')
            ->get();

        return view('home', [
            'topProducts' => $topProducts,
            'topFashop' => $topFashop,
            'collection' => $collection,
        ]);
    }


    public function search(Request $request): JsonResponse
    {
        $search = Str::of((string) $request->query('q'))
            ->squish()
            ->limit(80, '')
            ->toString();

        if (mb_strlen($search) < 2) {
            return response()->json([
                'products' => [],
                'collections' => [],
                'total' => 0,
            ]);
        }

        $locale = app()->getLocale() === 'fr' ? 'fr' : 'en';
        $stemmer = StemmerFactory::create($locale);

        $terms = preg_split(
            '/[^\p{L}\p{N}]+/u',
            mb_strtolower($search),
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        $booleanQuery = collect($terms)
            ->map(fn (string $term) => $stemmer->stem($term))
            ->filter(fn (string $term) => mb_strlen($term) >= 2)
            ->unique()
            ->map(fn (string $term) => '+' . $term . '*')
            ->implode(' ');

        if ($booleanQuery === '') {
            return response()->json([
                'products' => [],
                'collections' => [],
                'total' => 0,
            ]);
        }

        $titleColumn = "title_{$locale}";
        $descriptionColumn = "description_{$locale}";
        $slugColumn = "slug_{$locale}";

        $products = Product::query()
            ->where('active', true)
            ->whereRaw(
                'MATCH(title_en, title_fr, description_en, description_fr)
             AGAINST(? IN BOOLEAN MODE)',
                [$booleanQuery]
            )
            ->select([
                'id',
                'title_en',
                'title_fr',
                'description_en',
                'description_fr',
                'slug_en',
                'slug_fr',
                'price',
            ])
            ->selectRaw(
                'MATCH(title_en, title_fr, description_en, description_fr)
             AGAINST(? IN BOOLEAN MODE) AS relevance',
                [$booleanQuery]
            )
            ->with([
                'media' => fn ($query) => $query
                    ->where('collection_name', 'gallery')
                    ->orderBy('order_column')
                    ->limit(1),
            ])
            ->orderByRaw(
                "CASE
                WHEN LOWER(COALESCE({$titleColumn}, title_en)) = LOWER(?) THEN 0
                WHEN LOWER(COALESCE({$titleColumn}, title_en)) LIKE LOWER(?) THEN 1
                ELSE 2
            END",
                [$search, $search . '%']
            )
            ->orderByDesc('relevance')
            ->limit(8)
            ->get()
            ->map(function (Product $product) use (
                $descriptionColumn,
                $slugColumn
            ) {
                return [
                    'id' => $product->id,
                    'type' => 'product',
                    'title' => $product->title,
                    'description' => Str::limit(
                        trim(strip_tags(
                            $product->{$descriptionColumn}
                                ?: $product->description_en
                                ?: ''
                        )),
                        110
                    ),
                    'price' => (float) $product->price,
                    'image' => $product->getFirstMediaUrl('gallery', 'small'),
                    'url' => localized_route('shop.item', [
                        'url' => $product->{$slugColumn} ?: $product->slug_en,
                    ]),
                ];
            })
            ->values();

        $collectionNameColumn = "name_{$locale}";
        $collectionDescriptionColumn = "description_{$locale}";
        $collectionSlugColumn = "slug_{$locale}";

        $collections = ProductCollection::query()
            ->where('active', true)
            ->whereRaw(
                'MATCH(name_en, name_fr, description_en, description_fr)
             AGAINST(? IN BOOLEAN MODE)',
                [$booleanQuery]
            )
            ->select([
                'id',
                'name_en',
                'name_fr',
                'description_en',
                'description_fr',
                'slug_en',
                'slug_fr',
            ])
            ->selectRaw(
                'MATCH(name_en, name_fr, description_en, description_fr)
             AGAINST(? IN BOOLEAN MODE) AS relevance',
                [$booleanQuery]
            )
            ->orderByRaw(
                "CASE
                WHEN LOWER(COALESCE({$collectionNameColumn}, name_en)) = LOWER(?) THEN 0
                WHEN LOWER(COALESCE({$collectionNameColumn}, name_en)) LIKE LOWER(?) THEN 1
                ELSE 2
            END",
                [$search, $search . '%']
            )
            ->orderByDesc('relevance')
            ->limit(4)
            ->get()
            ->map(function (ProductCollection $collection) use (
                $collectionDescriptionColumn,
                $collectionSlugColumn
            ) {
                return [
                    'id' => $collection->id,
                    'type' => 'collection',
                    'title' => $collection->title,
                    'description' => Str::limit(
                        trim(strip_tags(
                            $collection->{$collectionDescriptionColumn}
                                ?: $collection->description_en
                                ?: ''
                        )),
                        110
                    ),
                    'url' => localized_route('collection', [
                        'url' => $collection->{$collectionSlugColumn} ?: $collection->slug_en,
                    ]),
                ];
            })
            ->values();

        return response()->json([
            'products' => $products,
            'collections' => $collections,
            'total' => $products->count() + $collections->count(),
        ]);
    }


    public function getPage($url)
    {
        $page = Page::where('url_en', $url)
            ->firstOrFail();

        return view('pages.page', [
            'page' => $page,
        ]);
    }
}
