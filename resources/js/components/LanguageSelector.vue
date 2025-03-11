<template>
    <SelectRoot v-model="selectedLocale" @update:modelValue="emitChange">
        <SelectTrigger class="w-[150px] rounded border px-4 py-2 text-black border-[#19140035] text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]
            ">
            <SelectValue>{{ languageLabel }}</SelectValue>
        </SelectTrigger>

        <SelectContent class="rounded border bg-white shadow-lg">
            <SelectGroup>
                <SelectItem v-for="(label, code) in languages" :key="code" :value="code" class="cursor-pointer px-4 py-2 hover:bg-gray-100">
                    {{ label }}
                </SelectItem>
            </SelectGroup>
        </SelectContent>
    </SelectRoot>
</template>

<script setup lang="ts">
import { SelectContent, SelectGroup, SelectItem, SelectRoot, SelectTrigger, SelectValue } from 'radix-vue';
import { computed, ref } from 'vue';

const props = defineProps({
    modelValue: { // passed language code e.g. "en"
        type: String,
        required: true,
    },
    languages: { // Object with language codes and labels (e.g., { en: "English", es: "Español" })
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const selectedLocale = ref(props.modelValue);

// Compute the label of the current selected language
const languageLabel = computed(() => props.languages[selectedLocale.value] || 'Select Language');

const emitChange = (newLocale: string) => {
    selectedLocale.value = newLocale;
    emit('update:modelValue', newLocale);
    emit('change', newLocale);
};
</script>
