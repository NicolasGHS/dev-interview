<script setup lang="ts">
import { formatPrice } from '@/lib/utils';
import { Product } from '@/types';
import { router } from '@inertiajs/vue3';
import Card from './ui/card/Card.vue';

interface Props {
    product: Product;
}

const props = defineProps<Props>();

const navigateToProduct = (productId: number): void => {
    router.visit(`/product/${productId}`);
};
</script>

<template>
    <Card class="h-72 flex flex-col justify-between space-y-2 p-4" @click="navigateToProduct(props.product.id)">
        <div class="flex-1 space-y-2">
            <h3 class="font-semibold text-lg">{{ props.product.name }}</h3>
            <img src="/default_boardgame_image.avif" alt="{{ props.product.name }}" class="w-full h-24 object-cover rounded" />
            <p class="text-sm text-muted-foreground line-clamp-3">
                {{ props.product.description }} 
            </p>
        </div>
        <div class="flex items-center justify-between mt-auto">
            <span class="text-2xl font-bold text-primary">
                {{ formatPrice(props.product.price) }} 
            </span>
        </div>
    </Card>
</template>