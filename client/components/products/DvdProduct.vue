<template>
    <div>
        <v-text-field
            id="size"
            v-model="formState.size"
            label="Size (MB)"
            placeholder="Enter size in MB"
            type="number"
            min="1"
            step="1"
            :error-messages="errors.size"
            :disabled="disabled"
        />
        <p class="text-body-2 text-medium-emphasis font-italic mt-2">
            Please provide disc size in MB
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
