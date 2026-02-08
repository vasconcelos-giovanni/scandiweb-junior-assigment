<template>
    <div>
        <!-- Action Buttons -->
        <div class="d-flex justify-end ga-3 mb-4">
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
                :loading="loading"
                @click="handleMassDelete"
            >
                MASS DELETE
            </v-btn>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="d-flex justify-center align-center py-16">
            <v-progress-circular indeterminate color="primary" size="64"/>
        </div>

        <!-- Products Grid -->
        <v-row v-else-if="products.length > 0">
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
        <v-card v-else variant="outlined" class="pa-8 text-center">
            <v-icon icon="mdi-package-variant" size="64" color="grey-lighten-1"/>
            <div class="text-h6 mt-4 text-grey">
                No products found
            </div>
            <div class="text-body-2 mt-2 text-grey-darken-1">
                Click the ADD button to create your first product
            </div>
        </v-card>
    </div>
</template>

<script setup lang="ts">
const router = useRouter()

const {
    data: { products },
    actions,
} = useProducts()

const selectedIds = ref<number[]>([])
const loading = ref(false)

function toggleSelection(id: number, selected: boolean): void
{
    if (selected)
    {
        selectedIds.value.push(id)
    }
    else
    {
        selectedIds.value = selectedIds.value.filter(sid => sid !== id)
    }
}

function navigateToAdd(): void
{
    router.push('/add-product')
}

async function handleMassDelete(): Promise<void>
{
    if (selectedIds.value.length === 0) return

    try
    {
        loading.value = true
        const success = await actions.deleteProducts([...selectedIds.value])
        if (success) selectedIds.value = []
    }
    finally
    {
        loading.value = false
    }
}

async function triggerListProducts(): Promise<void>
{
    try
    {
        loading.value = true
        await actions.listProducts()
    }
    finally
    {
        loading.value = false
    }
}

onBeforeMount(triggerListProducts)
</script>
