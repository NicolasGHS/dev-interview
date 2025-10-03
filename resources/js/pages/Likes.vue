<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ProductCard from '@/components/ProductCard.vue';
import { onMounted, ref } from 'vue';
import { route } from 'ziggy-js';
import { Product } from '@/types';

const likedProducts = ref<Product[]>([]);
const likesCount = ref(0);
const isLoading = ref(true);

const getLikedProducts = async () => {
    try {
        const response = await fetch(route('api.likes'), {
            method: 'GET',
            headers: {
                Accept: 'application/json',
            },
            credentials: 'include',
        });

        const data = await response.json();
        
        if (data.success) {
            likedProducts.value = data.liked_products;
            likesCount.value = data.likes_count;
        }
    } catch (error) {
        console.error("Failed to fetch liked products: ", error);
    } finally {
        isLoading.value = false;
    }
};

const handleProductUnliked = (productId: number) => {
    likedProducts.value = likedProducts.value.filter(product => product.id !== productId);
    likesCount.value = likedProducts.value.length;
};

onMounted(() => {
    getLikedProducts();
});
</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold">My Favorites</h1>
                <div v-if="!isLoading" class="text-muted-foreground">
                    {{ likesCount }} item{{ likesCount !== 1 ? 's' : '' }}
                </div>
            </div>
            
            <div v-if="isLoading" class="flex justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            </div>
            
            <div v-else-if="likedProducts.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <ProductCard
                    v-for="product in likedProducts"
                    :key="product.id"
                    :product="product"
                    @product-unliked="handleProductUnliked"
                />
            </div>
            
            <div v-else class="text-center py-12">
                <div class="space-y-4">
                    <div class="text-6xl text-muted-foreground/50">♡</div>
                    <div>
                        <h3 class="text-lg font-semibold">No favorites yet</h3>
                        <p class="text-muted-foreground">
                            Start exploring products and add them to your favorites!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>