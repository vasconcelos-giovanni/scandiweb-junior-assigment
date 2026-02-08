<template>
    <div>
        <v-text-field
            id="height"
            v-model="formState.height"
            label="Height (CM)"
            placeholder="Enter height in CM"
            type="number"
            min="1"
            step="1"
            :error-messages="errors.height"
            :disabled="disabled"
        />

        <v-text-field
            id="width"
            v-model="formState.width"
            label="Width (CM)"
            placeholder="Enter width in CM"
            type="number"
            min="1"
            step="1"
            :error-messages="errors.width"
            :disabled="disabled"
        />

        <v-text-field
            id="lenght"
            v-model="formState.length"
            label="Length (CM)"
            placeholder="Enter length in CM"
            type="number"
            min="1"
            step="1"
            :error-messages="errors.length"
            :disabled="disabled"
        />
        <p class="text-body-2 text-medium-emphasis font-italic mt-2">
            Please provide dimensions in HxWxL format
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
