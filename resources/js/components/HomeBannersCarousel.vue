<script setup>
import { A11y, Autoplay, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { ref } from 'vue';
import 'swiper/css';
import 'swiper/css/pagination';

defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});

const modules = [Autoplay, Pagination, A11y];

const isExternal = (link) => typeof link === 'string' && /^https?:\/\//i.test(link);
const swiperRef = ref(null);

const onSwiper = (swiper) => {
    swiperRef.value = swiper;
};

const onZoneClick = (event, direction) => {
    const target = event.target;
    if (target instanceof Element && target.closest('.at_home_banner__btn')) {
        return;
    }
    if (!swiperRef.value) {
        return;
    }
    if (direction === 'prev') {
        swiperRef.value.slidePrev();
    } else {
        swiperRef.value.slideNext();
    }
};
</script>

<template>
    <section id="at_banner_home">
        <Swiper
            v-if="items.length"
            class="at_home_banner_swiper"
            :modules="modules"
            :slides-per-view="1"
            :autoplay="{ delay: 5000, disableOnInteraction: false, pauseOnMouseEnter: true }"
            :pagination="{ clickable: true, dynamicBullets: false }"
            :speed="700"
            :loop="items.length > 1"
            :a11y="{ enabled: true }"
            @swiper="onSwiper"
        >
            <SwiperSlide v-for="item in items" :key="item.id">
                <div
                    class="at_home_banner"
                    :style="{ backgroundImage: `url(${item.background_image || '/images/original/at_img_header_home.png'})` }"
                >
                    <button
                        type="button"
                        class="at_home_banner_zone at_home_banner_zone--left"
                        aria-label="Предыдущий баннер"
                        @click="onZoneClick($event, 'prev')"
                    />
                    <button
                        type="button"
                        class="at_home_banner_zone at_home_banner_zone--right"
                        aria-label="Следующий баннер"
                        @click="onZoneClick($event, 'next')"
                    />
                    <div class="container">
                        <div class="at_home_banner__content">
                            <div class="at_home_banner__text">
                                <h2 class="at_home_banner__title">{{ item.title }}</h2>
                                <p v-if="item.description" class="at_home_banner__description">{{ item.description }}</p>
                            </div>
                            <div class="at_home_banner__action">
                                <component
                                    v-if="item.button_text && item.button_link"
                                    :is="isExternal(item.button_link) ? 'a' : 'RouterLink'"
                                    :to="!isExternal(item.button_link) ? item.button_link : undefined"
                                    :href="isExternal(item.button_link) ? item.button_link : undefined"
                                    :target="isExternal(item.button_link) ? '_blank' : undefined"
                                    :rel="isExternal(item.button_link) ? 'noopener noreferrer' : undefined"
                                    class="at_home_banner__btn"
                                    :style="{ backgroundColor: item.button_color || '#ea3a31' }"
                                >
                                    {{ item.button_text }}
                                </component>
                            </div>
                        </div>
                    </div>
                </div>
            </SwiperSlide>
        </Swiper>

        <div v-else class="at_home_banner" style="background-image:url('/images/original/at_img_header_home.png')">
            <div class="container">
                <div class="at_home_banner__content">
                    <h2 class="at_home_banner__title">Доверяй качеству</h2>
                </div>
            </div>
        </div>
    </section>
</template>
