<script setup lang="ts">
import NavbarLayout from '@/layouts/NavbarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <NavbarLayout class="container mx-auto px-4 py-6">
        <slot />
    </NavbarLayout>
</template>
