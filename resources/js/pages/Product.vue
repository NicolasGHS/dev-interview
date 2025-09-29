<script setup lang="ts">
import { Product, type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';

const product: Product = ref(null);


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const getProduct = async () => {
    try {
        const result = await axios.get(route("product.getById", { id: 1 }));
        console.log("product:", result.data.data);
        product.value = result.data.data;
    } catch (error) {
        console.error("Failed to fetch product: ", error); 
    }
}

onMounted(getProduct);
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