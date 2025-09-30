<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem, type ProductsResponse } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Pagination } from '@/components/ui/pagination';
import ProductCard from '@/components/ProductCard.vue';

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




const handlePageChange = (page: number): void => {
    router.visit(`/dashboard?page=${page}`, {
        preserveState: true,
        preserveScroll: true,
    });
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
                        class="rounded-lg border p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer hover:bg-slate-50"
                    >
                        <ProductCard :product="product" />
                    </div>
                </div>

                <!-- Pagination Component -->
                <div v-if="props.products.pagination.last_page > 1" class="mt-6 flex justify-center">
                    <Pagination
                        :current-page="props.products.pagination.current_page"
                        :total-pages="props.products.pagination.last_page"
                        @page-change="handlePageChange"
                    />
                </div>

                <!-- Pagination Info -->
                <div v-if="props.products.pagination.total > 0" class="mt-4 text-center">
                    <p class="text-sm text-muted-foreground">
                        Showing {{ props.products.pagination.from || 0 }} - 
                        {{ props.products.pagination.to || 0 }} of 
                        {{ props.products.pagination.total }} products
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
