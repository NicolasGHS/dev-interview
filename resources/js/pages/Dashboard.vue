<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem, type ProductsResponse } from '@/types';
import { Head, router } from '@inertiajs/vue3';

interface Props {
    products: ProductsResponse;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const formatPrice = (price: string): string => {
    return `$${parseFloat(price).toFixed(2)}`;
};

const navigateToProduct = (productId: number): void => {
    router.visit(`/product/${productId}`);
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid auto-rows-min gap-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold">Products</h1>
                    <p class="text-sm text-muted-foreground">
                        Showing {{ props.products.pagination.from || 0 }} - 
                        {{ props.products.pagination.to || 0 }} of 
                        {{ props.products.pagination.total }} products
                    </p>
                </div>
                
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div 
                        v-for="product in props.products.data" 
                        :key="product.id"
                        @click="navigateToProduct(product.id)"
                        class="rounded-lg border p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer hover:bg-slate-50"
                    >
                        <div class="space-y-2">
                            <h3 class="font-semibold text-lg">{{ product.name }}</h3>
                            <p class="text-sm text-muted-foreground line-clamp-3">
                                {{ product.description || 'No description available' }}
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-primary">
                                    {{ formatPrice(product.price) }}
                                </span>
                                <span class="text-xs text-muted-foreground">
                                    ID: {{ product.id }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination Info -->
                <div v-if="props.products.pagination.total > 0" class="mt-6 text-center">
                    <p class="text-sm text-muted-foreground">
                        Page {{ props.products.pagination.current_page }} of {{ props.products.pagination.last_page }}
                        <span v-if="props.products.pagination.has_more_pages">
                            • More pages available
                        </span>
                    </p>
                </div>

                <!-- Empty State -->
                <div v-if="props.products.data.length === 0" class="text-center py-8">
                    <p class="text-muted-foreground">No products found.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
