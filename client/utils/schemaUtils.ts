import type { z } from 'zod'

/**
 * Generates an empty default value from a Zod object schema.
 * Used to initialize reactive form state from schema definitions,
 * ensuring the form shape always matches the schema (single source of truth).
 *
 * @example
 * const formState = reactive(itemVazio(ProductFormSchema))
 */
export function itemVazio<T extends z.ZodObject<any>>(schema: T): z.input<T> {
    const shape = schema.shape
    const result: Record<string, unknown> = {}

    for (const [key, fieldSchema] of Object.entries(shape)) {
        result[key] = getDefaultValue(fieldSchema as z.ZodType)
    }

    return result as z.input<T>
}

function getDefaultValue(schema: z.ZodType): unknown {
    const def = (schema as any)?._zod?.def

    switch (def?.type) {
        case 'string':
            return ''
        case 'number':
        case 'float':
            return 0
        case 'boolean':
            return false
        case 'array':
            return []
        case 'nullable':
            return null
        case 'optional':
            return undefined
        case 'literal':
            return def.value
        case 'object':
            return itemVazio(schema as z.ZodObject<any>)
        default:
            break
    }

    // Enum handling — uses the public `.options` property
    if ('options' in schema && Array.isArray((schema as any).options)) {
        return (schema as any).options[0]
    }

    return undefined
}

/**
 * Safely parses data against a Zod schema.
 * Returns the validated data on success, or the raw data as fallback.
 * Prevents data loss from partial schema mismatches in API responses.
 *
 * @example
 * data.products.value = fullSafeParse(ProductSchema.array(), response.data)
 */
export function fullSafeParse<T>(schema: z.ZodType<T>, data: unknown): T {
    const result = schema.safeParse(data)
    return result.success ? result.data : (data as T)
}

/**
 * Validates data against a Zod schema and returns field-level errors.
 * Compatible with Vuetify's `:error-messages` prop format.
 *
 * @example
 * const result = validateForm(CreateProductSchema, parsedData)
 * if (!result.success) setErrors(result.errors)
 */
export function validateForm<T>(
    schema: z.ZodType<T>,
    data: unknown,
): { success: true; data: T } | { success: false; errors: Record<string, string> } {
    const result = schema.safeParse(data)

    if (result.success) {
        return { success: true, data: result.data }
    }

    const errors: Record<string, string> = {}

    for (const issue of result.error.issues) {
        const path = issue.path.join('.')
        if (!errors[path]) {
            errors[path] = issue.message
        }
    }

    return { success: false, errors }
}
