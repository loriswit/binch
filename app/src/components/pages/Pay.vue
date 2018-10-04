<template>
  <v-form v-if="group" v-model="valid" @submit.prevent="submit()">
    <h1>Round payed by {{ payer }}</h1>
    <v-text-field class="price"
                  v-model="price"
                  :rules="[required, positive]"
                  type="number"
                  :autofocus="!price"
                  outline
                  single-line
                  reverse
                  suffix="Price"
    ></v-text-field>

    <h2>Consumers</h2>
    <v-list>
      <v-list-tile v-for="(member, i) in group.members" :key="i">
        <v-list-tile-content>
          <v-list-tile-title>{{ member.name }}</v-list-tile-title>
        </v-list-tile-content>

        <v-list-tile-action class="amount-buttons">
          <v-btn icon ripple
                 @click="--consumers[member.name]"
                 :disabled="consumers[member.name] < 1">
            <v-icon color="primary">remove_circle</v-icon>
          </v-btn>
          <span class="amount">{{ consumers[member.name] }}</span>
          <v-btn icon ripple
                 @click="++consumers[member.name]">
            <v-icon color="primary">add_circle</v-icon>
          </v-btn>
        </v-list-tile-action>
      </v-list-tile>
    </v-list>
    <Buttons text="Pay"
             :valid="valid && enoughConsumers"
             @cancel="$emit('page', 'Group')"/>
  </v-form>
</template>

<script>
import "@/assets/css/form.css"
import Buttons from "@/components/Buttons";

export default {
    name: "Pay",
    components: {Buttons},
    data: function () {
        const consumers = {};
        if (this.group)
            for (const member of this.group.members)
                consumers[member.name] = 0;

        return {
            valid: false,
            price: null,
            consumers: consumers,

            required: value => !!value || "Cannot be be empty",
            positive: value => value > 0 || "Must be positive",
        }
    },
    computed: {
        enoughConsumers() {
            return Object.values(this.consumers).some(amount => amount > 0);
        }
    },
    props: {
        group: Object,
        payer: String
    },
    methods: {
        submit() {
            const data = {
                payer: this.payer,
                price: parseInt(this.price * 100),
                consumers: this.consumers
            };

            localStorage.setItem("price", data.price);

            this.$emit("pay", data);
        }
    },
    created() {
        const price = localStorage.getItem("price");
        if (price)
            this.price = (parseFloat(price) / 100).toFixed(2);
    },
    beforeCreate() {
        scrollTo(0, 0);
    }
}
</script>

<style scoped>
.price {
  width: 140px;
  margin: auto;
}

.amount-buttons {
  flex-direction: row;
  align-items: center;
}

.amount-buttons .v-icon {
  font-size: 2.25em;
}

.amount {
  font-size: 1.2em;
  font-weight: bold;
  margin-left: 15px;
  margin-right: 15px;
}
</style>
