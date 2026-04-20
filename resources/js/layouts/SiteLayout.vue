<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import SiteHeaderDesktop from '../components/SiteHeaderDesktop.vue';
import SiteHeaderMobile from '../components/SiteHeaderMobile.vue';
import SiteFooter from '../components/SiteFooter.vue';
import FeedbackForm from '../components/FeedbackForm.vue';

const route = useRoute();
const showGlobalFeedbackForm = computed(() => route.name !== 'ask-question');
const feedbackFormHeading = computed(() => (route.name === 'product' ? 'Задать вопрос' : 'Форма обратной связи'));

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
    <FeedbackForm
        v-if="showGlobalFeedbackForm"
        :source-path="sourcePath"
        :heading="feedbackFormHeading"
    />
    <SiteFooter :settings="settings" />
</template>
