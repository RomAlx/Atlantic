<script setup>
defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <nav v-if="items.length" class="at_breadcrumbs small mb-3" aria-label="Хлебные крошки">
        <ol class="at_breadcrumbs__list">
            <li v-for="(item, index) in items" :key="`${item.label}-${index}`" class="at_breadcrumbs__item">
                <RouterLink
                    v-if="item.to && index < items.length - 1"
                    :to="item.to"
                    class="at_breadcrumbs__link"
                >
                    {{ item.label }}
                </RouterLink>
                <span v-else class="at_breadcrumbs__current">{{ item.label }}</span>
            </li>
        </ol>
    </nav>
</template>
