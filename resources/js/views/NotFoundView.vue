<script setup>
import { onMounted } from 'vue';
import SiteBreadcrumbs from '../components/SiteBreadcrumbs.vue';

onMounted(() => {
    document.title = 'Страница не найдена | Atlantic Group';
});
</script>

<template>
    <SiteBreadcrumbs :items="[{ label: 'Главная', to: '/' }, { label: 'Страница не найдена' }]" />
    <section>
        <div class="container py-5">
        <div class="h1"><span class="underline_bottom">Страница не найдена</span></div>
        <div class="row g-3 justify-content-center h4">Проверьте адрес страницы или вернитесь на главную.</div>
        </div>
    </section>
</template>
