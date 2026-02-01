<template>
  <v-card
    class="product-card"
    :class="{ 'border-primary': selected }"
    variant="outlined"
  >
    <v-card-text class="pa-4 position-relative">
      <!-- Checkbox for mass delete selection -->
      <v-checkbox
        :model-value="selected"
        class="delete-checkbox product-checkbox"
        density="compact"
        hide-details
        @update:model-value="$emit('update:selected', $event ?? false)"
      />

      <!-- Product Information -->
      <div class="product-info">
        <div class="product-sku">
          {{ product.sku }}
        </div>
        <div class="product-name">
          {{ product.name }}
        </div>
        <div class="product-price">
          {{ formatPrice(product.price) }} $
        </div>
        <div class="product-attribute">
          {{ getSpecificAttribute() }}
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

/**
 * Get the type-specific attribute display string
 */
function getSpecificAttribute(): string {
  switch (props.product.type) {
    case 'dvd':
      return `Size: ${props.product.size} MB`
    case 'book':
      return `Weight: ${props.product.weight}KG`
    case 'furniture':
      return `Dimension: ${props.product.height}x${props.product.width}x${props.product.length}`
    default:
      return ''
  }
}
</script>

<style scoped lang="scss">
.product-card {
  position: relative;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;

  &:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  &.border-primary {
    border-color: rgb(var(--v-theme-primary));
    border-width: 2px;
  }
}

.product-checkbox {
  position: absolute;
  top: 4px;
  left: 4px;
}

.product-info {
  text-align: center;
  padding-top: 24px;

  .product-sku {
    font-weight: 600;
    font-size: 0.95rem;
    color: rgb(var(--v-theme-on-surface));
  }

  .product-name {
    font-size: 0.9rem;
    color: rgba(var(--v-theme-on-surface), 0.7);
    margin-top: 4px;
  }

  .product-price {
    font-size: 0.85rem;
    color: rgba(var(--v-theme-on-surface), 0.6);
    margin-top: 4px;
  }

  .product-attribute {
    font-size: 0.85rem;
    color: rgba(var(--v-theme-on-surface), 0.6);
    margin-top: 4px;
  }
}
</style>
