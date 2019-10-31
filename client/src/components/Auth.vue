<template>
  <v-dialog v-model="dialog" width="500">
    <v-card>
      <v-form v-model="valid" @submit.prevent="submit()">
        <v-card-title class="headline" primary-title>
          {{ $t("auth.title") }}
        </v-card-title>
        <v-card-text>
          <v-slide-y-transition>
            <p class="wrong" v-if="wrong">{{ $t("auth.wrong") }}</p>
          </v-slide-y-transition>
          <v-text-field v-model="input"
                        autocomplete="off"
                        @change="wrong = false"
                        :append-icon="showPass ? 'visibility_off' : 'visibility'"
                        :type="showPass ? 'text' : 'password'"
                        @click:append="showPass = !showPass"
                        :rules="[required]"
                        autofocus
                        :label="$t('auth.pass.label')"
          ></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn flat @click="dialog = false">
            {{ $t("button.cancel") }}
          </v-btn>
          <v-btn :loading="loading" color="primary" flat type="submit">
            {{ $t("button.ok") }}
          </v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-dialog>
</template>

<script>
import "@/assets/css/form.css"
import Storage from "@/storage"

export default {
    name: "Auth",
    data() {
        return {
            dialog: false,
            valid: false,
            input: "",
            showPass: false,
            loading: false,
            wrong: false,

            required: value => !!value || this.$t("validation.required"),
        }
    },
    props: {
        active: Boolean,
        groupID: String
    },
    watch: {
        active(value) {
            this.dialog = value;
        },
        dialog(value) {
            if (!value) {
                this.input = null;
                this.$emit("close");
            }
        }
    },
    methods: {
        submit() {
            this.loading = true;
            const options = {
                method: "GET",
                url: "group/" + this.groupID + "/auth",
                headers: {
                    Authorization: "Basic " + btoa(":" + this.input)
                }
            };
            this.$http(options).then(response => {
                this.loading = false;
                Storage.putToken(this.groupID, response.body.token);
                this.dialog = false;
            }, response => {
                this.loading = false;
                if (response.status === 403)
                    this.wrong = true;
            });
        }
    }
}
</script>

<style scoped>
.v-form {
  padding: 0;
}

.wrong {
  color: red;
}
</style>
