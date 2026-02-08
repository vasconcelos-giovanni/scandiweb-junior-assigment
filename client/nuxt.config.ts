import vuetify, { transformAssetUrls } from 'vite-plugin-vuetify'

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
    compatibilityDate: '2025-01-31',
    ssr: false,
    devtools: { enabled: true },

    build: {
        transpile: ['vuetify'],
    },

    modules: [
        '@nuxt/eslint',
        (_options, nuxt) => {
            nuxt.hooks.hook('vite:extendConfig', (config) => {
                config.plugins!.push(vuetify({ autoImport: true }))
            })
        },
    ],

    components: [
        {
            path: '~/components',
            pathPrefix: false,
        },
    ],

    imports: {
        dirs: [
            'composables/**',
            'utils/**',
        ],
    },

    vite: {
        vue: {
            template: {
                transformAssetUrls,
            },
        },
    },

    runtimeConfig: {
        public: {
            apiBaseUrl: process.env.NUXT_PUBLIC_API_BASE_URL || 'http://localhost:8000',
        },
    },

    router: {
        options: {
            strict: false,
        },
    },

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
