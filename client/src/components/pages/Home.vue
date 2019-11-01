<template>
  <div id="home">
    <div v-if="recentGroups">
      <h2>Recent groups</h2>
      <v-list>
        <v-list-item v-for="(group, i) in groups" :key="i">
          <v-list-item-content>
            <v-list-item-title>{{ group.name }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </div>
    <h1>{{ $t("home.title") }}</h1>
    <div id="buttons">
      <label>{{ $t("home.goto.label") }}</label>
      <v-btn @click="$emit('page', 'Goto')" color="primary" depressed>{{ $t("home.goto.button") }}</v-btn>
      <label>{{ $t("home.new.label") }}</label>
      <v-btn @click="$emit('page', 'New')" color="primary" depressed>{{ $t("home.new.button") }}</v-btn>
    </div>
  </div>
</template>

<script>
export default {
    name: "Home",
    data() {
        return {
            recentGroups: null
        };
    },
    created() {
        this.$emit("group", null);
    }
}
</script>

<style scoped>
#home {
  text-align: center;
}

h1 {
  margin-top: 20px;
  font-weight: normal;
}

#buttons {
  display: flex;
  flex-direction: column;
  align-items: center;
}

#buttons label {
  font-size: 1.25em;
  margin-top: 30px;
}

#buttons .v-btn {
  font-size: 1.75em;
  width: 290px;
  height: 70px;
  border-radius: 0;
  margin: 10px;
}
</style>
