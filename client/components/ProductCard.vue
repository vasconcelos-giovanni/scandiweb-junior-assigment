<template>
    <v-card
        :variant="selected ? 'tonal' : 'outlined'"
        :color="selected ? 'primary' : undefined"
        hover
    >
        <div class="d-flex justify-start pa-2 pb-0">
            <v-checkbox
                :model-value="selected"
                class="delete-checkbox"
                density="compact"
                hide-details
                @update:model-value="$emit('update:selected', $event ?? false)"
            />
        </div>

        <v-card-text class="text-center pt-0">
            <div class="text-subtitle-1 font-weight-bold">
                {{ product.sku }}
            </div>
            <div class="text-body-2 text-medium-emphasis mt-1">
                {{ product.name }}
            </div>
            <div class="text-body-2 text-medium-emphasis mt-1">
                {{ formatPrice(product.price) }} $
            </div>
            <div class="text-body-2 text-medium-emphasis mt-1">
                {{ product.specific_attribute }}
            </div>
        </v-card-text>
    </v-card>
</template>

<script setup lang="ts">
import type { Product } from '~/schemas/ProductSchemas'

withDefaults(
    defineProps<{
        product: Product
        selected?: boolean
    }>(),
    {
        selected: false,
    },
)

defineEmits<{
    'update:selected': [value: boolean]
}>()

function formatPrice(price: number): string
{
    return price.toFixed(2)
}
</script>
