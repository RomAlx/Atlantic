<section class="mt-10 rounded border bg-white p-6">
    <h2 class="mb-4 text-xl font-semibold">Задать вопрос</h2>
    <form action="{{ route('site.feedback.store') }}" method="post" class="grid gap-4 md:grid-cols-2">
        @csrf
        <input type="hidden" name="source_page" value="{{ url()->current() }}">

        <div>
            <label class="mb-1 block text-sm">Имя *</label>
            <input name="name" value="{{ old('name') }}" required class="w-full rounded border px-3 py-2">
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm">Телефон</label>
            <input name="phone" value="{{ old('phone') }}" class="w-full rounded border px-3 py-2">
            @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded border px-3 py-2">
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="md:col-span-2">
            <label class="mb-1 block text-sm">Сообщение</label>
            <textarea name="message" rows="4" class="w-full rounded border px-3 py-2">{{ old('message') }}</textarea>
            @error('message')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="md:col-span-2">
            <button class="rounded bg-slate-900 px-5 py-2 text-white">Отправить заявку</button>
        </div>
    </form>
</section>
