<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { onMounted, ref } from 'vue';
import { route } from 'ziggy-js';

// TODO: change any type
const items = ref<any[]>([]);
const cartCount = ref(0);

const getItems = async () => {
    try {
        const response = await fetch(route('cart.items'), {
            method: 'GET',
            headers: {
                Accept: 'application/json',
            },
            credentials: 'include',
        });


        const data = await response.json();
        items.value = data.cart_items;
        cartCount.value = data.cart_count;
    } catch (error) {
        console.error("Failed to fetch items: ", error);
    }
}

onMounted(() => {
    getItems();
});

</script>

<template>
    <AppLayout>
        <div>
            <h1>Basket</h1>
            <p v-if="cartCount">{{ cartCount }}</p>
            <div v-for="item in items" :key="item.id">
                <p>{{ item.product.name }}</p>
            </div>
        </div>
    </AppLayout>
</template>