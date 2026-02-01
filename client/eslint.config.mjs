// @ts-check
import withNuxt from './.nuxt/eslint.config.mjs'

export default withNuxt({
    rules: {
        // Vue specific rules
        'vue/multi-word-component-names': 'off',
        'vue/html-self-closing': ['error', {
            html: {
                void: 'always',
                normal: 'always',
                component: 'always',
            },
        }],
        'vue/no-v-html': 'off',

        // TypeScript rules
        '@typescript-eslint/no-explicit-any': 'warn',
        '@typescript-eslint/no-unused-vars': ['error', {
            argsIgnorePattern: '^_',
            varsIgnorePattern: '^_',
        }],

        // General rules
        'no-console': ['warn', { allow: ['warn', 'error'] }],
    },
})
