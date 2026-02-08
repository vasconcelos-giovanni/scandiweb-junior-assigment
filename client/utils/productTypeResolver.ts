import type { Component } from 'vue'
import type { ProductType, ProductFormState, CreateProductInput } from '~/schemas/ProductSchemas'
import DvdProduct from '~/components/products/DvdProduct.vue'
import BookProduct from '~/components/products/BookProduct.vue'
import FurnitureProduct from '~/components/products/FurnitureProduct.vue'

/**
 * Encapsulates all type-specific behavior for a product type.
 * This is the frontend equivalent of the backend's polymorphism pattern:
 * no if/else or switch/case — behavior is resolved via a map lookup.
 */
interface ProductTypeDefinition {
    /** Display label for the type switcher */
    label: string
    /** Vue component that renders the type-specific form fields */
    component: Component
    /** Parses string form values into typed data for Zod validation */
    parseFields: (form: ProductFormState) => Record<string, unknown>
    /** Builds the API payload from the validated form state */
    buildPayload: (form: ProductFormState) => Omit<CreateProductInput, 'sku' | 'name' | 'price'>
    /** Field keys owned by this type (cleared when switching types) */
    fieldKeys: string[]
}

/**
 * Product type resolver map — the single registry of all product types.
 *
 * Adding a new product type requires:
 * 1. A new Zod schema in ProductSchemas.ts
 * 2. A new Vue component in components/products/
 * 3. A new entry in this map
 *
 * No other files need conditionals.
 */
export const productTypeMap: Record<ProductType, ProductTypeDefinition> =
{
    dvd: {
        label: 'DVD',
        component: DvdProduct,
        parseFields: (form) => ({
            size: form.size ? parseInt(form.size, 10) : undefined,
        }),
        buildPayload: (form) => ({
            type: 'dvd' as const,
            size: parseInt(form.size, 10),
        }),
        fieldKeys: ['size'],
    },
    book: {
        label: 'Book',
        component: BookProduct,
        parseFields: (form) => ({
            weight: form.weight ? parseFloat(form.weight) : undefined,
        }),
        buildPayload: (form) => ({
            type: 'book' as const,
            weight: parseFloat(form.weight),
        }),
        fieldKeys: ['weight'],
    },
    furniture: {
        label: 'Furniture',
        component: FurnitureProduct,
        parseFields: (form) => ({
            height: form.height ? parseInt(form.height, 10) : undefined,
            width: form.width ? parseInt(form.width, 10) : undefined,
            length: form.length ? parseInt(form.length, 10) : undefined,
        }),
        buildPayload: (form) => ({
            type: 'furniture' as const,
            height: parseInt(form.height, 10),
            width: parseInt(form.width, 10),
            length: parseInt(form.length, 10),
        }),
        fieldKeys: ['height', 'width', 'length'],
    },
}

/**
 * Returns the product type definition for the given type.
 * Replaces all switch/case and if/else conditionals.
 */
export function resolveProductType(type: ProductType): ProductTypeDefinition {
    return productTypeMap[type]
}

/**
 * Returns the select items for the type switcher,
 * derived from the resolver map — no hardcoded arrays.
 */
export function getProductTypeItems(): { text: string; value: ProductType }[] {
    return Object.entries(productTypeMap).map(([value, def]) => ({
        text: def.label,
        value: value as ProductType,
    }))
}

/**
 * Returns all field keys from all product types.
 * Used to clear type-specific errors on type change.
 */
export function getAllTypeFieldKeys(): string[] {
    return Object.values(productTypeMap).flatMap(def => def.fieldKeys)
}
