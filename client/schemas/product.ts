/**
 * Zod Validation Schemas
 *
 * Using Zod 4.0.0 for form validation in the Product Add page.
 * Schemas match the API requirements from the Scandiweb Junior Assignment.
 */

import { z } from 'zod'

/**
 * Product type enum schema
 */
export const ProductTypeSchema = z.enum(['dvd', 'book', 'furniture'])

/**
 * Base product schema with common fields
 */
export const BaseProductSchema = z.object({
    sku: z
        .string()
        .min(1, 'SKU is required')
        .max(255, 'SKU must be at most 255 characters')
        .regex(/^[A-Za-z0-9_-]+$/, 'SKU must contain only letters, numbers, underscores, and hyphens'),
    name: z
        .string()
        .min(1, 'Name is required')
        .max(255, 'Name must be at most 255 characters'),
    price: z
        .number({ message: 'Price is required and must be a number' })
        .positive('Price must be a positive number')
        .multipleOf(0.01, 'Price must have at most 2 decimal places'),
    specific_attribute: z.string().optional(),
})

/**
 * DVD product schema
 */
export const DvdProductSchema = BaseProductSchema.extend({
    type: z.literal('dvd'),
    size: z
        .number({ message: 'Size is required and must be a number' })
        .int('Size must be a whole number')
        .positive('Size must be a positive number'),
})

/**
 * Book product schema
 */
export const BookProductSchema = BaseProductSchema.extend({
    type: z.literal('book'),
    weight: z
        .number({ message: 'Weight is required and must be a number' })
        .positive('Weight must be a positive number'),
})

/**
 * Furniture product schema
 */
export const FurnitureProductSchema = BaseProductSchema.extend({
    type: z.literal('furniture'),
    height: z
        .number({ message: 'Height is required and must be a number' })
        .int('Height must be a whole number')
        .positive('Height must be a positive number'),
    width: z
        .number({ message: 'Width is required and must be a number' })
        .int('Width must be a whole number')
        .positive('Width must be a positive number'),
    length: z
        .number({ message: 'Length is required and must be a number' })
        .int('Length must be a whole number')
        .positive('Length must be a positive number'),
})

/**
 * Discriminated union schema for all product types
 */
export const CreateProductSchema = z.discriminatedUnion('type', [
    DvdProductSchema,
    BookProductSchema,
    FurnitureProductSchema,
])

/**
 * Type inference from schemas
 */
export type DvdProductInput = z.infer<typeof DvdProductSchema>
export type BookProductInput = z.infer<typeof BookProductSchema>
export type FurnitureProductInput = z.infer<typeof FurnitureProductSchema>
export type CreateProductInput = z.infer<typeof CreateProductSchema>

/**
 * Form validation helper function
 *
 * @param formData - The form data to validate
 * @returns Object with success flag and either data or errors
 */
export function validateProductForm(formData: {
    sku: string
    name: string
    price: string
    type: 'dvd' | 'book' | 'furniture'
    size?: string
    weight?: string
    height?: string
    width?: string
    length?: string
}): {
    success: boolean
    data?: CreateProductInput
    errors?: Record<string, string>
} {
    // Parse numeric values
    const parsedData: Record<string, unknown> = {
        sku: formData.sku.trim(),
        name: formData.name.trim(),
        price: formData.price ? parseFloat(formData.price) : undefined,
        type: formData.type,
    }

    // Add type-specific fields
    if (formData.type === 'dvd') {
        parsedData.size = formData.size ? parseInt(formData.size, 10) : undefined
    }
    else if (formData.type === 'book') {
        parsedData.weight = formData.weight ? parseFloat(formData.weight) : undefined
    }
    else if (formData.type === 'furniture') {
        parsedData.height = formData.height ? parseInt(formData.height, 10) : undefined
        parsedData.width = formData.width ? parseInt(formData.width, 10) : undefined
        parsedData.length = formData.length ? parseInt(formData.length, 10) : undefined
    }

    const result = CreateProductSchema.safeParse(parsedData)

    if (result.success) {
        return { success: true, data: result.data }
    }

    // Convert Zod errors to a simple object
    const errors: Record<string, string> = {}
    for (const issue of result.error.issues) {
        const path = issue.path.join('.')
        if (!errors[path]) {
            errors[path] = issue.message
        }
    }

    return { success: false, errors }
}
