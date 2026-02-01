<template>
  <v-container>
    <!-- Page Header -->
    <div class="d-flex justify-space-between align-center flex-wrap ga-4 pb-4 mb-6" style="border-bottom: 1px solid rgba(0,0,0,0.12);">
      <h1 class="text-h4 font-weight-medium ma-0">
        Product List
      </h1>
      <div class="d-flex ga-3">
        <v-btn
          color="secondary"
          variant="outlined"
          @click="navigateToAdd"
        >
          ADD
        </v-btn>
        <v-btn
          id="delete-product-btn"
          color="error"
          variant="outlined"
          :disabled="selectedIds.length === 0"
          @click="handleMassDelete"
        >
          MASS DELETE
        </v-btn>
      </div>
    </div>

    <!-- Loading State -->
    <div
      v-if="loading"
      class="d-flex justify-center align-center py-16"
    >
      <v-progress-circular
        indeterminate
        color="primary"
        size="64"
      />
    </div>

    <!-- Products Grid -->
    <v-row
      v-else-if="products.length > 0"
    >
      <v-col
        v-for="product in products"
        :key="product.id"
        cols="12"
        sm="6"
        md="4"
        lg="3"
      >
        <ProductCard
          :product="product"
          :selected="selectedIds.includes(product.id)"
          @update:selected="toggleSelection(product.id, $event)"
        />
      </v-col>
    </v-row>

    <!-- Empty State -->
    <v-card
      v-else
      variant="outlined"
      class="pa-8 text-center"
    >
      <v-icon
        icon="mdi-package-variant"
        size="64"
        color="grey-lighten-1"
      />
      <div class="text-h6 mt-4 text-grey">
        No products found
      </div>
      <div class="text-body-2 mt-2 text-grey-darken-1">
        Click the ADD button to create your first product
      </div>
    </v-card>
  </v-container>
</template>

<script setup lang="ts">
// SEO
useHead({
  title: 'Product List',
})

// Router
const router = useRouter()

// Products composable
const { products, loading, fetchProducts, deleteProducts } = useProducts()

// Selected product IDs for mass delete
const selectedIds = ref<number[]>([])

/**
 * Toggle product selection
 */
function toggleSelection(id: number, selected: boolean): void {
  if (selected) {
    selectedIds.value.push(id)
  }
  else {
    selectedIds.value = selectedIds.value.filter((sid) => sid !== id)
  }
}

/**
 * Navigate to add product page
 */
function navigateToAdd(): void {
  router.push('/add-product')
}

/**
 * Handle mass delete action
 */
async function handleMassDelete(): Promise<void> {
  if (selectedIds.value.length === 0) {
    return
  }

  const success = await deleteProducts([...selectedIds.value])

  if (success) {
    selectedIds.value = []
  }
}

// Fetch products on mount
onMounted(() => {
  fetchProducts()
})
</script>
