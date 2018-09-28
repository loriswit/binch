<template>
  <v-form v-model="valid" @submit.prevent="submit()">
    <h1>{{ newGroup ? "New" : "Edit" }} group</h1>
    <v-text-field v-model="nameInput"
                  :rules="[required]"
                  :autofocus="newGroup"
                  label="Group name"
    ></v-text-field>
    <v-text-field v-model="idInput"
                  :rules="[required, validID]"
                  :disabled="!newGroup"
                  label="Group identifier"
    ></v-text-field>
    <v-checkbox class="new-pass"
                v-if="!newGroup"
                v-model="newPass"
                label="Change password"
    ></v-checkbox>
    <v-slide-y-transition>
      <v-text-field v-if="newGroup || newPass"
                    v-model="passInput"
                    autocomplete="off"
                    :append-icon="showPass ? 'visibility_off' : 'visibility'"
                    :type="showPass ? 'text' : 'password'"
                    @click:append="showPass = !showPass"
                    :rules="[required]"
                    :autofocus="newPass"
                    label="Password"
      ></v-text-field>
    </v-slide-y-transition>

    <h2>Members</h2>
    <v-list v-if="members.length">
      <v-slide-y-transition group>
        <v-list-tile v-for="(member, index) in members" :key="index">
          <v-text-field v-model="members[index]"
                        :rules="[required, noDuplicate]"
                        :append-icon="deletable(index) ? 'delete' : ''"
                        @click:append="deleteMember(index)"
                        :autofocus="index > 1 && !members[index].length"
                        placeholder="Member name"
          ></v-text-field>
        </v-list-tile>
      </v-slide-y-transition>
    </v-list>

    <v-layout>
      <v-btn @click="addMember()" flat>
        <v-icon>person_add</v-icon>
        <span>Add member</span>
      </v-btn>
    </v-layout>

    <Buttons :text="newGroup ? 'Create' : 'Save'"
             :valid="valid"
             @cancel="$emit('page', newGroup ? 'Home' : 'Group')"/>
  </v-form>
</template>

<script>
import "@/assets/css/form.css"
import Buttons from "@/components/Buttons";

export default {
    name: "Edit",
    components: {
        Buttons
    },
    data() {
        return {
            valid: false,
            showPass: false,
            nameInput: this.group ? this.group.name : "",
            idInput: this.group ? this.groupID : "",
            newPass: false,
            passInput: "",
            members: this.group ? this.group.members.map(e => e.name) : ["", ""],

            required: value => !!value || "Cannot be be empty",
            validID: value => /^[a-zA-Z0-9-]*$/.test(value) || "Must only contain letters, numbers and dashes",
            noDuplicate: value => {
                let count = 0;
                for (const member of this.members)
                    if (member === value)
                        ++count;

                return count < 2 || "This name is already taken";
            }
        }
    },
    props: {
        group: Object,
        groupID: String,
    },
    computed: {
        newGroup() {
            return this.group === null;
        }
    },
    watch: {
        nameInput(value) {
            if (!this.group)
                this.idInput = value.toLowerCase().trim().replace(/[ _]/g, "-").replace(/[^a-zA-Z0-9-]/g, "");
        },
        idInput(value) {
            this.idInput = value.toLowerCase();
        },
    },
    methods: {
        submit() {
            const data = {
                name: this.nameInput,
                path: this.idInput,
                members: this.members
            };
            if (this.newGroup || this.newPass)
                data.pass = this.passInput;

            this.$emit("edit", data);
        },
        deletable(index) {
            if (!this.group)
                return this.members.length > 2;

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
  height: 50px;
  background: #eee;
}

.v-btn .v-icon {
  margin-right: 10px;
}

.new-pass {
  margin-left: 20px;
}
</style>
