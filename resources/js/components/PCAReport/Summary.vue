<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'

import { type p_c_a_reportsEntity } from '@/types/DatabaseModels';
import { reactive, watch } from 'vue';

const props = defineProps<{ form: p_c_a_reportsEntity }>();

const emit = defineEmits(['update:form']);

const localForm = reactive({ ...props.form });

// Watch for changes and emit updates
watch(
  localForm,
  // - TODO:
  // Handle radio button changes to mimick te database boolean
  (newVal) => { emit('update:form', { ...newVal }); },
  { deep: true },
);
</script>

<template>
    <div class="grid grid-cols-2 gap-4">

    <Label htmlFor="o-prop">Occupation of the property</Label>
    <Input id="o-prop" v-model="localForm.occupation_of_the_property"></Input>

    <Label htmlFor="siteArea">Total Site area:</Label>
    <Input id="siteArea" v-model="localForm.total_site_area"></Input>

    <Label htmlFor="buildingArea">Surface area of the building (Approx.)</Label>
    <Input id="buildingArea" v-model="localForm.surface_area_of_the_building"></Input>

    <Label htmlFor="buildingOccupation">Occupation of the building</Label>
    <Input id="buildingOccupation" v-model="localForm.occupation_of_the_building"></Input>

    <Label htmlFor="yearConstr">Year of construction</Label>
    <Input id="yearConstr" v-model="localForm.year_of_construction"></Input>

    <Label htmlFor="building">Building</Label>
    <Input id="building" v-model="localForm.building"></Input>

    <Label htmlFor="floors">Number of floors:</Label>
    <Input id="floors" v-model="localForm.numbers_of_floors"></Input>

    <Label htmlFor="basement">Basement</Label>
    <RadioGroup id="basement" :default-value="localForm.basement.toString()" :orientation="'vertical'">
      <div class="flex items-center space-x-2">
        <RadioGroupItem id="r1" value="true" />
        <Label for="r1">Yes</Label>
      </div>
      <div class="flex items-center space-x-2">
        <RadioGroupItem id="r2" value="false" />
        <Label for="r2">No</Label>
      </div>
    </RadioGroup>

    <Label htmlFor="nonResidentalUnits">Non-residential units</Label>
    <Input id="nonResidentalUnits" v-model="localForm.non_residential_units"></Input>

    <Label htmlFor="residentialUnits">Residential units</Label>
    <Input id="residentialUnits" v-model="localForm.residential_units"></Input>
  </div>
</template>
