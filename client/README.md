# Scandiweb Junior Developer Test Assignment — Frontend Client

This is the frontend SPA for the **Scandiweb Junior Developer Test Assignment**. Built with **Nuxt 4**, **Vuetify 3**, and **Zod 4**, it follows a layered architecture where **Zod schemas are the single source of truth** for both validation and TypeScript types. Product type differences are handled through **component-based polymorphism** with a resolver map — no `if/else` or `switch/case` anywhere.

---

## Table of Contents

- [Overview](#overview)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
  - [Data Flow](#data-flow)
  - [Key Patterns](#key-patterns)
- [Project Structure](#project-structure)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Configuration](#configuration)
  - [Development](#development)
  - [Production Build](#production-build)
- [Pages](#pages)
  - [Product List (/)](#product-list-)
  - [Product Add (/add-product)](#product-add-add-product)
- [Architectural Guide](#architectural-guide)
  - [Schemas — Single Source of Truth](#schemas--single-source-of-truth)
  - [Product Type Polymorphism](#product-type-polymorphism)
  - [Composables — data / actions Pattern](#composables--data--actions-pattern)
  - [Centralized API Layer](#centralized-api-layer)
  - [Template Components](#template-components)
  - [Thin Pages](#thin-pages)
  - [Utility Functions](#utility-functions)
- [Component Reference](#component-reference)
  - [DefaultTemplate](#defaulttemplate)
  - [ProductList](#productlist)
  - [ProductCard](#productcard)
  - [DvdProduct / BookProduct / FurnitureProduct](#dvdproduct--bookproduct--furnitureproduct)
- [Element IDs and Classes](#element-ids-and-classes)
- [Styling](#styling)
- [Environment Variables](#environment-variables)

---

## Overview

This frontend client provides a user interface for managing products with three distinct types:

- **DVD** — with `size` attribute (in MB)
- **Book** — with `weight` attribute (in Kg)
- **Furniture** — with `height`, `width`, and `length` attributes (dimensions in CM)

The application consumes a PHP REST API backend and follows all the Scandiweb test assignment requirements for element IDs, classes, and functionality.

### Features

- ✅ Product List page — responsive grid with all products
- ✅ Product Add page — dynamic form fields based on product type
- ✅ Mass Delete — checkbox selection and batch deletion
- ✅ Client-side validation via Zod 4 discriminated union schemas
- ✅ Schema-driven types — no manually duplicated TypeScript interfaces
- ✅ Component-based polymorphism — no `if/else` or `switch/case` for product types
- ✅ Layered architecture — schemas → composables → components → pages
- ✅ Pure Vuetify styling — zero custom CSS
- ✅ Responsive design — adapts to desktop and mobile
- ✅ No notifications — per assignment requirements

---

## Tech Stack

| Technology | Version | Purpose |
|------------|---------|---------|
| [Nuxt](https://nuxt.com/) | 4.3.0 | Vue.js meta-framework (SPA mode) |
| [Vue.js](https://vuejs.org/) | 3.5.x | Reactive UI framework |
| [Vuetify](https://vuetifyjs.com/) | 3.11.7 | Material Design component library |
| [Zod](https://zod.dev/) | 4.0.0 | Schema validation & type derivation |
| [TypeScript](https://www.typescriptlang.org/) | 5.x | Static type checking |

---

## Architecture

### Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                        Page (.vue)                              │
│  Thin shell: <DefaultTemplate> + feature component              │
├─────────────────────────────────────────────────────────────────┤
│                    Feature Component (.vue)                      │
│  UI logic, loading state, user interactions                     │
│  Destructures { data, actions } from composable                  │
├─────────────────────────────────────────────────────────────────┤
│               API Composable (composables/api/)                 │
│  data = { reactive refs }    actions = { async methods }        │
│  Uses useAPI() for HTTP, schemas for parsing                    │
├─────────────────────────────────────────────────────────────────┤
│                  useAPI Composable                               │
│  Centralized $fetch wrapper with base URL                       │
├─────────────────────────────────────────────────────────────────┤
│               Zod Schemas (schemas/)                            │
│  Single source of truth for types, validation, and parsing      │
└─────────────────────────────────────────────────────────────────┘
```

### Key Patterns

| Pattern | Description |
|---------|-------------|
| **Schema-driven types** | All TypeScript types are derived from Zod schemas via `z.infer`, `z.input`, or `z.output`. No manually duplicated interfaces. |
| **data / actions** | Composables return `{ data, actions }` — reactive data and async actions, clearly separated. |
| **Product type polymorphism** | Each product type is a component (`DvdProduct.vue`, etc.) resolved by a map. Vue's `<component :is>` replaces all conditionals. |
| **Centralized API** | `useAPI()` wraps `$fetch` with the base URL. All HTTP goes through this layer. |
| **DefaultTemplate** | Reusable page wrapper providing a title, responsive sizing, optional back button, and a content slot. |
| **Thin pages** | Pages are 3–5 lines: `<DefaultTemplate>` + a feature component. All logic lives in components and composables. |
| **itemVazio** | Form state is initialized from Zod schemas via `itemVazio(Schema)`, keeping the schema as the single source of truth. |
| **Component-level loading** | Loading state (`ref(false)`) is managed in the component with `try/finally`, not in the composable. |

---

## Project Structure

```
client/
├── app.vue                              # NuxtLayout + title template
├── nuxt.config.ts                       # Nuxt config (SSR off, auto-imports)
├── package.json                         # Dependencies
├── tsconfig.json                        # TypeScript configuration
├── eslint.config.mjs                    # ESLint configuration
│
├── layouts/
│   └── default.vue                      # App bar + main + footer (Vuetify shell)
│
├── components/
│   ├── templates/
│   │   └── DefaultTemplate.vue          # Page wrapper (title + back + slot)
│   ├── products/
│   │   ├── DvdProduct.vue               # DVD-specific form fields
│   │   ├── BookProduct.vue              # Book-specific form fields
│   │   └── FurnitureProduct.vue         # Furniture-specific form fields
│   ├── ProductList.vue                  # Product grid, selection, mass delete
│   └── ProductCard.vue                  # Single product display card
│
├── composables/
│   ├── useAPI.ts                        # Centralized $fetch wrapper
│   └── api/
│       └── useProducts.ts              # Products data/actions composable
│
├── pages/
│   ├── index.vue                        # Product List (thin page)
│   └── add-product.vue                  # Product Add (form page)
│
├── plugins/
│   └── vuetify.ts                       # Vuetify plugin (theme + defaults)
│
├── schemas/
│   └── ProductSchemas.ts                # All Zod schemas + derived types
│
├── utils/
│   ├── schemaUtils.ts                   # itemVazio, fullSafeParse, validateForm
│   └── productTypeResolver.ts           # Polymorphic product type map (no conditionals)
│
└── public/
```

---

## Getting Started

### Prerequisites

- **Node.js** >= 20.0.0
- **npm**, **pnpm**, **yarn**, or **bun**
- **Backend API** running (see `/api` folder)

### Installation

```bash
cd client
npm install
```

### Configuration

```bash
cp .env.example .env
```

Update the API base URL:

```env
NUXT_PUBLIC_API_BASE_URL=http://localhost:8000
```

### Development

```bash
npm run dev
```

Available at `http://localhost:3000`.

### Production Build

```bash
# Static generation (recommended for SPA)
npm run generate

# SSR build
npm run build

# Preview production build
npm run preview
```

---

## Pages

### Product List (/)

The main page displaying all products in a responsive grid. This is a **thin page** — it only composes `DefaultTemplate` and `ProductList`:

```vue
<template>
    <DefaultTemplate titulo="Product List" :show-back="false">
        <ProductList/>
    </DefaultTemplate>
</template>
```

**Features:**
- Displays all products sorted by primary key
- Each product shows: SKU, Name, Price, and type-specific attribute
- Checkbox selection for mass delete (class: `.delete-checkbox`)
- ADD button navigates to Product Add page
- MASS DELETE button (`#delete-product-btn`) deletes selected products
- Loading spinner during fetch
- Empty state with guidance message

### Product Add (/add-product)

Form page for creating new products. Uses `DefaultTemplate` for the page wrapper and `itemVazio(ProductFormSchema)` for initial form state:

**Features:**
- Form with ID `#product_form`
- Dynamic fields based on selected product type
- Client-side validation with Zod discriminated union schema
- Save button submits the form; Cancel navigates back
- Back button in `DefaultTemplate` header
- API error display via `v-alert`

**Form Fields:**

| Field | ID | Type-Specific | Description |
|-------|-----|:---:|-------------|
| SKU | `#sku` | | Unique product identifier |
| Name | `#name` | | Product name |
| Price | `#price` | | Price in dollars |
| Type Switcher | `#productType` | | DVD / Book / Furniture |
| Size | `#size` | DVD | Size in MB |
| Weight | `#weight` | Book | Weight in KG |
| Height | `#height` | Furniture | Height in CM |
| Width | `#width` | Furniture | Width in CM |
| Length | `#length` | Furniture | Length in CM |

---

## Architectural Guide

### Schemas — Single Source of Truth

All schemas live in `schemas/ProductSchemas.ts`. Types are **derived**, never manually written:

```typescript
// Schema defines shape AND validation
export const ProductSchema = z.object({
    id: z.number().int().nonnegative(),
    sku: z.string(),
    name: z.string(),
    price: z.coerce.number().nonnegative(),
    type: ProductTypeSchema,
    specific_attribute: z.string(),
})

// Type is derived — always in sync
export type Product = z.output<typeof ProductSchema>
```

**Schema categories:**

| Schema | Purpose |
|--------|---------|
| `ProductSchema` | API response shape for product listing |
| `CreateProductSchema` | Discriminated union for form validation |
| `ProductFormSchema` | Flat object for `itemVazio()` form initialization |
| `DvdProductSchema` / `BookProductSchema` / `FurnitureProductSchema` | Type-specific validation |

### Product Type Polymorphism

The Scandiweb assignment **explicitly requires** avoiding `if/else` and `switch/case` for product type differences. The backend solves this with abstract classes and factories. The frontend mirrors this with **component-based polymorphism** and a **resolver map**.

#### The Resolver Map

`utils/productTypeResolver.ts` is the single registry. Each product type declares its:

| Property | Purpose |
|----------|--------|
| `label` | Display name for the type switcher dropdown |
| `component` | Vue component with type-specific form fields |
| `parseFields(form)` | Converts string form values to typed data for Zod validation |
| `buildPayload(form)` | Builds the type-specific portion of the API request body |
| `fieldKeys` | Field names owned by this type (for clearing errors on type switch) |

```typescript
// No if/else, no switch/case — just a map lookup
const productTypeMap: Record<ProductType, ProductTypeDefinition> =
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
    // book: { ... },
    // furniture: { ... },
}
```

#### How the Form Uses It

The page renders type-specific fields via Vue's dynamic `<component :is>` — no template conditionals:

```vue
<component
    :is="currentTypeDefinition.component"
    v-model="formState"
    :errors="errors"
    :disabled="loading"
/>
```

Validation and payload building also go through the resolver:

```typescript
const typeDefinition = resolveProductType(formState.type)

// Validation — no conditionals
const validation = validateForm(CreateProductSchema, {
    ...baseFields,
    type: formState.type,
    ...typeDefinition.parseFields(formState),
})

// Payload — no conditionals
const payload = {
    ...baseFields,
    ...typeDefinition.buildPayload(formState),
}
```

#### Type-Specific Components

Each product type has its own component in `components/products/`:

| Component | Fields | Description Text |
|-----------|--------|------------------|
| `DvdProduct.vue` | `#size` | "Please provide disc size in MB" |
| `BookProduct.vue` | `#weight` | "Please provide book weight in KG" |
| `FurnitureProduct.vue` | `#height`, `#width`, `#length` | "Please provide dimensions in HxWxL format" |

All share the same interface (`modelValue`, `errors`, `disabled`), making them interchangeable — the essence of polymorphism.

#### Adding a New Product Type

To add a new product type (e.g., "Poster"), you only need to:

1. Add a Zod schema in `schemas/ProductSchemas.ts`
2. Add it to the `CreateProductSchema` discriminated union
3. Create a `PosterProduct.vue` component in `components/products/`
4. Add one entry in `productTypeResolver.ts`

**No other files need modification.** No conditionals anywhere.

---

### Composables — data / actions Pattern

API composables in `composables/api/` return `{ data, actions }`:

```typescript
export default function useProducts()
{
    const { fetch } = useAPI()

    const data =
    {
        products: ref<Product[]>([]),
        error: ref<string | null>(null),
    }

    const actions =
    {
        async listProducts(): Promise<void> { /* ... */ },
        async createProduct(payload): Promise<boolean> { /* ... */ },
        async deleteProducts(ids): Promise<boolean> { /* ... */ },
        clearError(): void { /* ... */ },
    }

    return { data, actions }
}
```

Components **destructure** the refs from `data` so they auto-unwrap in templates:

```typescript
const {
    data: { products },
    actions,
} = useProducts()

// `products` is a top-level ref — auto-unwraps in <template>
```

### Centralized API Layer

`composables/useAPI.ts` wraps `$fetch` with the runtime base URL:

```typescript
export default function useAPI()
{
    const config = useRuntimeConfig()

    const baseFetch = $fetch.create({
        baseURL: `${config.public.apiBaseUrl}`,
    })

    async function fetch<T = unknown>(url: string, options?): Promise<T>
    {
        return baseFetch(url, options) as T
    }

    return { fetch }
}
```

All API composables use `useAPI()` instead of calling `$fetch` directly.

### Template Components

`DefaultTemplate` provides consistent page structure:

```vue
<DefaultTemplate titulo="Page Title" :show-back="false">
    <FeatureComponent/>
</DefaultTemplate>
```

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `titulo` | `string` | required | Page heading (also sets `<title>`) |
| `showBack` | `boolean` | `true` | Whether to show the back button |

The template uses Vuetify's `useDisplay()` for responsive title sizing.

### Thin Pages

Pages are intentionally minimal — they compose templates and components:

```
pages/index.vue        →  DefaultTemplate + ProductList
pages/add-product.vue  →  DefaultTemplate + inline form
```

All business logic, loading states, and interactions live in the **components** and **composables**, not in pages.

### Utility Functions

Located in `utils/schemaUtils.ts`, auto-imported globally:

| Function | Purpose |
|----------|---------|
| `itemVazio(schema)` | Generates an empty default object from a Zod object schema. Used for form state initialization. |
| `fullSafeParse(schema, data)` | Safely parses data against a schema, returning raw data as fallback on failure. Prevents data loss from partial mismatches. |
| `validateForm(schema, data)` | Validates data and returns field-level error messages compatible with Vuetify's `:error-messages` prop. |

**Example — Form initialization:**

```typescript
import { ProductFormSchema } from '~/schemas/ProductSchemas'

const formState = reactive(itemVazio(ProductFormSchema))
// → { sku: '', name: '', price: '', type: 'dvd', size: '', ... }
```

---

## Component Reference

### DefaultTemplate

**Path:** `components/templates/DefaultTemplate.vue`

Page wrapper that renders a responsive title, optional back navigation, a divider, and a content slot. Sets the document `<title>` via `useHead()`.

### ProductList

**Path:** `components/ProductList.vue`

Feature component containing all product listing logic:
- Calls `useProducts()` composable
- Manages `loading` state with `try/finally` (component-level)
- Manages `selectedIds` for mass delete
- Renders action buttons, loading spinner, product grid, or empty state
- Fetches products on `onBeforeMount`

### ProductCard

**Path:** `components/ProductCard.vue`

Presentational component for a single product:
- Vuetify `v-card` with `tonal`/`outlined` variant based on selection
- Checkbox for mass delete selection (`.delete-checkbox` class)
- Displays SKU, name, formatted price, and type-specific attribute
- Emits `update:selected` for parent binding
- Pure Vuetify layout — no custom CSS

### DvdProduct / BookProduct / FurnitureProduct

**Path:** `components/products/{Type}Product.vue`

Type-specific form field components, resolved polymorphically via `productTypeResolver.ts`:

- Each owns its specific `v-text-field` inputs with the required IDs (`#size`, `#weight`, `#height`, `#width`, `#length`)
- Each renders its own description text (e.g., "Please provide disc size in MB")
- All share the same props interface: `modelValue: ProductFormState`, `errors: Record<string, string>`, `disabled: boolean`
- All emit `update:modelValue` for `v-model` binding with the parent form
- Interchangeable — Vue's `<component :is>` swaps them without conditionals

---

## Element IDs and Classes

Required by the Scandiweb assignment specification:

| Element | Selector | Location |
|---------|----------|----------|
| Mass Delete button | `#delete-product-btn` | ProductList.vue |
| Delete checkboxes | `.delete-checkbox` | ProductCard.vue |
| Product form | `#product_form` | add-product.vue |
| SKU input | `#sku` | add-product.vue |
| Name input | `#name` | add-product.vue |
| Price input | `#price` | add-product.vue |
| Type switcher | `#productType` | add-product.vue |
| Size input (DVD) | `#size` | DvdProduct.vue |
| Weight input (Book) | `#weight` | BookProduct.vue |
| Height input (Furniture) | `#height` | FurnitureProduct.vue |
| Width input (Furniture) | `#width` | FurnitureProduct.vue |
| Length input (Furniture) | `#length` | FurnitureProduct.vue |

---

## Styling

This project uses **Vuetify exclusively** for styling — there is no custom CSS. All layout, spacing, typography, and color are achieved through:

- Vuetify component props (`variant`, `color`, `density`, `elevation`, etc.)
- Vuetify utility classes (`d-flex`, `justify-end`, `pa-4`, `mt-2`, `text-h6`, etc.)
- Vuetify theme configuration in `plugins/vuetify.ts`
- Vuetify global defaults for `VBtn`, `VTextField`, `VSelect`, and `VCard`

---

## Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `NUXT_PUBLIC_API_BASE_URL` | `http://localhost:8000` | Backend API base URL |
