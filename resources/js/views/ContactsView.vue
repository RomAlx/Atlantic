<script setup>
import { computed, onMounted, ref } from 'vue';
import { fetchJson } from '../services/api';
import SiteBreadcrumbs from '../components/SiteBreadcrumbs.vue';
import ContactsCompanyBody from '../components/ContactsCompanyBody.vue';

const loading = ref(true);
const error = ref('');
const data = ref({ item: null });

const item = computed(() => data.value?.item);

onMounted(async () => {
    loading.value = true;
    error.value = '';
    try {
        data.value = await fetchJson('/api/contacts');
        document.title = 'Контакты | Atlantic Group';
    } catch (e) {
        error.value = e?.message || 'Не удалось загрузить контакты';
        document.title = 'Контакты | Atlantic Group';
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <section class="at_contacts_page">
        <SiteBreadcrumbs :items="[{ label: 'Главная', to: '/' }, { label: 'Контакты' }]" />
        <h1 class="h1"><span class="underline_bottom">Контакты</span></h1>
        <p v-if="loading" class="text-center">Загрузка...</p>
        <p v-else-if="error" class="text-center text-danger">{{ error }}</p>
        <div v-else-if="!item" class="text-center text-secondary">Данные компании пока не заполнены.</div>
        <ContactsCompanyBody v-else :item="item" />
    </section>
</template>
