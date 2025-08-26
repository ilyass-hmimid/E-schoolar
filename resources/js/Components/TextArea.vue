<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-gray-700">
            {{ label }}
        </label>
        <div class="mt-1">
            <textarea
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                :class="{
                    'border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500': error,
                    'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500': !error
                }"
                class="shadow-sm block w-full sm:text-sm rounded-md"
                :placeholder="placeholder"
                :rows="rows"
                :disabled="disabled"
            ></textarea>
            <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
            <p v-if="hint" class="mt-2 text-sm text-gray-500">{{ hint }}</p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'TextArea',
    props: {
        modelValue: {
            type: [String, Number],
            default: ''
        },
        label: {
            type: String,
            default: ''
        },
        error: {
            type: String,
            default: ''
        },
        hint: {
            type: String,
            default: ''
        },
        placeholder: {
            type: String,
            default: ''
        },
        rows: {
            type: [String, Number],
            default: 3
        },
        disabled: {
            type: Boolean,
            default: false
        }
    },
    emits: ['update:modelValue']
};
</script>
