<script setup lang="ts">
import { route } from 'ziggy-js';
import Button from './ui/button/Button.vue';
import { usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { ShoppingCart, Check } from 'lucide-vue-next';

const props = defineProps(["id"]);

const page = usePage();
const user = page.props.auth.user;
const isAdding = ref(false);
const isChecking = ref(false);
const inCart = ref(false);
const quantity = ref(0);

const checkCartStatus = async () => {
    if (isChecking.value) return;
    
    isChecking.value = true;
    console.log('Checking cart status for product:', props.id);
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        const response = await fetch(route('cart.check'), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken || '',
            },
            credentials: 'include',
            body: JSON.stringify({
                product_id: props.id,
            }),
        });

        console.log('Cart check response status:', response.status);
        const data = await response.json();
        console.log('Cart check response data:', data);
        
        if (data.success) {
            inCart.value = data.in_cart;
            quantity.value = data.quantity;
            console.log('Updated cart status:', { inCart: inCart.value, quantity: quantity.value });
        } else {
            console.error('Cart check failed:', data.message);
        }
    } catch (error) {
        console.error("Failed to check cart status:", error);
    } finally {
        isChecking.value = false;
    }
};

const addToCard = async () => {
    if (isAdding.value || inCart.value) return;
    
    isAdding.value = true;
    
    try {
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        const response = await fetch(route('cart.add'), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken || '',
            },
            credentials: 'include',
            body: JSON.stringify({
                user_id: user.id,
                product_id: props.id,
            }),
        });

        const data = await response.json();
        
        if (data.success) {
            console.log('Item added to cart successfully');
            // Update local state to reflect the item is now in cart
            inCart.value = true;
            quantity.value = data.item.quantity;
        } else {
            console.error('Failed to add item to cart:', data.message);
        }
    } catch (error) {
        console.error("Failed to add item to cart:", error); 
    } finally {
        isAdding.value = false;
    }
};

onMounted(() => {
    console.log('AddCardButton mounted for product:', props.id);
    checkCartStatus();
});
</script>

<template>
    <Button 
        @click="addToCard" 
        :disabled="isAdding || inCart || isChecking"
        :class="[
            'flex-1 px-6 py-3 rounded-lg font-semibold transition-all duration-200 hover:cursor-pointer flex items-center justify-center gap-2',
            inCart 
                ? 'bg-green-600 text-white hover:bg-green-700' 
                : 'bg-primary text-primary-foreground hover:bg-primary/90'
        ]"
    >
        <component 
            :is="inCart ? Check : ShoppingCart" 
            :size="20" 
        />
        <span v-if="isChecking">
            Loading...
        </span>
        <span v-else-if="isAdding">
            Adding...
        </span>
        <span v-else-if="inCart">
            In Cart ({{ quantity }})
        </span>
        <span v-else>
            Add to Cart
        </span>
    </Button>
</template>