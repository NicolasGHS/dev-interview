<script setup lang="ts">
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import AppLayout from '@/layouts/AppLayout.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
  <Head title="Product" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div>
        <p>product</p>
    </div>
  </AppLayout>
</template>