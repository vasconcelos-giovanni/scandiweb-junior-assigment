import {
    ProductSchema,
    type Product,
    type CreateProductInput,
}
    from '~/schemas/ProductSchemas'

export default function useProducts() {
    const { fetch } = useAPI()

    const data =
    {
        products: ref<Product[]>([]),
        error: ref<string | null>(null),
    }

    const actions =
    {
        async listProducts(): Promise<void> {
            try {
                const response = await fetch<{ data: Product[] }>('products', {
                    method: 'GET',
                })
                const rawData = response.data ?? response
                data.products.value = fullSafeParse(ProductSchema.array(), rawData)
            }
            catch (err: any) {
                data.error.value = err?.data?.message ?? 'Failed to fetch products'
            }
        },

        async createProduct(payload: CreateProductInput): Promise<boolean> {
            try {
                await fetch('products', {
                    method: 'POST',
                    body: payload,
                })
                return true
            }
            catch (err: any) {
                data.error.value = err?.data?.message ?? 'Failed to create product'

                if (err?.data?.errors) {
                    data.error.value = Object.entries(err.data.errors)
                        .map(([field, msg]) => `${field}: ${msg}`)
                        .join(', ')
                }

                return false
            }
        },

        async deleteProducts(ids: number[]): Promise<boolean> {
            if (ids.length === 0) return true

            try {
                await fetch('products', {
                    method: 'DELETE',
                    body: { ids },
                })

                data.products.value = data.products.value.filter(
                    p => !ids.includes(p.id),
                )
                return true
            }
            catch (err: any) {
                data.error.value = err?.data?.message ?? 'Failed to delete products'
                return false
            }
        },

        clearError(): void {
            data.error.value = null
        },
    }

    return { data, actions }
}
