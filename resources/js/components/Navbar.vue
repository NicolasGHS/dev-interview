<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import UserMenuContent from '@/components/UserMenuContent.vue';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import { getInitials } from '@/composables/useInitials';
import { cn } from '@/lib/utils';
import { basket, dashboard } from '@/routes';
import type { User } from '@/types';
import { Link, usePage, router } from '@inertiajs/vue3';
import { Search, ShoppingCart, Heart } from 'lucide-vue-next';
import { computed, ref, watch, onMounted } from 'vue';

interface Props {
    class?: string;
}

const props = defineProps<Props>();

const page = usePage();
const user = computed(() => page.props.auth?.user as User | undefined);

const searchQuery = ref('');

// Initialize search query from URL when on dashboard
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');
    if (searchParam && window.location.pathname === '/dashboard') {
        searchQuery.value = searchParam;
    }
});

const handleSearch = () => {
    const params = new URLSearchParams();
    if (searchQuery.value.trim()) {
        params.append('search', searchQuery.value.trim());
    }
    
    const url = searchQuery.value.trim() ? `/dashboard?${params.toString()}` : '/dashboard';
    router.visit(url, {
        preserveState: true,
        preserveScroll: false,
    });
};

// Debounce search for real-time updates
let searchTimeout: number;
watch(searchQuery, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        if (window.location.pathname === '/dashboard') {
            handleSearch();
        }
    }, 300);
});
</script>

<template>
    <nav 
        :class="cn(
            'flex h-16 w-full items-center justify-between border-b bg-background px-4 shadow-sm',
            props.class
        )"
    >
        <!-- Left side - Logo/Brand -->
        <div class="flex items-center space-x-4">
            <Link 
                :href="dashboard()" 
                class="flex items-center space-x-2 font-semibold text-foreground hover:text-primary transition-colors"
            >
                <div class="h-8 w-8 rounded-lg bg-primary flex items-center justify-center">
                    <span class="text-primary-foreground text-sm font-bold">S</span>
                </div>
                <span class="hidden sm:inline-block">Store</span>
            </Link>
        </div>

        <!-- Center - Search Bar -->
        <div class="flex-1 max-w-md mx-4">
            <div class="relative">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    v-model="searchQuery"
                    placeholder="Search products..."
                    class="pl-10 w-full"
                    @keydown.enter="handleSearch"
                />
            </div>
        </div>

        <!-- Right side - Actions and User -->
        <div class="flex items-center space-x-2">
            <!-- Language Switcher -->
            <LanguageSwitcher />
            
            <!-- Favorites Button -->
            <Button variant="ghost" size="sm" as-child>
                <Link href="/likes" class="relative">
                    <Heart class="h-5 w-5" />
                    <span class="sr-only">Favorites</span>
                </Link>
            </Button>

            <!-- Basket Button -->
            <Button variant="ghost" size="sm" as-child>
                <Link :href="basket()" class="relative">
                    <ShoppingCart class="h-5 w-5" />
                    <!-- Optional: Badge for cart count -->
                    <!-- <Badge class="absolute -top-2 -right-2 h-5 w-5 rounded-full p-0 text-xs">
                        0
                    </Badge> -->
                    <span class="sr-only">Shopping cart</span>
                </Link>
            </Button>

            <!-- User Profile Dropdown -->
            <DropdownMenu v-if="user">
                <DropdownMenuTrigger as-child>
                    <Button variant="ghost" class="relative h-9 w-9 rounded-full p-0 hover:cursor-pointer">
                        <Avatar class="h-9 w-9">
                            <AvatarImage
                                v-if="user.avatar"
                                :src="user.avatar"
                                :alt="user.name"
                            />
                            <AvatarFallback class="bg-primary text-primary-foreground">
                                {{ getInitials(user.name) }}
                            </AvatarFallback>
                        </Avatar>
                        <span class="sr-only">User menu</span>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-56">
                    <UserMenuContent :user="user" />
                </DropdownMenuContent>
            </DropdownMenu>

            <!-- Login/Register buttons for guests -->
            <div v-else class="flex items-center space-x-2">
                <Button variant="ghost" size="sm" as-child>
                    <Link href="/login">
                        Log in
                    </Link>
                </Button>
                <Button size="sm" as-child>
                    <Link href="/register">
                        Sign up
                    </Link>
                </Button>
            </div>
        </div>
    </nav>
</template>