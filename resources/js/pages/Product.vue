<script setup lang="ts">
import { Product, type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { type PageProps as InertiaPageProps } from '@inertiajs/core';
import { formatPrice } from '@/lib/utils';
import { route } from 'ziggy-js';
import { Heart } from 'lucide-vue-next';
import AddCardButton from '@/components/AddCardButton.vue';
import Button from '@/components/ui/button/Button.vue';

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

const isLiked = ref(false);
const isUpdatingLike = ref(false);

const checkIfLiked = async () => {
    try {
        const response = await fetch(route('api.likes.check'), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            credentials: 'include',
            body: JSON.stringify({
                product_ids: [product.value.id]
            }),
        });

        const data = await response.json();
        
        if (data.success) {
            isLiked.value = data.liked_product_ids.includes(product.value.id);
        }
    } catch (error) {
        console.error('Error checking like status:', error);
    }
};

const toggleLike = async () => {
    if (isUpdatingLike.value) return;
    
    isUpdatingLike.value = true;
    
    try {
        const response = await fetch(route('likes.toggle', product.value.id), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            credentials: 'include',
        });

        const data = await response.json();
        
        if (data.success) {
            isLiked.value = data.liked;
        } else {
            console.error('Failed to toggle like:', data.message);
        }
    } catch (error) {
        console.error('Error toggling like:', error);
    } finally {
        isUpdatingLike.value = false;
    }
};

onMounted(() => {
    checkIfLiked();
});

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
            :src="product.image" 
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
            <AddCardButton :id="product.id" />
            <Button
              variant="outline"
              size="lg"
              :disabled="isUpdatingLike"
              @click="toggleLike"
              :class="[
                'flex items-center justify-center p-3 transition-all duration-200',
                isLiked 
                  ? 'text-red-500 border-red-500 hover:bg-red-50' 
                  : 'text-muted-foreground hover:text-red-500 hover:border-red-500'
              ]"
              :title="isLiked ? 'Remove from Favorites' : 'Add to Favorites'"
            >
              <Heart 
                :size="20" 
                :class="[
                  'transition-all duration-200',
                  isLiked ? 'fill-current' : ''
                ]"
              />
            </Button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>