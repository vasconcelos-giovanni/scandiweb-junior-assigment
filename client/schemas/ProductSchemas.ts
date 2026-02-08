import { z } from 'zod'

// === Enums ===

export const ProductTypeSchema = z.enum(['dvd', 'book', 'furniture'])

// === Product Listing Schema (API response shape) ===

export const ProductSchema = z.object({
    id: z.number().int().nonnegative(),
    sku: z.string(),
    name: z.string(),
    price: z.coerce.number().nonnegative(),
    type: ProductTypeSchema,
    specific_attribute: z.string(),
})

// === Form Validation Schemas ===

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
})

export const DvdProductSchema = BaseProductSchema.extend({
    type: z.literal('dvd'),
    size: z
        .number({ message: 'Size is required and must be a number' })
        .int('Size must be a whole number')
        .positive('Size must be a positive number'),
})

export const BookProductSchema = BaseProductSchema.extend({
    type: z.literal('book'),
    weight: z
        .number({ message: 'Weight is required and must be a number' })
        .positive('Weight must be a positive number'),
})

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

export const CreateProductSchema = z.discriminatedUnion('type', [
    DvdProductSchema,
    BookProductSchema,
    FurnitureProductSchema,
])

/**
 * Form state schema — flat object with string fields for input binding.
 * Used with `itemVazio()` to generate the initial reactive form state.
 */
export const ProductFormSchema = z.object({
    sku: z.string(),
    name: z.string(),
    price: z.string(),
    type: ProductTypeSchema,
    size: z.string(),
    weight: z.string(),
    height: z.string(),
    width: z.string(),
    length: z.string(),
})

// === Type Exports (derived from schemas — single source of truth) ===

export type ProductType = z.infer<typeof ProductTypeSchema>
export type Product = z.output<typeof ProductSchema>
export type CreateProductInput = z.infer<typeof CreateProductSchema>
export type ProductFormState = z.input<typeof ProductFormSchema>
