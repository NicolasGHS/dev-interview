<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import { router } from '@inertiajs/vue3';

const goToHome = () => {
    router.visit('/');
};

const goToBasket = () => {
    router.visit('/basket');
};
</script>

<template>
    <Head title="Order Confirmation" />
    
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
        <Card class="max-w-md w-full p-8 text-center">
            <div class="space-y-6">
                <!-- Success Icon -->
                <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                
                <!-- Thank You Message -->
                <div class="space-y-2">
                    <h1 class="text-2xl font-bold text-gray-900">Thank You!</h1>
                    <p class="text-gray-600">Your order has been successfully placed.</p>
                </div>
                
                <!-- Order Details -->
                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                    <p class="text-sm text-gray-600">Order Number:</p>
                    <p class="font-mono text-lg font-semibold">#{{ Math.random().toString(36).substr(2, 9).toUpperCase() }}</p>
                </div>
                
                <!-- Additional Info -->
                <div class="text-sm text-gray-600 space-y-1">
                    <p>You will receive an email confirmation shortly.</p>
                    <p>We'll notify you when your order is shipped.</p>
                </div>
                
                <!-- Action Buttons -->
                <div class="space-y-3">
                    <Button @click="goToHome" class="w-full">
                        Continue Shopping
                    </Button>
                    <Button @click="goToBasket" variant="outline" class="w-full">
                        View Cart
                    </Button>
                </div>
            </div>
        </Card>
    </div>
</template>