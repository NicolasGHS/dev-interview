<script setup lang="ts">
import { formatPrice } from '@/lib/utils';
import { Product } from '@/types';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { Heart } from 'lucide-vue-next';
import Card from './ui/card/Card.vue';
import Button from './ui/button/Button.vue';

const { t } = useI18n();

interface Props {
    product: Product;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    productUnliked: [productId: number];
}>();

const isLiked = ref(false);
const isUpdatingLike = ref(false);

const navigateToProduct = (productId: number): void => {
    router.visit(`/product/${productId}`);
};

const checkIfLiked = async () => {
    try {
        const response = await fetch(route('api.likes.check'), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            credentials: 'include',
            body: JSON.stringify({
                product_ids: [props.product.id]
            }),
        });

        const data = await response.json();
        
        if (data.success) {
            isLiked.value = data.liked_product_ids.includes(props.product.id);
        }
    } catch (error) {
        console.error('Error checking like status:', error);
    }
};

const toggleLike = async (event: Event) => {
    event.stopPropagation(); // Prevent navigation when clicking heart
    
    if (isUpdatingLike.value) return;
    
    isUpdatingLike.value = true;
    
    try {
        const response = await fetch(route('likes.toggle', props.product.id), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            credentials: 'include',
        });

        const data = await response.json();
        
        if (data.success) {
            isLiked.value = data.liked;
            
            // If product was unliked, emit event for likes page
            if (!data.liked) {
                emit('productUnliked', props.product.id);
            }
        } else {
            console.error('Failed to toggle like:', data.message);
        }
    } catch (error) {
        console.error('Error toggling like:', error);
    } finally {
        isUpdatingLike.value = false;
    }
};

onMounted(() => {
    checkIfLiked();
});
</script>

<template>
    <Card class="overflow-hidden transition-all duration-200 hover:shadow-lg cursor-pointer group" @click="navigateToProduct(props.product.id)">
        <!-- Image Section -->
        <div class="relative">
            <div class="aspect-[4/3] overflow-hidden">
                <img 
                    :src="props.product.image" 
                    :alt="props.product.name" 
                    class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105" 
                />
            </div>
            
            <!-- Like button overlay -->
            <Button
                variant="ghost"
                size="sm"
                :disabled="isUpdatingLike"
                @click="toggleLike"
                class="absolute top-3 right-3 h-9 w-9 p-0 bg-white/90 backdrop-blur-sm hover:bg-white shadow-sm"
                :class="[
                    isLiked 
                        ? 'text-red-500 hover:text-red-600' 
                        : 'text-gray-600 hover:text-gray-800'
                ]"
                :title="isLiked ? t('remove_from_favorites') : t('add_to_favorites')"
            >
                <Heart 
                    :size="18" 
                    :class="[
                        'transition-all duration-200',
                        isLiked ? 'fill-current' : ''
                    ]"
                />
            </Button>
        </div>
        
        <!-- Content Section -->
        <div class="p-4 space-y-3">
            <div class="space-y-2">
                <h3 class="font-semibold text-lg leading-tight line-clamp-2">
                    {{ props.product.name }}
                </h3>
                <p class="text-sm text-muted-foreground line-clamp-2 leading-relaxed">
                    {{ props.product.description }} 
                </p>
            </div>
            
            <div class="pt-1">
                <span class="text-xl font-bold text-primary">
                    {{ formatPrice(props.product.price) }} 
                </span>
            </div>
        </div>
    </Card>
</template>