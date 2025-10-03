import { createI18n } from 'vue-i18n';
import en from './locales/en';
import fr from './locales/fr';
import nl from './locales/nl';

export const i18n = createI18n({
    legacy: false,
    locale: 'en', // default locale
    fallbackLocale: 'en',
    messages: {
        en,
        fr,
        nl,
    },
});

export default i18n;
