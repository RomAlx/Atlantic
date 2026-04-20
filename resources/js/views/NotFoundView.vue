<script setup>
import { onMounted } from 'vue';
import SiteBreadcrumbs from '../components/SiteBreadcrumbs.vue';

onMounted(() => {
    document.title = 'Страница не найдена | Atlantic Group';
});
</script>

<template>
    <section>
        <SiteBreadcrumbs :items="[{ label: 'Главная', to: '/' }, { label: 'Страница не найдена' }]" />
        <h1 class="mb-4 text-3xl font-semibold">Страница не найдена</h1>
        <p>Проверьте адрес страницы или вернитесь на главную.</p>
    </section>
</template>
