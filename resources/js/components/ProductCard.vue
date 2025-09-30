<script setup lang="ts">
import { formatPrice } from '@/lib/utils';
import { Product } from '@/types';
import { router } from '@inertiajs/vue3';

interface Props {
    product: Product;
}

const props = defineProps<Props>();

const navigateToProduct = (productId: number): void => {
    router.visit(`/product/${productId}`);
};

</script>

<template>
    <div class="space-y-2" @click="navigateToProduct(props.product.id)">
        <h3 class="font-semibold text-lg">{{ props.product.name }}</h3>
        <p class="text-sm text-muted-foreground line-clamp-3">
            {{ props.product.description }} 
        </p>
        <div class="flex items-center justify-between">
            <span class="text-2xl font-bold text-primary">
                {{ formatPrice(props.product.price) }} 
            </span>
        </div>
    </div>
</template>