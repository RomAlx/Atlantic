<script setup>
import { computed } from 'vue';
import SocialIcon from './SocialIcon.vue';

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({}),
    },
});

const telHref = (number) => {
    if (!number || typeof number !== 'string') {
        return null;
    }
    const digits = number.replace(/[^\d+]/g, '');

    return digits.length ? digits : null;
};

const phoneRows = computed(() => {
    const list = props.settings?.phones;
    if (Array.isArray(list) && list.length > 0) {
        return list.filter((p) => p && typeof p.number === 'string' && p.number.trim().length > 0);
    }
    const legacy = props.settings?.phone;
    if (typeof legacy === 'string' && legacy.trim().length > 0) {
        return [{ label: '', number: legacy.trim() }];
    }

    return [];
});

const socialLinks = computed(() => {
    const list = props.settings?.social_links;
    if (Array.isArray(list) && list.length > 0) {
        return list.filter((row) => row && typeof row.url === 'string' && row.url.trim().length > 0);
    }
    const s = props.settings?.socials;
    if (!s || typeof s !== 'object') {
        return [];
    }

    return Object.entries(s)
        .filter(([, url]) => typeof url === 'string' && url.trim().length > 0)
        .map(([network, url]) => ({ network, label: network, url }));
});

const companyName = computed(() => props.settings?.company_name || 'Atlantic Group');
</script>

<template>
    <footer>
        <div class="container">
            <div class="row justify-content-center align-items-start gy-4 at_footer_main">
                <div class="col-lg-4 col-md-6 order-2 order-lg-1 text-center text-lg-start">
                    <div class="at_foot_p">
                        <p v-if="settings?.address">{{ settings.address }}</p>
                        <p v-if="settings?.warehouse_address" class="small text-secondary mb-2">
                            Склад: {{ settings.warehouse_address }}
                        </p>
                        <template v-for="(row, idx) in phoneRows" :key="'ph-' + idx">
                            <p class="mb-1">
                                <span v-if="row.label" class="small text-secondary d-block">{{ row.label }}</span>
                                <a :href="telHref(row.number) ? `tel:${telHref(row.number)}` : '#'">{{ row.number }}</a>
                            </p>
                        </template>
                        <p v-if="settings?.email">
                            <a :href="`mailto:${settings.email}`">{{ settings.email }}</a>
                        </p>
                        <div v-if="socialLinks.length" class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-start align-items-center mt-3">
                            <a
                                v-for="(row, idx) in socialLinks"
                                :key="'soc-' + idx"
                                :href="row.url"
                                class="at_social_link"
                                target="_blank"
                                rel="noopener noreferrer"
                                :title="row.label || row.network"
                            >
                                <SocialIcon :network="row.network || 'other'" :label="row.label || row.network" />
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 order-1 order-lg-2 text-center">
                    <div class="at_f_logo"><img :src="'/images/original/at_logo_footer.png'" :alt="companyName"></div>
                </div>
                <div class="col-lg-4 col-md-6 order-3 order-lg-3 text-center text-lg-end">
                    <div class="at_menu_foot d-inline-block text-start">
                        <ul>
                            <li><RouterLink to="/">Главная</RouterLink></li>
                            <li><RouterLink to="/about">О компании</RouterLink></li>
                            <li><RouterLink to="/catalog">Каталог</RouterLink></li>
                            <li><RouterLink to="/contacts">Контакты</RouterLink></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="at_foot_bottom">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-auto text-center">
                        <div class="at_copy">© {{ new Date().getFullYear() }} {{ companyName }}</div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>
