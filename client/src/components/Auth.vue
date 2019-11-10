<template>
  <v-dialog v-model="dialog" width="500">
    <v-card>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <v-card-title class="headline" primary-title>
          {{ $t("auth.title") }}
        </v-card-title>
        <v-card-text>
          <v-slide-y-transition>
            <p class="wrong" v-if="wrong">{{ $t("auth.wrong") }}</p>
          </v-slide-y-transition>
          <v-text-field v-model="input"
                        ref="input"
                        autocomplete="off"
                        @change="wrong = false"
                        :append-icon="showPass ? 'mdi-eye-off' : 'mdi-eye'"
                        :type="showPass ? 'text' : 'password'"
                        @click:append="showPass = !showPass"
                        :rules="[required]"
                        :label="$t('auth.pass.label')"
          ></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="dialog = false">
            {{ $t("button.cancel") }}
          </v-btn>
          <v-btn text :loading="loading" color="primary" :disabled="!valid" type="submit">
            {{ $t("button.ok") }}
          </v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-dialog>
</template>

<script>
import "@/assets/css/form.css"
import {mapMutations, mapState} from "vuex";

export default {
    name: "Auth",
    data() {
        return {
            dialog: false,
            valid: true,
            input: "",
            showPass: false,
            loading: false,
            wrong: false,

            required: value => !!value || this.$t("validation.required"),
        }
    },
    computed: {
        ...mapState(["auth"])
    },
    watch: {
        dialog(value) {
            if (value)
                setTimeout(() => this.$refs.input.focus());
            else {
                this.closeAuthDialog();
                this.wrong = false;
                this.$refs.form.reset();
            }
        },
        'auth.required'(value) {
            this.dialog = value;
        }
    },
    methods: {
        ...mapMutations(["closeAuthDialog", "putToken"]),

        async submit() {
            this.loading = true;

            const options = {
                method: "GET",
                url: "group/" + this.$route.params.id + "/auth",
                headers: {
                    Authorization: "Basic " + btoa(":" + this.input)
                }
            };

            try {
                const response = await this.$http(options);
                this.putToken({
                    id: this.$route.params.id,
                    token: response.body.token
                });

                // run pending requests
                for (const nextRequest of this.auth.next)
                    nextRequest();

                this.closeAuthDialog();
                this.$router.go(-1);

            } catch (response) {
                if (response.status === 403)
                    this.wrong = true;

            } finally {
                this.loading = false;
            }
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
