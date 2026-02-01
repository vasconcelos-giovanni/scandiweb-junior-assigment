# Scandiweb Junior Developer Test Assignment - Frontend Client

This is the frontend SPA for the **Scandiweb Junior Developer Test Assignment**. It's built with **Nuxt 4.3.0**, **Vuetify 3.11.7**, and **Zod 4.0.0** for form validation.

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
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
- [API Integration](#api-integration)
- [Form Validation](#form-validation)
- [Component Reference](#component-reference)
- [Element IDs and Classes](#element-ids-and-classes)
- [Styling](#styling)
- [Environment Variables](#environment-variables)

---

## Overview

This frontend client provides a user interface for managing products with three distinct types:

- **DVD** - with `size` attribute (in MB)
- **Book** - with `weight` attribute (in Kg)
- **Furniture** - with `height`, `width`, and `length` attributes (dimensions in CM)

The application consumes the PHP REST API backend and follows all the Scandiweb test assignment requirements for element IDs, classes, and functionality.

---

## Features

- ✅ **Product List Page** - Display all products in a responsive grid
- ✅ **Product Add Page** - Create new products with dynamic form fields
- ✅ **Mass Delete** - Select multiple products and delete them at once
- ✅ **Form Validation** - Client-side validation using Zod 4.0.0
- ✅ **Type Switching** - Dynamic form fields based on product type
- ✅ **Light Theme** - Clean, light-themed UI following Vuetify conventions
- ✅ **Responsive Design** - Works on desktop and mobile devices
- ✅ **No Notifications** - As per requirements, no alert windows or notifications

---

## Tech Stack

| Technology | Version | Purpose |
|------------|---------|---------|
| [Nuxt](https://nuxt.com/) | 4.3.0 | Vue.js meta-framework for SSR/SPA |
| [Vue.js](https://vuejs.org/) | 3.5.x | Reactive UI framework |
| [Vuetify](https://vuetifyjs.com/) | 3.11.7 | Material Design component library |
| [Zod](https://zod.dev/) | 4.0.0 | TypeScript-first schema validation |
| [TypeScript](https://www.typescriptlang.org/) | 5.x | Type safety |
| [SCSS](https://sass-lang.com/) | - | CSS preprocessor |

---

## Project Structure

```
client/
├── app.vue                          # Main application component
├── nuxt.config.ts                   # Nuxt configuration
├── package.json                     # Dependencies and scripts
├── tsconfig.json                    # TypeScript configuration
├── eslint.config.mjs                # ESLint configuration
├── .env.example                     # Environment variables template
├── .gitignore                       # Git ignore rules
│
├── assets/
│   └── styles/
│       └── main.scss                # Global SCSS styles
│
├── components/
│   └── ProductCard.vue              # Product card component
│
├── composables/
│   └── useProducts.ts               # Products API composable
│
├── pages/
│   ├── index.vue                    # Product List page (/)
│   └── add-product.vue              # Product Add page (/add-product)
│
├── plugins/
│   └── vuetify.ts                   # Vuetify plugin configuration
│
├── schemas/
│   └── product.ts                   # Zod validation schemas
│
└── types/
    └── product.ts                   # TypeScript type definitions
```

---

## Getting Started

### Prerequisites

- **Node.js** >= 20.0.0
- **npm**, **pnpm**, **yarn**, or **bun** package manager
- **Backend API** running (see `/api` folder)

### Installation

1. Navigate to the client directory:

```bash
cd client
```

2. Install dependencies:

```bash
# Using npm
npm install

# Using pnpm
pnpm install

# Using yarn
yarn install

# Using bun
bun install
```

### Configuration

1. Copy the environment file:

```bash
cp .env.example .env
```

2. Update the API base URL in `.env`:

```env
NUXT_PUBLIC_API_BASE_URL=http://localhost:8000
```

### Development

Start the development server:

```bash
# Using npm
npm run dev

# Using pnpm
pnpm dev

# Using yarn
yarn dev
```

The application will be available at `http://localhost:3000`.

### Production Build

Generate a production build:

```bash
# Static Generation (recommended for SPA)
npm run generate

# Server-Side Rendering build
npm run build
```

Preview the production build:

```bash
npm run preview
```

---

## Pages

### Product List (/)

The main page displaying all products in a responsive grid.

**Features:**
- Displays all products sorted by primary key
- Each product shows: SKU, Name, Price, and type-specific attribute
- Checkbox selection for mass delete (class: `.delete-checkbox`)
- "ADD" button navigates to Product Add page
- "MASS DELETE" button (ID: `#delete-product-btn`) deletes selected products
- No pagination - all items on one page
- No notifications or alerts

**Screenshot Reference:**
```
┌─────────────────────────────────────────────────────────────┐
│ Product List                              [ADD] [MASS DELETE]│
├─────────────────────────────────────────────────────────────┤
│ ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐           │
│ │☐        │ │☐        │ │☐        │ │☐        │           │
│ │JVC200123│ │GGWP0007 │ │TR120555 │ │         │           │
│ │Acme DISC│ │War Peace│ │Chair    │ │         │           │
│ │1.00 $   │ │20.00 $  │ │40.00 $  │ │         │           │
│ │Size:700 │ │Weight:2 │ │24x45x15 │ │         │           │
│ └─────────┘ └─────────┘ └─────────┘ └─────────┘           │
└─────────────────────────────────────────────────────────────┘
```

### Product Add (/add-product)

Form page for creating new products.

**Features:**
- Form with ID: `#product_form`
- Dynamic fields based on selected product type
- Type switcher dropdown (ID: `#productType`)
- Client-side validation with Zod
- "Save" button submits the form
- "Cancel" button returns to Product List
- Type-specific description text

**Form Fields:**

| Field | ID | Description |
|-------|-----|-------------|
| SKU | `#sku` | Unique product identifier |
| Name | `#name` | Product name |
| Price | `#price` | Price in dollars |
| Type Switcher | `#productType` | DVD / Book / Furniture |
| Size (DVD) | `#size` | Size in MB |
| Weight (Book) | `#weight` | Weight in KG |
| Height (Furniture) | `#height` | Height in CM |
| Width (Furniture) | `#width` | Width in CM |
| Length (Furniture) | `#length` | Length in CM |

---

## API Integration

The client communicates with the backend API using the `useProducts` composable.

### Endpoints Used

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/products` | Fetch all products |
| POST | `/products` | Create a new product |
| DELETE | `/products` | Mass delete products |

### Request Examples

**Create DVD Product:**
```json
{
  "sku": "DVD001",
  "name": "Test DVD",
  "price": 9.99,
  "type": "dvd",
  "size": 700
}
```

**Create Book Product:**
```json
{
  "sku": "BOOK001",
  "name": "Test Book",
  "price": 19.99,
  "type": "book",
  "weight": 1.5
}
```

**Create Furniture Product:**
```json
{
  "sku": "FURN001",
  "name": "Test Chair",
  "price": 49.99,
  "type": "furniture",
  "height": 100,
  "width": 50,
  "length": 50
}
```

**Mass Delete:**
```json
{
  "ids": [1, 2, 3]
}
```

---

## Form Validation

Form validation is handled using **Zod 4.0.0** with the following rules:

### Common Fields

| Field | Validation |
|-------|------------|
| SKU | Required, max 255 chars, alphanumeric with `-` and `_` |
| Name | Required, max 255 chars |
| Price | Required, positive number, max 2 decimal places |

### Type-Specific Fields

| Type | Field | Validation |
|------|-------|------------|
| DVD | Size | Required, positive integer |
| Book | Weight | Required, positive number |
| Furniture | Height | Required, positive integer |
| Furniture | Width | Required, positive integer |
| Furniture | Length | Required, positive integer |

---

## Component Reference

### ProductCard

Displays a single product card with checkbox for selection.

**Props:**
| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `product` | `Product` | required | The product data to display |
| `selected` | `boolean` | `false` | Whether the product is selected |

**Events:**
| Event | Payload | Description |
|-------|---------|-------------|
| `update:selected` | `boolean` | Emitted when checkbox is toggled |

**Usage:**
```vue
<ProductCard
  :product="product"
  :selected="isSelected"
  @update:selected="handleSelection"
/>
```

---

## Element IDs and Classes

As per the test assignment requirements:

### Product List Page

| Element | ID/Class | Text |
|---------|----------|------|
| Mass Delete button | `#delete-product-btn` | MASS DELETE |
| Delete Checkbox | `.delete-checkbox` | N/A |
| Add Product Button | - | ADD |

### Product Add Page

| Element | ID | Text |
|---------|-----|------|
| Product form | `#product_form` | N/A |
| SKU input field | `#sku` | N/A |
| Name input field | `#name` | N/A |
| Price input field | `#price` | N/A |
| Type switcher | `#productType` | N/A |
| DVD option in types | - | DVD |
| Book option in types | - | Book |
| Furniture option in types | - | Furniture |
| DVD size input field | `#size` | N/A |
| Book weight input field | `#weight` | N/A |
| Furniture height field | `#height` | N/A |
| Furniture width field | `#width` | N/A |
| Furniture length field | `#length` | N/A |
| Save button | - | Save |
| Cancel button | - | Cancel |

---

## Styling

The application uses a **light theme** following Vuetify's color conventions:

| Color | Usage | Value |
|-------|-------|-------|
| Primary | Buttons, links | `#1976D2` (Blue) |
| Secondary | Secondary buttons | `#424242` (Grey) |
| Error | Error states, delete | `#D32F2F` (Red) |
| Success | Success states | `#388E3C` (Green) |
| Info | Information | `#0288D1` (Light Blue) |
| Warning | Warnings | `#FBC02D` (Amber) |
| Background | Page background | `#FAFAFA` |
| Surface | Cards, forms | `#FFFFFF` |

### Customizing Styles

Global styles are in `assets/styles/main.scss`. The Vuetify theme is configured in `plugins/vuetify.ts`.

---

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `NUXT_PUBLIC_API_BASE_URL` | Backend API base URL | `http://localhost:8000` |

---

## Scripts

| Script | Description |
|--------|-------------|
| `npm run dev` | Start development server |
| `npm run build` | Build for production (SSR) |
| `npm run generate` | Generate static site |
| `npm run preview` | Preview production build |
| `npm run lint` | Run ESLint |
| `npm run lint:fix` | Fix ESLint issues |
| `npm run typecheck` | Run TypeScript type checking |

---

## Browser Support

The application supports modern browsers:

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

---

## License

This project is part of the Scandiweb Junior Developer Test Assignment.

---

## Author

Giovanni Vasconcelos
