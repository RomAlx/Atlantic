<script setup>
import { computed } from 'vue';

const props = defineProps({
    query: {
        type: String,
        default: '',
    },
    settings: {
        type: Object,
        default: () => ({}),
    },
});

const companyName = computed(() => props.settings?.company_name || 'Atlantic Group');

const displayPhone = computed(
    () => props.settings?.main_phone || props.settings?.phone || '+7 (901) 123 45 67',
);
</script>

<template>
    <section id="head">
        <header>
            <div class="container">
                <div class="at_header_top d-flex flex-wrap align-items-center gap-2 gap-lg-3 py-1">
                    <div class="at_header_logo flex-shrink-0">
                        <div class="sa_logo text-center text-lg-start">
                            <RouterLink to="/"><img :src="'/images/original/at_logo_header.png'" :alt="companyName"></RouterLink>
                        </div>
                    </div>
                    <div class="at_header_search flex-grow-1 min-w-0 d-flex justify-content-center">
                        <form action="/search" method="get" class="at_search w-100" style="max-width: 520px">
                            <div class="at_search_field flex-grow-1 min-w-0">
                                <span class="at_search_icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                                    </svg>
                                </span>
                                <input
                                    name="q"
                                    type="search"
                                    :value="query"
                                    placeholder="Поиск товаров в каталоге"
                                    class="form-control at_search_style"
                                    autocomplete="off"
                                >
                            </div>
                            <button class="btn at_btn_style" type="submit">Найти</button>
                        </form>
                    </div>
                    <div class="at_header_phone flex-shrink-0 text-center text-lg-end ms-lg-auto">
                        <div class="at_phone">{{ displayPhone }}</div>
                        <div v-if="settings?.email" class="small mt-1">
                            <a :href="`mailto:${settings.email}`" class="text-decoration-none at_header_email">{{ settings.email }}</a>
                        </div>
                    </div>
                </div>
                <div class="at_header_nav_row d-flex flex-wrap justify-content-center align-items-center gap-3 gap-xl-4 pb-2 pt-1">
                    <nav class="menu at_header_menu d-flex flex-wrap justify-content-center">
                        <div class="item"><RouterLink class="at_nav_link" to="/">Главная</RouterLink></div>
                        <div class="item"><RouterLink class="at_nav_link" to="/about">О компании</RouterLink></div>
                        <div class="item"><RouterLink class="at_nav_link" to="/catalog">Каталог</RouterLink></div>
                        <div class="item"><RouterLink class="at_nav_link" to="/support">Техподдержка</RouterLink></div>
                        <div class="item"><RouterLink class="at_nav_link" to="/contacts">Контакты</RouterLink></div>
                    </nav>
                    <div class="at_but at_but_head m-0">
                        <RouterLink class="at_but_style" to="/ask">Задать вопрос</RouterLink>
                    </div>
                </div>
            </div>
        </header>
    </section>
</template>
