import vuetify, { transformAssetUrls } from 'vite-plugin-vuetify'

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
    compatibilityDate: '2025-01-31',
    devtools: { enabled: true },

    // Build transpile for Vuetify
    build: {
        transpile: ['vuetify'],
    },

    // Vuetify module integration
    modules: [
        '@nuxt/eslint',
        (_options, nuxt) => {
            nuxt.hooks.hook('vite:extendConfig', (config) => {
                // @ts-expect-error - vite-plugin-vuetify type issue
                config.plugins.push(vuetify({ autoImport: true }))
            })
        },
    ],

    // Vite configuration
    vite: {
        vue: {
            template: {
                transformAssetUrls,
            },
        },
    },

    // CSS configuration
    css: ['~/assets/styles/main.scss'],

    // Runtime configuration
    runtimeConfig: {
        public: {
            apiBaseUrl: process.env.NUXT_PUBLIC_API_BASE_URL || 'http://localhost:8000',
        },
    },

    // Router configuration
    router: {
        options: {
            strict: false,
        },
    },

    // App configuration
    app: {
        head: {
            title: 'Scandiweb Test Assignment',
            meta: [
                { charset: 'utf-8' },
                { name: 'viewport', content: 'width=device-width, initial-scale=1' },
                { name: 'description', content: 'Product Management System - Scandiweb Junior Developer Test Assignment' },
            ],
            link: [
                { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
            ],
        },
    },
})
