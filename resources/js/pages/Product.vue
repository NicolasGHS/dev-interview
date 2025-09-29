<script setup lang="ts">
import { Product, type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { type PageProps as InertiaPageProps } from '@inertiajs/core';

interface PageProps extends InertiaPageProps {
  product: Product;
}



const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const page = usePage<PageProps>();
const product = computed(() => page.props.product);

</script>

<template>
  <Head title="Product" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div v-if="product">
        <p>{{ product.name }}</p>
        <p>{{ product.description }}</p>
        <p>{{ product.price }}</p>
    </div>
  </AppLayout>
</template>