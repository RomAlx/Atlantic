<?php

namespace App\Services\Site;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Repositories\FeedbackRequestRepositoryInterface;
use App\Contracts\Repositories\PageRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\SettingRepositoryInterface;
use App\Contracts\Repositories\SupportArticleRepositoryInterface;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use App\Models\SupportArticle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

readonly class SiteService
{
    public function __construct(
        private CategoryRepositoryInterface $categories,
        private ProductRepositoryInterface $products,
        private PageRepositoryInterface $pages,
        private SupportArticleRepositoryInterface $supportArticles,
        private SettingRepositoryInterface $settings,
        private FeedbackRequestRepositoryInterface $feedbackRequests,
    ) {}

    /**
     * @return array{categories: list<array<string, mixed>>, products: list<array<string, mixed>>}
     */
    public function home(): array
    {
        $categories = $this->categories->getActiveRootForHome(8)
            ->map(fn (Category $category) => $this->categorySummaryPayload($category))
            ->values()
            ->all();

        $products = $this->products->getActiveFeaturedForHome(8)
            ->map(fn (Product $product) => $this->productSummaryPayload($product))
            ->values()
            ->all();

        $setting = $this->settings->first();
        $defaultAbout1 = 'Atlantic Group поставляет строительные и отделочные материалы для профессионалов и частных заказчиков. Мы отбираем продукцию проверенных производителей и помогаем подобрать решение под ваш объект.';
        $defaultAbout2 = 'Наши специалисты консультируют по ассортименту, срокам и условиям поставки. Работаем прозрачно: фиксируем договорённости и сопровождаем заказ от заявки до получения материала на объекте.';
        $defaultBlocks = [
            ['title' => 'Доставка и логистика', 'text' => 'Организуем отгрузку в удобное время и согласуем маршрут, чтобы материалы прибыли целыми и в срок.'],
            ['title' => 'Подбор материалов', 'text' => 'Поможем сравнить варианты по характеристикам и бюджету — от черновой отделки до финишных покрытий.'],
            ['title' => 'Гарантия качества', 'text' => 'Сотрудничаем с надёжными брендами и контролируем соответствие заявленным параметрам продукции.'],
            ['title' => 'Сопровождение заказа', 'text' => 'На связи на этапе подбора, оплаты и получения: ответим на вопросы и подскажем следующий шаг.'],
        ];
        $blocks = is_array($setting?->home_client_blocks) && $setting->home_client_blocks !== []
            ? array_values($setting->home_client_blocks)
            : $defaultBlocks;

        return [
            'categories' => $categories,
            'products' => $products,
            'home_content' => [
                'about_paragraph_1' => filled($setting?->home_about_paragraph_1)
                    ? (string) $setting->home_about_paragraph_1
                    : $defaultAbout1,
                'about_paragraph_2' => filled($setting?->home_about_paragraph_2)
                    ? (string) $setting->home_about_paragraph_2
                    : $defaultAbout2,
                'client_blocks' => $blocks,
            ],
        ];
    }

    /**
     * @return array{items: list<array<string, mixed>>}
     */
    public function catalog(string $searchQuery): array
    {
        $items = $this->categories->getActiveRootForCatalog($searchQuery !== '' ? $searchQuery : null)
            ->map(function (Category $category) {
                $row = $this->categorySummaryPayload($category);
                $row['products_count'] = (int) $category->products_in_subtree_count;
                $row['subcategories_count'] = (int) ($category->children_count ?? 0);

                return $row;
            })
            ->values()
            ->all();

        return ['items' => $items];
    }

    /**
     * @return array{
     *     category: array<string, mixed>,
     *     subcategories: array{items: list<array<string, mixed>>, meta: array<string, int>},
     *     products: array{items: list<array<string, mixed>>, meta: array<string, int>}
     * }
     */
    public function category(string $categorySlug, int $subcategoriesPage, int $productsPage, int $perPage): array
    {
        $category = $this->categories->findActiveBySlug($categorySlug);

        $subPaginator = $this->categories->paginateActiveChildren($category->id, $perPage, $subcategoriesPage);
        $subcategories = [
            'items' => $subPaginator->getCollection()
                ->map(fn (Category $c) => $this->categorySummaryPayload($c))
                ->values()
                ->all(),
            'meta' => $this->paginationMeta($subPaginator),
        ];

        $subtreeIds = $this->categories->getActiveSubtreeCategoryIds($category);
        $prodPaginator = $this->products->paginateActiveByCategoryIds($subtreeIds, $perPage, $productsPage);
        $products = [
            'items' => $prodPaginator->getCollection()
                ->map(fn (Product $product) => $this->productCardPayload($product, $category->id))
                ->values()
                ->all(),
            'meta' => $this->paginationMeta($prodPaginator),
        ];

        return [
            'category' => array_merge($this->categoryDetailPayload($category), [
                'breadcrumbs' => $this->categoryBreadcrumbs($category),
            ]),
            'subcategories' => $subcategories,
            'products' => $products,
        ];
    }

    /**
     * @return array{items: list<array<string, mixed>>, meta: array<string, int>}
     */
    public function categorySubcategoriesPage(string $categorySlug, int $page, int $perPage): array
    {
        $category = $this->categories->findActiveBySlug($categorySlug);
        $paginator = $this->categories->paginateActiveChildren($category->id, $perPage, $page);

        return [
            'items' => $paginator->getCollection()
                ->map(fn (Category $c) => $this->categorySummaryPayload($c))
                ->values()
                ->all(),
            'meta' => $this->paginationMeta($paginator),
        ];
    }

    /**
     * @return array{items: list<array<string, mixed>>, meta: array<string, int>}
     */
    public function categoryProductsPage(string $categorySlug, int $page, int $perPage): array
    {
        $category = $this->categories->findActiveBySlug($categorySlug);
        $subtreeIds = $this->categories->getActiveSubtreeCategoryIds($category);
        $paginator = $this->products->paginateActiveByCategoryIds($subtreeIds, $perPage, $page);

        return [
            'items' => $paginator->getCollection()
                ->map(fn (Product $product) => $this->productCardPayload($product, $category->id))
                ->values()
                ->all(),
            'meta' => $this->paginationMeta($paginator),
        ];
    }

    /**
     * @return array{current_page: int, last_page: int, per_page: int, total: int}
     */
    private function paginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }

    /**
     * @return array{item: array<string, mixed>}
     */
    public function product(string $categorySlug, string $productSlug): array
    {
        $product = $this->products->findActiveBySlugs($categorySlug, $productSlug);

        return [
            'item' => [
                'id' => $product->id,
                'category_id' => $product->category_id,
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'short_description' => $product->short_description,
                'description' => $product->description,
                'specifications' => $product->specifications,
                'seo_title' => $product->seo_title,
                'seo_description' => $product->seo_description,
                'category' => $product->category,
                'images' => $product->images->map(fn ($img) => [
                    'id' => $img->id,
                    'path' => $img->publicUrl(),
                    'alt' => $img->alt,
                    'sort_order' => $img->sort_order,
                    'is_main' => $img->is_main,
                ])->values()->all(),
            ],
        ];
    }

    /**
     * @return array{item: Page}
     */
    public function page(string $slug): array
    {
        return ['item' => $this->pages->findActiveBySlug($slug)];
    }

    /**
     * @return array{query: string, items: list<array<string, mixed>>}
     */
    public function search(string $query): array
    {
        $items = $this->products->searchActive($query !== '' ? $query : null)
            ->map(fn (Product $product) => $this->productSummaryPayload($product))
            ->values()
            ->all();

        return [
            'query' => $query,
            'items' => $items,
        ];
    }

    /**
     * @return array{query: string, items: list<array<string, mixed>>}
     */
    public function supportArticles(string $query): array
    {
        $items = $this->supportArticles->searchActive($query !== '' ? $query : null)
            ->map(fn (SupportArticle $article) => $this->supportArticleCardPayload($article))
            ->values()
            ->all();

        return [
            'query' => $query,
            'items' => $items,
        ];
    }

    /**
     * @return array{item: array<string, mixed>}
     */
    public function supportArticle(string $slug): array
    {
        $article = $this->supportArticles->findActiveBySlug($slug);
        $payload = $this->supportArticleDetailPayload($article);
        $payload['content_html'] = Str::markdown((string) ($article->content_md ?? ''));

        return ['item' => $payload];
    }

    /**
     * @return array{item: array<string, mixed>|null}
     */
    public function settings(): array
    {
        return $this->contactsPayload();
    }

    /**
     * Данные для страницы «Контакты» и настроек сайта (из одной записи в БД).
     *
     * @return array{item: array<string, mixed>|null}
     */
    public function contacts(): array
    {
        return $this->contactsPayload();
    }

    /**
     * @return array{item: array<string, mixed>|null}
     */
    private function contactsPayload(): array
    {
        $setting = $this->settings->first();

        if (! $setting instanceof Setting) {
            return ['item' => null];
        }

        $networks = config('site.social_networks', []);

        $socialLinks = collect($setting->social_links ?? [])
            ->map(function (array $row) use ($networks): array {
                $key = $row['network'] ?? 'other';

                return [
                    'network' => $key,
                    'label' => $networks[$key]['label'] ?? $key,
                    'url' => $row['url'] ?? '',
                ];
            })
            ->values()
            ->all();

        return [
            'item' => [
                'company_name' => $setting->company_name,
                'email' => $setting->email,
                'address' => $setting->address,
                'warehouse_address' => $setting->warehouse_address,
                'main_phone' => $setting->mainPhoneNumber(),
                'phone' => $setting->mainPhoneNumber(),
                'phones' => $setting->phones ?? [],
                'social_links' => $socialLinks,
                'socials' => $setting->socials ?? [],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{message: string, id: int}
     */
    public function submitFeedback(array $data): array
    {
        $feedback = $this->feedbackRequests->create($data + [
            'status' => 'new',
        ]);

        return [
            'message' => 'Заявка отправлена',
            'id' => $feedback->id,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function categorySummaryPayload(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'image' => $category->imagePublicUrl(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function categoryDetailPayload(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'seo_title' => $category->seo_title,
            'seo_description' => $category->seo_description,
            'image' => $category->imagePublicUrl(),
        ];
    }

    /**
     * @return list<array{id: int, name: string, slug: string}>
     */
    private function categoryBreadcrumbs(Category $leaf): array
    {
        $chain = [];
        $current = $leaf;
        $guard = 0;

        while ($current && $guard++ < 50) {
            array_unshift($chain, [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug,
            ]);
            if (! $current->parent_id) {
                break;
            }
            $current = Category::query()
                ->where('id', $current->parent_id)
                ->where('is_active', true)
                ->first();
        }

        return $chain;
    }

    /**
     * @return array<string, mixed>
     */
    private function productSummaryPayload(Product $product): array
    {
        return [
            'id' => $product->id,
            'category' => $product->category,
            'name' => $product->name,
            'slug' => $product->slug,
            'short_description' => $product->short_description,
            'sku' => $product->sku,
            'main_image' => $product->images->first()?->publicUrl(),
            'images' => $this->productImagesList($product),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function productCardPayload(Product $product, ?int $listingCategoryId = null): array
    {
        $cat = $product->category;

        return [
            'id' => $product->id,
            'category_id' => $product->category_id,
            'name' => $product->name,
            'slug' => $product->slug,
            'short_description' => $product->short_description,
            'sku' => $product->sku,
            'main_image' => $product->images->first()?->publicUrl(),
            'images' => $this->productImagesList($product),
            'category' => [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
            ],
            'in_child_category' => $listingCategoryId !== null && (int) $product->category_id !== (int) $listingCategoryId,
        ];
    }

    /**
     * @return list<array{path: string, alt: string|null}>
     */
    private function productImagesList(Product $product): array
    {
        return $product->images
            ->map(fn ($img) => [
                'path' => $img->publicUrl(),
                'alt' => $img->alt,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function supportArticleCardPayload(SupportArticle $article): array
    {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'description' => $article->description,
            'preview_image' => $article->previewImagePublicUrl(),
            'video_url' => $article->video_url,
            'updated_at' => $article->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function supportArticleDetailPayload(SupportArticle $article): array
    {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'description' => $article->description,
            'content_md' => $article->content_md,
            'preview_image' => $article->previewImagePublicUrl(),
            'video_url' => $article->video_url,
            'seo_title' => $article->seo_title,
            'seo_description' => $article->seo_description,
        ];
    }
}
