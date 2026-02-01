<template>
  <v-container class="main-content">
    <!-- Page Header -->
    <div class="page-header">
      <h1>Product Add</h1>
      <div class="header-actions">
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
      ref="formRef"
      class="product-form"
      @submit.prevent="handleSave"
    >
      <!-- Common Fields -->
      <div class="form-section">
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
      <div class="form-section">
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
      <div class="type-specific-fields">
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
          <div class="type-description">
            Please provide disc size in MB
          </div>
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
          <div class="type-description">
            Please provide book weight in KG
          </div>
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
          <div class="type-description">
            Please provide dimensions in HxWxL format
          </div>
        </template>
      </div>

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

<style scoped lang="scss">
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  flex-wrap: wrap;
  gap: 16px;
  border-bottom: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
  padding-bottom: 16px;

  h1 {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 500;
  }

  .header-actions {
    display: flex;
    gap: 12px;
  }
}

.product-form {
  max-width: 600px;

  .form-section {
    margin-bottom: 8px;
  }

  .type-specific-fields {
    background: rgba(var(--v-theme-surface-light), 1);
    padding: 16px;
    border-radius: 8px;
    margin-top: 8px;

    .type-description {
      font-size: 0.85rem;
      color: rgba(var(--v-theme-on-surface), 0.6);
      margin-top: 8px;
      font-style: italic;
    }
  }
}

@media (max-width: 600px) {
  .page-header {
    flex-direction: column;
    align-items: flex-start;

    .header-actions {
      width: 100%;
      justify-content: flex-end;
    }
  }
}
</style>
