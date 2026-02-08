<template>
    <div>
        <v-text-field
            id="weight"
            v-model="formState.weight"
            label="Weight (KG)"
            placeholder="Enter weight in KG"
            type="number"
            min="0.01"
            step="0.01"
            :error-messages="errors.weight"
            :disabled="disabled"
        />
        <p class="text-body-2 text-medium-emphasis font-italic mt-2">
            Please provide book weight in KG
        </p>
    </div>
</template>

<script setup lang="ts">
import type { ProductFormState } from '~/schemas/ProductSchemas'

defineProps<{
    formState: ProductFormState
    errors: Record<string, string>
    disabled: boolean
}>()
</script>
