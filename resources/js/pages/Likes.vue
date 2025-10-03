<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ProductCard from '@/components/ProductCard.vue';
import { onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import { Product } from '@/types';

const { t } = useI18n();
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
                <h1 class="text-3xl font-bold">{{ t('favorites') }}</h1>
                <div v-if="!isLoading" class="text-muted-foreground">
                    {{ likesCount }} {{ likesCount !== 1 ? t('items') : t('item') }}
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
                        <h3 class="text-lg font-semibold">{{ t('no_favorites_yet') }}</h3>
                        <p class="text-muted-foreground">
                            {{ t('start_exploring') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>