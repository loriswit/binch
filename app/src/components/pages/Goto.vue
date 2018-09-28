<template>
  <v-form v-model="valid" @submit.prevent="$emit('group', input)">
    <h1>Go to group</h1>
    <v-text-field v-model="input"
                  :rules="[required, validID]"
                  label="Group identifier"
                  autofocus
    ></v-text-field>
    <Buttons text="Go" :valid="valid" @cancel="$emit('page', 'Home')"/>
  </v-form>
</template>

<script>
import Buttons from "@/components/Buttons";

import "@/assets/css/form.css"

export default {
    name: "Goto",
    components: {
        Buttons
    },
    data() {
        return {
            valid: false,
            input: "",

            required: value => !!value || "Cannot be be empty",
            validID: value => /^[a-zA-Z0-9-]*$/.test(value) || "Must only contain letters, numbers and dashes"
        }
    },
    created() {
        this.$emit("group", null);
    }
}
</script>
