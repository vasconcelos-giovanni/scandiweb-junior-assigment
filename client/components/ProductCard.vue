<template>
  <v-card
    :color="selected ? 'primary' : undefined"
    :variant="selected ? 'outlined' : 'outlined'"
    :class="selected ? 'border-primary' : ''"
    hover
  >
    <v-card-text class="pa-4 position-relative">
      <!-- Checkbox for mass delete selection -->
      <v-checkbox
        :model-value="selected"
        class="delete-checkbox position-absolute"
        style="top: 4px; left: 4px;"
        density="compact"
        hide-details
        @update:model-value="$emit('update:selected', $event ?? false)"
      />

      <!-- Product Information -->
      <div class="text-center pt-6">
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
      </div>
    </v-card-text>
  </v-card>
</template>

<script setup lang="ts">
import type { Product } from '~/types/product'

interface Props {
  product: Product
  selected?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  selected: false,
})

defineEmits<{
  'update:selected': [value: boolean]
}>()

/**
 * Format price to 2 decimal places
 */
function formatPrice(price: number): string {
  return price.toFixed(2)
}
</script>
