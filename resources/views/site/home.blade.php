@extends('site.layout')

@section('content')
    <section class="mb-10">
        <h2 class="mb-4 text-2xl font-semibold">Категории</h2>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($categories as $category)
                <a href="{{ route('site.catalog.category', $category->slug) }}" class="rounded border bg-white p-4 hover:shadow">
                    <h3 class="mb-2 text-lg font-medium">{{ $category->name }}</h3>
                    <p class="text-sm text-slate-600">{{ \Illuminate\Support\Str::limit(strip_tags((string) $category->description), 110) }}</p>
                </a>
            @empty
                <p class="text-slate-600">Категории пока не добавлены.</p>
            @endforelse
        </div>
    </section>

    <section>
        <h2 class="mb-4 text-2xl font-semibold">Популярные товары</h2>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($featuredProducts as $product)
                <a href="{{ route('site.catalog.product', [$product->category->slug, $product->slug]) }}" class="rounded border bg-white p-4 hover:shadow">
                    <h3 class="mb-2 text-lg font-medium">{{ $product->name }}</h3>
                    <p class="text-sm text-slate-600">{{ \Illuminate\Support\Str::limit(strip_tags((string) $product->short_description), 100) }}</p>
                </a>
            @empty
                <p class="text-slate-600">Товары пока не добавлены.</p>
            @endforelse
        </div>
    </section>

    @include('site._feedback_form')
@endsection
