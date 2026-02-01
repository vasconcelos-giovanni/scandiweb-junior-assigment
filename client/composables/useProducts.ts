/**
 * Products Composable
 *
 * Provides reactive state management and API communication
 * for product operations in the Scandiweb Junior Assignment.
 */

import type {
    Product,
    CreateProductPayload,
    MassDeleteRequest,
    ApiErrorResponse,
} from '~/types/product'

/**
 * API response wrapper for products list
 */
interface ProductsApiResponse {
    data: Product[]
}

/**
 * API response wrapper for single product
 */
interface ProductApiResponse {
    data: Product
    message?: string
}

/**
 * Composable for managing products
 */
export function useProducts() {
    const config = useRuntimeConfig()
    const apiBaseUrl = config.public.apiBaseUrl

    // Reactive state
    const products = ref<Product[]>([])
    const loading = ref(false)
    const error = ref<string | null>(null)

    /**
     * Fetch all products from the API
     */
    async function fetchProducts(): Promise<void> {
        loading.value = true
        error.value = null

        try {
            const response = await $fetch<ProductsApiResponse>(`${apiBaseUrl}/products`, {
                method: 'GET',
            })

            products.value = response.data ?? response
        }
        catch (err) {
            const apiError = err as { data?: ApiErrorResponse }
            error.value = apiError.data?.message ?? 'Failed to fetch products'
            console.error('Error fetching products:', err)
        }
        finally {
            loading.value = false
        }
    }

    /**
     * Create a new product
     *
     * @param payload - The product data to create
     * @returns The created product or null on error
     */
    async function createProduct(payload: CreateProductPayload): Promise<Product | null> {
        loading.value = true
        error.value = null

        try {
            const response = await $fetch<ProductApiResponse>(`${apiBaseUrl}/products`, {
                method: 'POST',
                body: payload,
            })

            const newProduct = response.data ?? response
            return newProduct as Product
        }
        catch (err) {
            const apiError = err as { data?: ApiErrorResponse }
            error.value = apiError.data?.message ?? 'Failed to create product'

            // If there are field-specific errors, format them
            if (apiError.data?.errors) {
                const fieldErrors = Object.entries(apiError.data.errors)
                    .map(([field, msg]) => `${field}: ${msg}`)
                    .join(', ')
                error.value = fieldErrors
            }

            console.error('Error creating product:', err)
            return null
        }
        finally {
            loading.value = false
        }
    }

    /**
     * Mass delete products by IDs
     *
     * @param ids - Array of product IDs to delete
     * @returns True if successful, false otherwise
     */
    async function deleteProducts(ids: number[]): Promise<boolean> {
        if (ids.length === 0) {
            return true
        }

        loading.value = true
        error.value = null

        try {
            const payload: MassDeleteRequest = { ids }

            await $fetch(`${apiBaseUrl}/products`, {
                method: 'DELETE',
                body: payload,
            })

            // Remove deleted products from local state
            products.value = products.value.filter((p) => !ids.includes(p.id))

            return true
        }
        catch (err) {
            const apiError = err as { data?: ApiErrorResponse }
            error.value = apiError.data?.message ?? 'Failed to delete products'
            console.error('Error deleting products:', err)
            return false
        }
        finally {
            loading.value = false
        }
    }

    /**
     * Clear error state
     */
    function clearError(): void {
        error.value = null
    }

    return {
        // State
        products: readonly(products),
        loading: readonly(loading),
        error: readonly(error),

        // Actions
        fetchProducts,
        createProduct,
        deleteProducts,
        clearError,
    }
}
