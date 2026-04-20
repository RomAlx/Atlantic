<script setup>
import { computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import YandexMetrikaCounter from './components/YandexMetrikaCounter.vue';
import SiteLayout from './layouts/SiteLayout.vue';
import { useSettingsStore } from './stores/settingsStore';

const route = useRoute();
const settingsStore = useSettingsStore();

const query = computed(() => String(route.query.q ?? ''));
const sourcePath = computed(() => route.fullPath);

onMounted(() => {
    settingsStore.load();
});
</script>

<template>
    <main>
        <YandexMetrikaCounter />
        <SiteLayout :query="query" :settings="settingsStore.item" :source-path="sourcePath">
            <section id="at_content">
                <div class="container">
                    <RouterView />
                </div>
            </section>
        </SiteLayout>
    </main>
</template>
