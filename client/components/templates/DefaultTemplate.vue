<template>
    <v-container>
        <div class="d-flex align-center ga-2 mb-2">
            <v-btn
                v-if="showBack"
                icon="mdi-arrow-left"
                variant="text"
                size="small"
                @click="goBack"
            />
            <h1 :class="smAndDown ? 'text-h6' : 'text-h5'">
                {{ titulo }}
            </h1>
        </div>

        <v-divider class="mb-6"/>

        <slot/>
    </v-container>
</template>

<script setup lang="ts">
import { useDisplay } from 'vuetify'

const { smAndDown } = useDisplay()
const router = useRouter()

const props = withDefaults(
    defineProps<{
        titulo: string
        showBack?: boolean
    }>(),
    {
        showBack: true,
    },
)

useHead({ title: props.titulo })

function goBack(): void
{
    if (window.history.length > 1)
    {
        router.back()
    }
    else
    {
        router.push('/')
    }
}
</script>
