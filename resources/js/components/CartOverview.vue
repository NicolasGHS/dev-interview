<script setup lang="ts">
import Card from './ui/card/Card.vue';
import { formatPrice } from '@/lib/utils';
import { computed } from 'vue';
import CheckoutButton from './CheckoutButton.vue';

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
    items: CartItem[];
}

const props = withDefaults(defineProps<Props>(), {
    items: () => []
});

const totalPrice = computed(() => {
    return props.items.reduce((total, item) => {
        const itemPrice = parseFloat(item.product.price);
        return total + (itemPrice * item.quantity);
    }, 0);
});

const formattedTotalPrice = computed(() => {
    return formatPrice(totalPrice.value.toString());
});

</script>

<template>
    <Card class="p-4 w-1/3">
        <div class="space-y-4 flex flex-col">
            <h3 class="text-lg font-semibold">Order Summary</h3>
            
            <div v-if="props.items.length > 0" class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Items ({{ props.items.reduce((sum, item) => sum + item.quantity, 0) }})</span>
                    <span>{{ formattedTotalPrice }}</span>
                </div>
                
                <hr class="my-2" />
                
                <div class="flex justify-between font-semibold text-lg">
                    <span>Total</span>
                    <span class="text-primary">{{ formattedTotalPrice }}</span>
                </div>

            </div>
            
            <div v-else class="text-center text-muted-foreground py-4">
                <p>No items in cart</p>
            </div>
            <CheckoutButton />
        </div>
    </Card>
</template>