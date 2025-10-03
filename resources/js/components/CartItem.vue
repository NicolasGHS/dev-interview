<script setup lang="ts">
import { formatPrice } from '@/lib/utils';
import { route } from 'ziggy-js';
import { ref } from 'vue';
import { Trash2 } from 'lucide-vue-next';
import Card from './ui/card/Card.vue';
import Button from './ui/button/Button.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

interface CartItem {
    id: number;
    quantity: number;
    product: {
        id: number;
        name: string;
        description: string | null;
        price: string;
    };
}

interface Props {
    item: CartItem;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    quantityChanged: [item: CartItem];
    itemRemoved: [itemId: number];
}>();

const isUpdating = ref(false);

const updateQuantity = async (action: 'increment' | 'decrement') => {
    if (isUpdating.value) return;
    
    isUpdating.value = true;
    
    try {
        const endpoint = action === 'increment' 
            ? route('cart.increment', props.item.id)
            : route('cart.decrement', props.item.id);
            
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
        const response = await fetch(endpoint, {
            method: 'PATCH',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken || '',
            },
            credentials: 'include',
        });

        const data = await response.json();
        
        if (data.success) {
            if (data.item_removed) {
                emit('itemRemoved', props.item.id);
            } else {
                emit('quantityChanged', data.item);
            }
        } else {
            console.error('Failed to update quantity:', data.message);
        }
    } catch (error) {
        console.error('Error updating quantity:', error);
    } finally {
        isUpdating.value = false;
    }
};

const incrementQuantity = () => updateQuantity('increment');
const decrementQuantity = () => updateQuantity('decrement');

const deleteItem = async () => {
    if (isUpdating.value) return;
    
    isUpdating.value = true;
    
    try {
        const endpoint = route('cart.remove', props.item.id);
        
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
        const response = await fetch(endpoint, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken || '',
            },
            credentials: 'include',
        });

        const data = await response.json();
        
        if (data.success) {
            emit('itemRemoved', props.item.id);
        } else {
            console.error('Failed to delete item:', data.message);
        }
    } catch (error) {
        console.error('Error deleting item:', error);
    } finally {
        isUpdating.value = false;
    }
};
</script>

<template>
    <Card class="p-4">
        <div class="flex gap-4">
            <!-- Product Image -->
            <div class="flex-shrink-0">
                <img 
                    src="/default_boardgame_image.avif" 
                    :alt="props.item.product.name" 
                    class="w-20 h-20 object-cover rounded-lg"
                />
            </div>
            
            <!-- Product Details -->
            <div class="flex-1 space-y-2">
                <div class="flex items-start justify-between">
                    <h3 class="font-semibold text-lg">{{ props.item.product.name }}</h3>
                    <Button
                        variant="ghost"
                        size="sm"
                        :disabled="isUpdating"
                        @click="deleteItem"
                        class="h-8 w-8 p-0 text-destructive hover:text-destructive hover:bg-destructive/10"
                        :title="t('remove_from_cart')"
                    >
                        <Trash2 :size="16" />
                    </Button>
                </div>
                <p 
                    v-if="props.item.product.description" 
                    class="text-sm text-muted-foreground line-clamp-2"
                >
                    {{ props.item.product.description }}
                </p>
                <div class="flex items-center justify-between">
                    <span class="text-lg font-bold text-primary">
                        {{ formatPrice(props.item.product.price) }}
                    </span>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-muted-foreground">{{ t('quantity') }}:</span>
                        <div class="flex items-center gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="isUpdating"
                                @click="decrementQuantity"
                                class="h-8 w-8 p-0"
                            >
                                -
                            </Button>
                            <span class="font-semibold min-w-[2rem] text-center">
                                {{ props.item.quantity }}
                            </span>
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="isUpdating"
                                @click="incrementQuantity"
                                class="h-8 w-8 p-0"
                            >
                                +
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Card>
</template>