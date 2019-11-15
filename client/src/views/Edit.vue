<template>
  <v-form v-model="valid" @submit.prevent="submit">
    <h1>{{ newGroup ? $t("edit.title.new") : $t("edit.title.edit") }}</h1>
    <v-text-field v-model="nameInput"
                  :rules="[required]"
                  :autofocus="newGroup"
                  :label="$t('edit.name.label')"
    ></v-text-field>
    <v-text-field v-model="idInput"
                  :rules="[required, validID]"
                  :disabled="!newGroup"
                  :label="$t('edit.id.label')"
    ></v-text-field>
    <v-checkbox class="new-pass"
                v-if="!newGroup"
                v-model="newPass"
                :label="$t('edit.pass.checkbox')"
    ></v-checkbox>
    <v-slide-y-transition>
      <v-text-field v-if="newGroup || newPass"
                    v-model="passInput"
                    autocomplete="off"
                    :append-icon="showPass ? 'mdi-eye-off' : 'mdi-eye'"
                    :type="showPass ? 'text' : 'password'"
                    @click:append="showPass = !showPass"
                    :rules="[required]"
                    :autofocus="newPass"
                    :label="$t('edit.pass.label')"
      ></v-text-field>
    </v-slide-y-transition>

    <h2>{{ $t("edit.members.title") }}</h2>
    <v-list v-if="members.length">
      <v-slide-y-transition group>
        <v-list-item v-for="(member, index) in members" :key="index">
          <v-text-field v-model="members[index]"
                        :rules="[required, noDuplicate]"
                        :append-icon="deletable(index) ? 'mdi-delete' : ''"
                        @click:append="deleteMember(index)"
                        :autofocus="index > 1 && !member.length"
                        :placeholder="$t('edit.members.label')"
          ></v-text-field>
        </v-list-item>
      </v-slide-y-transition>
    </v-list>

    <v-layout>
      <v-btn @click="addMember" text height="50">
        <v-icon>mdi-account-plus</v-icon>
        <span>{{ $t("edit.members.button") }}</span>
      </v-btn>
    </v-layout>

    <Buttons :text="newGroup ? $t('button.create') : $t('button.save')"
             :valid="valid"/>
  </v-form>
</template>

<script>
import "@/assets/css/form.css"
import Buttons from "@/components/Buttons";
import {mapActions, mapMutations, mapState} from "vuex";

export default {
    name: "Edit",
    components: {
        Buttons
    },
    data() {
        return {
            valid: false,
            showPass: false,
            nameInput: "",
            idInput: "",
            newPass: false,
            passInput: "",
            members: ["", ""],

            required: value => !!value || this.$t("validation.required"),
            validID: value => /^[a-zA-Z0-9-]*$/.test(value) || this.$t("validation.id"),
            noDuplicate: value => {
                let count = 0;
                for (const member of this.members)
                    if (member === value)
                        ++count;

                return count < 2 || this.$t("validation.member");
            }
        }
    },
    props: {
        newGroup: {type: Boolean, default: false}
    },
    computed: {
        ...mapState(["group", "refresh"]),
    },
    watch: {
        nameInput(value) {
            if (this.newGroup)
                this.idInput = value
                    .toLowerCase().trim().replace(/[ _]/g, "-")
                    .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
                    .replace(/[^a-zA-Z0-9-]/g, "");
        },
        idInput(value) {
            this.idInput = value.toLowerCase();
        },
    },
    methods: {
        ...mapActions(["fetchGroup", "editGroup"]),
        ...mapMutations(["onRefresh"]),

        async submit() {
            const data = {
                name: this.nameInput,
                path: this.idInput,
                members: this.members
            };

            if (this.newGroup || this.newPass)
                data.pass = this.passInput;

            await this.editGroup({
                group: data,
                newGroup: this.newGroup
            });

            if (this.newGroup)
                await this.$router.push("/group/" + data.path);
            else
                this.$router.go(-1);
        },

        deletable(index) {
            if (this.newGroup)
                return this.members.length > 2;

            if (!this.group)
                return false;

            return index >= this.group.members.length;
        },
        deleteMember(index) {
            if (this.deletable(index))
                this.members.splice(index, 1)
        },
        addMember() {
            this.members.push("");
            scrollTo(0, document.body.scrollHeight);
        }
    },
    created() {
        if (!this.newGroup) {
            this.idInput = this.$route.params.id;

            this.onRefresh(async () => {
                await this.fetchGroup(this.$route.params.id);
                this.nameInput = this.group.name;
                this.members = this.group.members.map(e => e.name);
            });

            this.refresh();
        }
    },
    beforeCreate() {
        scrollTo(0, 0);
    }
}
</script>

<style scoped>
.v-form {
  padding-bottom: 150px;
}

.v-btn {
  border-bottom: 1px solid lightgrey;
  margin: 0;
  position: fixed;
  bottom: 50px;
  left: 0;
  width: 100%;
  background: #eee;
  border-radius: 0;
}

.v-btn:hover {
  position: fixed;
}

.v-btn .v-icon {
  margin-right: 10px;
}

.new-pass {
  margin-left: 20px;
}

.v-list {
  margin-top: 10px;
}
</style>
