<script setup lang="ts">
import Navbar from '@/components/Navbar.vue';

interface Props {
    class?: string;
}

const props = defineProps<Props>();
</script>

<template>
    <div class="min-h-screen bg-background">
        <Navbar />
        <main :class="props.class">
            <slot />
        </main>
    </div>
</template>