<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Services\Site\SiteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteApiController extends Controller
{
    public function __construct(
        private readonly SiteService $site,
    ) {}

    public function home(): JsonResponse
    {
        return response()->json($this->site->home());
    }

    public function catalog(Request $request): JsonResponse
    {
        $query = trim((string) $request->string('q'));

        return response()->json($this->site->catalog($query));
    }

    public function category(Request $request, string $categorySlug): JsonResponse
    {
        $perPage = $this->catalogListingPerPage($request);

        return response()->json($this->site->category(
            $categorySlug,
            max(1, (int) $request->query('subcategories_page', 1)),
            max(1, (int) $request->query('products_page', 1)),
            $perPage,
        ));
    }

    public function categorySubcategories(Request $request, string $categorySlug): JsonResponse
    {
        $perPage = $this->catalogListingPerPage($request);

        return response()->json($this->site->categorySubcategoriesPage(
            $categorySlug,
            max(1, (int) $request->query('page', 1)),
            $perPage,
        ));
    }

    public function categoryProducts(Request $request, string $categorySlug): JsonResponse
    {
        $perPage = $this->catalogListingPerPage($request);

        return response()->json($this->site->categoryProductsPage(
            $categorySlug,
            max(1, (int) $request->query('page', 1)),
            $perPage,
        ));
    }

    private function catalogListingPerPage(Request $request): int
    {
        return min(48, max(6, (int) $request->query('per_page', 12)));
    }

    public function product(string $categorySlug, string $productSlug): JsonResponse
    {
        return response()->json($this->site->product($categorySlug, $productSlug));
    }

    public function page(string $slug): JsonResponse
    {
        return response()->json($this->site->page($slug));
    }

    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->string('q'));

        return response()->json($this->site->search($query));
    }

    public function supportArticles(Request $request): JsonResponse
    {
        $query = trim((string) $request->string('q'));

        return response()->json($this->site->supportArticles($query));
    }

    public function supportArticle(string $slug): JsonResponse
    {
        return response()->json($this->site->supportArticle($slug));
    }

    public function settings(): JsonResponse
    {
        return response()->json($this->site->settings());
    }

    public function contacts(): JsonResponse
    {
        return response()->json($this->site->contacts());
    }

    public function feedback(StoreFeedbackRequest $request): JsonResponse
    {
        return response()->json(
            $this->site->submitFeedback($request->validated()),
            201
        );
    }
}
