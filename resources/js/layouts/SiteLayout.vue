<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import SiteHeaderDesktop from '../components/SiteHeaderDesktop.vue';
import SiteHeaderMobile from '../components/SiteHeaderMobile.vue';
import SiteFooter from '../components/SiteFooter.vue';
import FeedbackForm from '../components/FeedbackForm.vue';

defineProps({
    query: {
        type: String,
        default: '',
    },
    settings: {
        type: Object,
        default: () => ({}),
    },
    sourcePath: {
        type: String,
        required: true,
    },
});

const DESKTOP_MIN = 1501;
const isDesktop = ref(true);
let mq;

const syncLayout = () => {
    if (typeof window === 'undefined') {
        return;
    }
    isDesktop.value = window.matchMedia(`(min-width: ${DESKTOP_MIN}px)`).matches;
};

onMounted(() => {
    syncLayout();
    mq = window.matchMedia(`(min-width: ${DESKTOP_MIN}px)`);
    mq.addEventListener('change', syncLayout);
});

onUnmounted(() => {
    mq?.removeEventListener('change', syncLayout);
});
</script>

<template>
    <SiteHeaderDesktop v-if="isDesktop" :query="query" :settings="settings" />
    <SiteHeaderMobile v-else :query="query" :settings="settings" />
    <slot />
    <FeedbackForm :source-path="sourcePath" />
    <SiteFooter :settings="settings" />
</template>
