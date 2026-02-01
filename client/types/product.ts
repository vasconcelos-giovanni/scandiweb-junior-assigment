/**
 * Product Types
 *
 * TypeScript interfaces matching the API response structure
 * from the Scandiweb Junior Assignment Backend.
 */

/**
 * Product type discriminator
 */
export type ProductType = 'dvd' | 'book' | 'furniture'

/**
 * Base product interface with common properties
 */
export interface BaseProduct {
    id: number
    sku: string
    name: string
    price: number
    type: ProductType
    specificAttribute: string
}

/**
 * DVD product with size attribute
 */
export interface DvdProduct extends BaseProduct {
    type: 'dvd'
    size: number
}

/**
 * Book product with weight attribute
 */
export interface BookProduct extends BaseProduct {
    type: 'book'
    weight: number
}

/**
 * Furniture product with dimensions
 */
export interface FurnitureProduct extends BaseProduct {
    type: 'furniture'
    height: number
    width: number
    length: number
}

/**
 * Union type for all product types
 */
export type Product = DvdProduct | BookProduct | FurnitureProduct

/**
 * Product list API response
 */
export interface ProductListResponse {
    data: Product[]
}

/**
 * Single product API response
 */
export interface ProductResponse {
    data: Product
    message?: string
}

/**
 * API error response
 */
export interface ApiErrorResponse {
    error: string
    message?: string
    errors?: Record<string, string>
}

/**
 * Create product request - base fields
 */
export interface CreateProductRequest {
    sku: string
    name: string
    price: number
    type: ProductType
}

/**
 * Create DVD product request
 */
export interface CreateDvdRequest extends CreateProductRequest {
    type: 'dvd'
    size: number
}

/**
 * Create Book product request
 */
export interface CreateBookRequest extends CreateProductRequest {
    type: 'book'
    weight: number
}

/**
 * Create Furniture product request
 */
export interface CreateFurnitureRequest extends CreateProductRequest {
    type: 'furniture'
    height: number
    width: number
    length: number
}

/**
 * Union type for all create product request types
 */
export type CreateProductPayload = CreateDvdRequest | CreateBookRequest | CreateFurnitureRequest

/**
 * Mass delete request
 */
export interface MassDeleteRequest {
    ids: number[]
}

/**
 * Mass delete response
 */
export interface MassDeleteResponse {
    message: string
}

/**
 * Product form state (used in add-product page)
 */
export interface ProductFormState {
    sku: string
    name: string
    price: string
    type: ProductType
    // DVD specific
    size: string
    // Book specific
    weight: string
    // Furniture specific
    height: string
    width: string
    length: string
}

/**
 * Initial empty form state
 */
export const initialFormState: ProductFormState = {
    sku: '',
    name: '',
    price: '',
    type: 'dvd',
    size: '',
    weight: '',
    height: '',
    width: '',
    length: '',
}
