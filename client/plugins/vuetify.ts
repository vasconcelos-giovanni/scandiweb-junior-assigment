import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'

import { createVuetify } from 'vuetify'

export default defineNuxtPlugin((nuxtApp) => {
    const vuetify = createVuetify({
        theme: {
            defaultTheme: 'light',
            themes: {
                light: {
                    dark: false,
                    colors: {
                        background: '#FAFAFA',
                        surface: '#FFFFFF',
                        primary: '#1976D2',
                        secondary: '#424242',
                        error: '#D32F2F',
                        info: '#0288D1',
                        success: '#388E3C',
                        warning: '#FBC02D',
                    },
                },
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
