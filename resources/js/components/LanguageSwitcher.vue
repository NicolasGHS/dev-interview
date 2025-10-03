<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';
import { Globe } from 'lucide-vue-next';
import Button from './ui/button/Button.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from './ui/dropdown-menu';

const { locale } = useI18n();
const isLoading = ref(false);

const languages = [
    { code: 'en', name: 'English', flag: '🇺🇸' },
    { code: 'fr', name: 'Français', flag: '🇫🇷' },
    { code: 'nl', name: 'Nederlands', flag: '🇳🇱' },
];

const currentLanguage = computed(() => {
    return languages.find(lang => lang.code === locale.value) || languages[0];
});

const switchLanguage = async (newLocale: string) => {
    if (isLoading.value || newLocale === locale.value) return;
    
    isLoading.value = true;
    
    try {
        const response = await fetch('/set-locale', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({ locale: newLocale }),
        });

        if (response.ok) {
            // Update the local i18n locale
            locale.value = newLocale as 'en' | 'fr' | 'nl';
            
            // Reload the page to get new translations from server
            router.reload();
        }
    } catch (error) {
        console.error('Failed to switch language:', error);
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="sm" class="gap-2" :disabled="isLoading">
                <Globe :size="16" />
                <span class="hidden sm:inline">{{ currentLanguage.flag }} {{ currentLanguage.name }}</span>
                <span class="sm:hidden">{{ currentLanguage.flag }}</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuItem
                v-for="language in languages"
                :key="language.code"
                as-child
            >
                <button
                    @click="switchLanguage(language.code)"
                    :class="[
                        'w-full text-left flex items-center cursor-pointer',
                        language.code === locale ? 'bg-accent' : ''
                    ]"
                >
                    <span class="mr-2">{{ language.flag }}</span>
                    {{ language.name }}
                </button>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>