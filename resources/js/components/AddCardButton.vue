<script setup lang="ts">
import { route } from 'ziggy-js';
import Button from './ui/button/Button.vue';
import { useForm } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'


const props = defineProps(["id"])

const page = usePage();
const user = page.props.auth.user;

const form = useForm({
    user_id: user.id,
    product_id: props.id,
})


const addToCard = async () => {
    try {
        form.post(route('cart.add'))
    } catch (error) {
        console.error("Failed to add movie to cart: ", error); 
    }
}

</script>

<template>
    <Button @click="addToCard" class="flex-1 bg-primary text-primary-foreground hover:bg-primary/90 px-6 py-3 rounded-lg font-semibold transition-colors duration-200 hover:cursor-pointer">
        Add to Cart
    </Button>
</template>