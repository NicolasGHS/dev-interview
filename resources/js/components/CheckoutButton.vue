<script setup lang="ts">
import Button from './ui/button/Button.vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref } from 'vue';

const isProcessing = ref(false);

const handleCheckout = async () => {
    if (isProcessing.value) return;
    
    isProcessing.value = true;
    
    try {
        // Clear the cart first
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        const response = await fetch(route('cart.clear'), {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken || '',
            },
            credentials: 'include',
        });

        console.log("response deleting: ", response);

        const data = await response.json();
        
        if (data.success) {
            // Cart cleared successfully, redirect to thank you page
            router.visit('/order-confirmation');
        } else {
            console.error('Failed to clear cart:', data.message);
            // Even if cart clearing fails, still redirect (order is "placed")
            router.visit('/order-confirmation');
        }
    } catch (error) {
        console.error('Error during checkout:', error);
        // Even if there's an error, still redirect (order is "placed")
        router.visit('/order-confirmation');
    } finally {
        isProcessing.value = false;
    }
};
</script>

<template>
    <Button @click="handleCheckout" :disabled="isProcessing">
        {{ isProcessing ? 'Processing...' : 'Checkout' }}
    </Button>
</template>
