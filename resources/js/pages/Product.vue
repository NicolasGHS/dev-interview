<script setup lang="ts">
import { Product, type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { type PageProps as InertiaPageProps } from '@inertiajs/core';
import { formatPrice } from '@/lib/utils';
import AddCardButton from '@/components/AddCardButton.vue';
import SaveButton from '@/components/SaveButton.vue';

interface PageProps extends InertiaPageProps {
  product: Product;
}



const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const page = usePage<PageProps>();
const product = computed(() => page.props.product);

</script>

<template>
  <Head title="Product" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div v-if="product" class="max-w-6xl mx-auto p-6">
      <!-- Product Header -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Product Image -->
        <div class="space-y-4">
          <img 
            src="/default_boardgame_image.avif" 
            :alt="product.name" 
            class="w-full h-96 object-cover rounded-lg shadow-lg" 
          />
        </div>

        <!-- Product Details -->
        <div class="space-y-6">
          <div>
            <h1 class="text-3xl font-bold mb-2">{{ product.name }}</h1>
            <p class="text-4xl font-bold text-primary mb-4">{{ formatPrice(product.price) }}</p>
          </div>

          <div class="prose max-w-none">
            <h3 class="text-lg font-semibold mb-2">Description</h3>
            <p class="leading-relaxed">{{ product.description }}</p>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center gap-4 pt-6">
            <AddCardButton />
            <SaveButton />  
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>