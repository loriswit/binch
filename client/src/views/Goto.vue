<template>
  <v-form v-model="valid" @submit.prevent="submit">
    <h1>{{ $t("goto.title") }}</h1>
    <v-text-field v-model="input"
                  :rules="[required, validID]"
                  :label="$t('goto.input.label')"
                  autofocus
    ></v-text-field>
    <Buttons :text="$t('button.go')" :valid="valid"/>
  </v-form>
</template>

<script>
import Buttons from "@/components/Buttons";

import "@/assets/css/form.css"
import {mapActions, mapState} from "vuex";

export default {
    name: "Goto",
    components: {
        Buttons
    },
    data() {
        return {
            valid: false,
            input: "",

            required: value => !!value || this.$t("validation.required"),
            validID: value => /^[a-zA-Z0-9-]*$/.test(value) || this.$t("validation.id")
        }
    },
    computed: {
        ...mapState(["group"])
    },
    methods: {
        ...mapActions(["fetchGroup"]),

        async submit() {
            const id = this.input.toLowerCase();
            await this.fetchGroup(id);
            await this.$router.push("/group/" + id);
        }
    },
    created() {
        this.$emit("group", null);
    }
}
</script>
