import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'

import { createVuetify, type ThemeDefinition } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

/**
 * Custom light theme following Vuetify conventions
 * for primary, secondary, info, success, warning, error colors
 */
const lightTheme: ThemeDefinition = {
    dark: false,
    colors: {
        background: '#FAFAFA',
        surface: '#FFFFFF',
        'surface-bright': '#FFFFFF',
        'surface-light': '#EEEEEE',
        'surface-variant': '#424242',
        'on-surface-variant': '#EEEEEE',
        primary: '#1976D2',
        'primary-darken-1': '#1565C0',
        secondary: '#424242',
        'secondary-darken-1': '#212121',
        error: '#D32F2F',
        info: '#0288D1',
        success: '#388E3C',
        warning: '#FBC02D',
    },
}

export default defineNuxtPlugin((nuxtApp) => {
    const vuetify = createVuetify({
        components,
        directives,
        theme: {
            defaultTheme: 'light',
            themes: {
                light: lightTheme,
            },
        },
        defaults: {
            VBtn: {
                variant: 'elevated',
            },
            VTextField: {
                variant: 'outlined',
                density: 'comfortable',
            },
            VSelect: {
                variant: 'outlined',
                density: 'comfortable',
            },
            VCard: {
                elevation: 2,
            },
        },
    })

    nuxtApp.vueApp.use(vuetify)
})
