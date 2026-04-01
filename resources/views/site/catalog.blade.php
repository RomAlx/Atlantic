@extends('site.layout')

@section('content')
    @if ($query !== '')
        <p class="mb-4 text-sm text-slate-600">Поиск по категориям: "{{ $query }}"</p>
    @endif

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($categories as $category)
            <a href="{{ route('site.catalog.category', $category->slug) }}" class="rounded border bg-white p-4 hover:shadow">
                <h2 class="mb-2 text-lg font-medium">{{ $category->name }}</h2>
                <p class="mb-2 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit(strip_tags((string) $category->description), 120) }}</p>
                <p class="text-sm text-slate-500">Товаров: {{ $category->products_count }}</p>
            </a>
        @empty
            <p class="text-slate-600">Ничего не найдено.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $categories->links() }}</div>
    @include('site._feedback_form')
@endsection
