<script setup>
import { computed } from 'vue';
import { A11y, Autoplay, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { resolveMediaUrl } from '../utils/media';
import 'swiper/css';
import 'swiper/css/pagination';

const props = defineProps({
    images: {
        type: Array,
        default: () => [],
    },
    name: {
        type: String,
        default: '',
    },
    fallback: {
        type: String,
        default: '/images/source/normalized/image-3.jpg',
    },
    /** card — превью в сетке; detail — крупная галерея на странице товара */
    variant: {
        type: String,
        default: 'card',
        validator: (v) => ['card', 'detail'].includes(v),
    },
});

const modules = [Autoplay, Pagination, A11y];

const list = computed(() => {
    const raw = props.images ?? [];
    const withPath = raw.filter((img) => img && (img.path || img.url));

    if (withPath.length === 0) {
        return [{ path: null, alt: props.name }];
    }

    return withPath;
});

const swiperKey = computed(() => `${props.variant}-${list.value.length}-${list.value.map((x) => x.path).join('|')}`);

const autoplayOptions = computed(() =>
    list.value.length > 1
        ? {
              delay: 5000,
              disableOnInteraction: false,
              pauseOnMouseEnter: true,
          }
        : false,
);
</script>

<template>
    <div
        class="at_preview_swiper"
        :class="{
            'at_preview_swiper--card': variant === 'card',
            'at_preview_swiper--detail': variant === 'detail',
        }"
    >
        <Swiper
            :key="swiperKey"
            class="at_swiper_root"
            :modules="modules"
            :slides-per-view="1"
            :space-between="0"
            :speed="800"
            :autoplay="autoplayOptions"
            :pagination="{ clickable: true, dynamicBullets: list.length > 3 }"
            :a11y="{ enabled: true }"
            :watch-overflow="true"
        >
            <SwiperSlide v-for="(img, i) in list" :key="i" class="at_swiper_slide">
                <img
                    class="at_preview_swiper_img"
                    :src="resolveMediaUrl(img.path, fallback)"
                    :alt="img.alt || name"
                    loading="lazy"
                >
            </SwiperSlide>
        </Swiper>
    </div>
</template>
