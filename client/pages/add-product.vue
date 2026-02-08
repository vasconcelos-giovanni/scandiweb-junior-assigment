<template>
    <DefaultTemplate titulo="Product Add">
        <!-- Action Buttons -->
        <div class="d-flex justify-end ga-3 mb-4">
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

        <!-- Product Form -->
        <v-form
            id="product_form"
            @submit.prevent="handleSave"
        >
            <v-row>
                <v-col cols="12" md="8" lg="6">
                    <!-- Common Fields -->
                    <v-text-field
                        id="sku"
                        v-model="formState.sku"
                        label="SKU"
                        placeholder="Enter product SKU"
                        :error-messages="errors.sku"
                        :disabled="loading"
                    />

                    <v-text-field
                        id="name"
                        v-model="formState.name"
                        label="Name"
                        placeholder="Enter product name"
                        :error-messages="errors.name"
                        :disabled="loading"
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
                    />

                    <!-- Type Switcher -->
                    <v-select
                        id="productType"
                        v-model="formState.type"
                        label="Type Switcher"
                        :items="productTypeItems"
                        item-title="text"
                        item-value="value"
                        :error-messages="errors.type"
                        :disabled="loading"
                    />

                    <!-- Type-Specific Fields (polymorphic — no conditionals) -->
                    <v-sheet rounded="lg" class="pa-4 mt-2">
                        <component
                            :is="currentTypeDefinition.component"
                            :form-state="formState"
                            :errors="errors"
                            :disabled="loading"
                        />
                    </v-sheet>

                    <!-- Error Alert -->
                    <v-snackbar
                        v-model="apiError"
                        color="error"
                        variant="tonal"
                        elevation="24"
                        :timeout="5000"
                        @update:model-value="(val) => !val && actions.clearError()"
                    >
                        {{ apiError }}
                        <template #actions>
                            <v-btn
                                variant="text"
                                @click="actions.clearError"
                            >
                            Close
                            </v-btn>
                        </template>
                    </v-snackbar>
                </v-col>
            </v-row>
        </v-form>
    </DefaultTemplate>
</template>

<script setup lang="ts">
import { CreateProductSchema, ProductFormSchema } from '~/schemas/ProductSchemas'
import { resolveProductType, getProductTypeItems, getAllTypeFieldKeys } from '~/utils/productTypeResolver'

const router = useRouter()

const {
    data: { error: apiError },
    actions,
} = useProducts()

const loading = ref(false)

const productTypeItems = getProductTypeItems()

const formState = reactive(itemVazio(ProductFormSchema))
const errors = reactive<Record<string, string>>({})

const currentTypeDefinition = computed(() => resolveProductType(formState.type))

function clearErrors(): void {
    Object.keys(errors).forEach(key => { errors[key] = '' })
}

async function handleSave(): Promise<void> {
    actions.clearError()
    clearErrors()

    const typeDefinition = currentTypeDefinition.value

    const validation = validateForm(CreateProductSchema, {
        sku: formState.sku,
        name: formState.name,
        price: formState.price ? parseFloat(formState.price) : undefined,
        type: formState.type,
        ...typeDefinition.parseFields(formState),
    })

    if (!validation.success) {
        Object.entries(validation.errors).forEach(([key, message]) => {
            errors[key] = message
        })
        return
    }

    try {
        loading.value = true

        const payload = {
            sku: formState.sku,
            name: formState.name,
            price: parseFloat(formState.price),
            ...typeDefinition.buildPayload(formState),
        }

        const success = await actions.createProduct(payload)
        if (success) router.push('/')
    }
    finally {
        loading.value = false
    }
}

function handleCancel(): void {
    router.push('/')
}

watch(
    () => formState.type,
    () => {
        getAllTypeFieldKeys().forEach(key => { errors[key] = '' })
    },
)
</script>
