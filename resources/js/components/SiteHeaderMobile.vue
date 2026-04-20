<script setup>
import { computed, ref, watch } from 'vue';
import { useRoute } from 'vue-router';

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

const route = useRoute();
const open = ref(false);

watch(
    () => route.fullPath,
    () => {
        open.value = false;
    },
);

const toggle = () => {
    open.value = !open.value;
};
</script>

<template>
    <section id="head_mobile">
        <header>
            <div class="container">
                <div class="d-flex align-items-center justify-content-between py-2 gap-2">
                    <div class="at_but at_but_head m-0">
                        <RouterLink to="/"><img :src="'/images/original/at_logo_header2.png'" :alt="companyName"></RouterLink>
                    </div>
                    <button type="button" class="at_mobile_menu_btn" aria-label="Открыть меню" :aria-expanded="open" @click="toggle">
                        <span class="at_mobile_menu_icon" :class="{ 'is-open': open }" />
                    </button>
                </div>
            </div>
            <Transition name="at_fade">
                <div v-if="open" class="at_mobile_nav_overlay" @click.self="open = false">
                    <nav class="at_mobile_nav_panel" @click.stop>
                        <form action="/search" method="get" class="at_search mb-3" @submit="open = false">
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
                                    placeholder="Поиск"
                                    class="form-control at_search_style"
                                >
                            </div>
                            <button class="btn at_btn_style" type="submit">Найти</button>
                        </form>
                        <p class="at_phone text-center mb-2">{{ displayPhone }}</p>
                        <p v-if="settings?.email" class="text-center small mb-2">
                            <a :href="`mailto:${settings.email}`" class="text-decoration-none at_header_email">{{ settings.email }}</a>
                        </p>
                        <ul class="at_mobile_nav_list list-unstyled text-center mb-0">
                            <li><RouterLink class="at_nav_link" to="/" @click="open = false">Главная</RouterLink></li>
                            <li><RouterLink class="at_nav_link" to="/about" @click="open = false">О компании</RouterLink></li>
                            <li><RouterLink class="at_nav_link" to="/catalog" @click="open = false">Каталог</RouterLink></li>
                            <li><RouterLink class="at_nav_link" to="/support" @click="open = false">Техподдержка</RouterLink></li>
                            <li><RouterLink class="at_nav_link" to="/contacts" @click="open = false">Контакты</RouterLink></li>
                        </ul>
                        <div class="text-center mt-3">
                            <RouterLink class="at_but_style" to="/ask" @click="open = false">Задать вопрос</RouterLink>
                        </div>
                    </nav>
                </div>
            </Transition>
        </header>
    </section>
</template>
