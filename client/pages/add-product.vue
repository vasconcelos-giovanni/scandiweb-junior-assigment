<template>
  <v-container>
    <!-- Page Header -->
    <div class="d-flex justify-space-between align-center flex-wrap ga-4 pb-4 mb-6" style="border-bottom: 1px solid rgba(0,0,0,0.12);">
      <h1 class="text-h4 font-weight-medium ma-0">
        Product Add
      </h1>
      <div class="d-flex ga-3">
        <v-btn
          color="primary"
          variant="outlined"
          :loading="loading"
          :disabled="loading"
          @click="handleSave"
        >
          Save
        </v-btn>
        <v-btn
          color="secondary"
          variant="outlined"
          :disabled="loading"
          @click="handleCancel"
        >
          Cancel
        </v-btn>
      </div>
    </div>

    <!-- Product Form -->
    <v-form
      id="product_form"
      style="max-width: 600px;"
      @submit.prevent="handleSave"
    >
      <!-- Common Fields -->
      <div class="mb-2">
        <v-text-field
          id="sku"
          v-model="formState.sku"
          label="SKU"
          placeholder="Enter product SKU"
          :error-messages="errors.sku"
          :disabled="loading"
          required
        />

        <v-text-field
          id="name"
          v-model="formState.name"
          label="Name"
          placeholder="Enter product name"
          :error-messages="errors.name"
          :disabled="loading"
          required
        />

        <v-text-field
          id="price"
          v-model="formState.price"
          label="Price ($)"
          placeholder="Enter product price"
          type="number"
          step="0.01"
          min="0"
          :error-messages="errors.price"
          :disabled="loading"
          required
        />
      </div>

      <!-- Type Switcher -->
      <div class="mb-2">
        <v-select
          id="productType"
          v-model="formState.type"
          label="Type Switcher"
          :items="productTypes"
          item-title="text"
          item-value="value"
          :error-messages="errors.type"
          :disabled="loading"
          required
        />
      </div>

      <!-- Type-Specific Fields -->
      <v-sheet
        color="grey-lighten-4"
        rounded="lg"
        class="pa-4 mt-2"
      >
        <!-- DVD Fields -->
        <template v-if="formState.type === 'dvd'">
          <v-text-field
            id="size"
            v-model="formState.size"
            label="Size (MB)"
            placeholder="Enter size in MB"
            type="number"
            min="1"
            step="1"
            :error-messages="errors.size"
            :disabled="loading"
            required
          />
          <p class="text-body-2 text-medium-emphasis font-italic mt-2">
            Please provide disc size in MB
          </p>
        </template>

        <!-- Book Fields -->
        <template v-if="formState.type === 'book'">
          <v-text-field
            id="weight"
            v-model="formState.weight"
            label="Weight (KG)"
            placeholder="Enter weight in KG"
            type="number"
            min="0.01"
            step="0.01"
            :error-messages="errors.weight"
            :disabled="loading"
            required
          />
          <p class="text-body-2 text-medium-emphasis font-italic mt-2">
            Please provide book weight in KG
          </p>
        </template>

        <!-- Furniture Fields -->
        <template v-if="formState.type === 'furniture'">
          <v-text-field
            id="height"
            v-model="formState.height"
            label="Height (CM)"
            placeholder="Enter height in CM"
            type="number"
            min="1"
            step="1"
            :error-messages="errors.height"
            :disabled="loading"
            required
          />

          <v-text-field
            id="width"
            v-model="formState.width"
            label="Width (CM)"
            placeholder="Enter width in CM"
            type="number"
            min="1"
            step="1"
            :error-messages="errors.width"
            :disabled="loading"
            required
          />

          <v-text-field
            id="length"
            v-model="formState.length"
            label="Length (CM)"
            placeholder="Enter length in CM"
            type="number"
            min="1"
            step="1"
            :error-messages="errors.length"
            :disabled="loading"
            required
          />
          <p class="text-body-2 text-medium-emphasis font-italic mt-2">
            Please provide dimensions in HxWxL format
          </p>
        </template>
      </v-sheet>

      <!-- Error Alert -->
      <v-alert
        v-if="apiError"
        type="error"
        variant="tonal"
        closable
        class="mt-4"
        @click:close="clearError"
      >
        {{ apiError }}
      </v-alert>
    </v-form>
  </v-container>
</template>

<script setup lang="ts">
import type { ProductFormState, ProductType, CreateProductPayload } from '~/types/product'
import { validateProductForm } from '~/schemas/product'

// SEO
useHead({
  title: 'Product Add',
})

// Router
const router = useRouter()

// Products composable
const { loading, error: apiError, createProduct, clearError } = useProducts()

// Product type options
const productTypes = [
  { text: 'DVD', value: 'dvd' as const },
  { text: 'Book', value: 'book' as const },
  { text: 'Furniture', value: 'furniture' as const },
]

// Form state
const formState = reactive<ProductFormState>({
  sku: '',
  name: '',
  price: '',
  type: 'dvd',
  size: '',
  weight: '',
  height: '',
  width: '',
  length: '',
})

// Validation errors
const errors = reactive<Record<string, string>>({})

/**
 * Clear all validation errors
 */
function clearErrors(): void {
  Object.keys(errors).forEach((key) => {
    errors[key] = ''
  })
}

/**
 * Set validation errors from Zod result
 */
function setErrors(validationErrors: Record<string, string>): void {
  clearErrors()
  Object.entries(validationErrors).forEach(([key, message]) => {
    errors[key] = message
  })
}

/**
 * Build the API payload based on product type
 */
function buildPayload(): CreateProductPayload {
  const base = {
    sku: formState.sku,
    name: formState.name,
    price: parseFloat(formState.price),
  }

  switch (formState.type) {
    case 'dvd':
      return {
        ...base,
        type: 'dvd' as const,
        size: parseInt(formState.size, 10),
      }
    case 'book':
      return {
        ...base,
        type: 'book' as const,
        weight: parseFloat(formState.weight),
      }
    case 'furniture':
      return {
        ...base,
        type: 'furniture' as const,
        height: parseInt(formState.height, 10),
        width: parseInt(formState.width, 10),
        length: parseInt(formState.length, 10),
      }
  }
}

/**
 * Handle form save
 */
async function handleSave(): Promise<void> {
  clearError()
  clearErrors()

  // Validate form with Zod
  const validation = validateProductForm({
    sku: formState.sku,
    name: formState.name,
    price: formState.price,
    type: formState.type as ProductType,
    size: formState.size,
    weight: formState.weight,
    height: formState.height,
    width: formState.width,
    length: formState.length,
  })

  if (!validation.success) {
    setErrors(validation.errors ?? {})
    return
  }

  // Build and send payload
  const payload = buildPayload()
  const product = await createProduct(payload)

  if (product) {
    // Navigate back to product list on success
    router.push('/')
  }
}

/**
 * Handle form cancel
 */
function handleCancel(): void {
  router.push('/')
}

// Clear type-specific fields when type changes
watch(
  () => formState.type,
  () => {
    // Clear type-specific errors when type changes
    errors.size = ''
    errors.weight = ''
    errors.height = ''
    errors.width = ''
    errors.length = ''
  },
)
</script>
