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
    <Card class="h-72 flex flex-col justify-between space-y-2 p-4 relative" @click="navigateToProduct(props.product.id)">
        <!-- Like button -->
        <Button
            variant="ghost"
            size="sm"
            :disabled="isUpdatingLike"
            @click="toggleLike"
            class="absolute top-2 right-2 h-8 w-8 p-0 z-10"
            :class="[
                isLiked 
                    ? 'text-red-500 hover:text-red-600' 
                    : 'text-muted-foreground hover:text-foreground'
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