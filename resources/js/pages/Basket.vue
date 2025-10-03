<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import CartItem from '@/components/CartItem.vue';
import { onMounted, ref } from 'vue';
import { route } from 'ziggy-js';
import CartOverview from '@/components/CartOverview.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

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

const handleQuantityChanged = (updatedItem: any) => {
    const index = items.value.findIndex(item => item.id === updatedItem.id);
    if (index !== -1) {
        items.value[index] = updatedItem;
        // Recalculate cart count
        cartCount.value = items.value.reduce((sum, item) => sum + item.quantity, 0);
    }
};

const handleItemRemoved = (itemId: number) => {
    items.value = items.value.filter(item => item.id !== itemId);
    // Recalculate cart count
    cartCount.value = items.value.reduce((sum, item) => sum + item.quantity, 0);
};

onMounted(() => {
    getItems();
});

</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <h1 class="text-3xl font-bold">{{ t('basket') }}</h1>
            <p v-if="cartCount" class="text-muted-foreground">
                {{ cartCount }} {{ cartCount !== 1 ? t('items') : t('item') }} {{ t('in_your_basket') }}
            </p>
            <div class="flex gap-8 justify-center">
                <div v-if="items.length > 0" class="space-y-4">
                    <CartItem 
                        v-for="item in items" 
                        :key="item.id"
                        :item="item"
                        @quantity-changed="handleQuantityChanged"
                        @item-removed="handleItemRemoved"
                    />
                </div>
                <div v-else class="text-center py-8">
                    <p class="text-muted-foreground">{{ t('your_basket_is_empty') }}</p>
                </div>
                <CartOverview :items="items" v-if="items.length > 0" />
            </div>
        </div>
    </AppLayout>
</template>