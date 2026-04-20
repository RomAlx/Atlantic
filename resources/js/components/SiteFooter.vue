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

function parseContactAddresses(raw) {
    if (raw == null) {
        return [];
    }
    let list = raw;
    if (typeof list === 'string') {
        try {
            list = JSON.parse(list);
        } catch {
            return [];
        }
    }
    if (!Array.isArray(list)) {
        return [];
    }
    return list.filter((row) => row && typeof row.address === 'string' && row.address.trim().length > 0);
}

const addressLocations = computed(() => parseContactAddresses(props.settings?.contact_addresses));

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
                        <template v-for="(row, idx) in phoneRows" :key="'ph-' + idx">
                            <p class="mb-2 small fw-bold">
                                <template v-if="(row.label || '').trim()">
                                    {{ (row.label || '').trim() }} — <a :href="telHref(row.number) ? `tel:${telHref(row.number)}` : '#'">{{ row.number }}</a>
                                </template>
                                <template v-else>
                                    <a :href="telHref(row.number) ? `tel:${telHref(row.number)}` : '#'">{{ row.number }}</a>
                                </template>
                            </p>
                        </template>
                        <template v-for="(loc, idx) in addressLocations" :key="'addr-' + idx">
                            <p v-if="(loc.title || '').trim()" class="small mb-1 fw-bold">{{ (loc.title || '').trim() }}</p>
                            <p class="small mb-1">{{ loc.address }}</p>
                            <template v-if="Array.isArray(loc.phones) && loc.phones.length">
                                <p
                                    v-for="(lp, j) in loc.phones.filter((p) => p && p.number)"
                                    :key="'lp-' + idx + '-' + j"
                                    class="small"
                                >
                                    <template v-if="(lp.label || '').trim()">
                                        {{ (lp.label || '').trim() }} — <a :href="telHref(lp.number) ? `tel:${telHref(lp.number)}` : '#'">{{ lp.number }}</a>
                                    </template>
                                    <template v-else>
                                        <a :href="telHref(lp.number) ? `tel:${telHref(lp.number)}` : '#'">{{ lp.number }}</a>
                                    </template>
                                </p>
                            </template>
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
                            <li><RouterLink to="/support">Техподдержка</RouterLink></li>
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
