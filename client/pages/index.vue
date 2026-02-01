<template>
  <v-container class="main-content">
    <!-- Page Header -->
    <div class="page-header">
      <h1>Product List</h1>
      <div class="header-actions">
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
    <div
      v-else-if="products.length > 0"
      class="product-grid"
    >
      <ProductCard
        v-for="product in products"
        :key="product.id"
        :product="product"
        :selected="selectedIds.includes(product.id)"
        @update:selected="toggleSelection(product.id, $event)"
      />
    </div>

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

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 16px;
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

  .product-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  }
}
</style>
