<script setup>
defineProps({
    query: {
        type: String,
        default: '',
    },
    settings: {
        type: Object,
        default: () => ({}),
    },
});
</script>

<template>
    <section id="head">
        <header>
            <div class="container">
                <div class="row">
                    <div class="col-xxl-3 col-xl-3 col-md-3 col-sm-3 col-xs-1">
                        <div class="sa_logo">
                            <RouterLink to="/"><img :src="'/images/original/at_logo_header.png'" alt="Atlantic Group"></RouterLink>
                        </div>
                    </div>
                    <div class="col-xxl-9 col-xl-9 col-md-9 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-xxl-9 col-md-7 col-sm-6 col-12">
                                <form action="/search" method="get" class="at_search">
                                    <input
                                        name="q"
                                        type="search"
                                        :value="query"
                                        placeholder="Поиск товаров в каталоге"
                                        class="form-control me-2 at_search_style"
                                    >
                                    <button class="btn at_btn_style" type="submit">Найти</button>
                                </form>
                            </div>
                            <div class="col-xxl-3 col-sm-5 col-12 align-self-center">
                                <div class="at_phone">{{ settings?.phone || '+7 (901) 123 45 67' }}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xxl-9 col-md-8 col-sm-6 col-12">
                                <div class="cont flexx at_wrap_box">
                                    <div class="wrap_menu">
                                        <nav class="menu flexx">
                                            <div class="item"><RouterLink to="/">Главная</RouterLink></div>
                                            <div class="item"><RouterLink to="/about">О компании</RouterLink></div>
                                            <div class="item"><RouterLink to="/catalog">Каталог</RouterLink></div>
                                            <div class="item"><RouterLink to="/contacts">Контакты</RouterLink></div>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-md-4 col-sm-6 col-12 align-self-center">
                                <div class="at_but at_but_head">
                                    <RouterLink class="at_but_style" to="/ask">Задать вопрос</RouterLink>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    </section>
    <section id="head_mobile">
        <header>
            <div class="container">
                <div class="at_but at_but_head">
                    <RouterLink to="/"><img :src="'/images/original/at_logo_header2.png'" alt="Atlantic Group"></RouterLink>
                </div>
            </div>
        </header>
    </section>
</template>
