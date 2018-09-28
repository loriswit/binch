<template>
  <div id="home">
    <div v-if="recentGroups">
      <h2>Recent groups</h2>
      <v-list>
        <v-list-tile v-for="(group, i) in groups" :key="i">
          <v-list-tile-content>
            <v-list-tile-title>{{ group.name }}</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
      </v-list>
    </div>
    <h1>Welcome to BinchApp</h1>
    <div id="buttons">
      <label>visit a group page</label>
      <v-btn @click="$emit('page', 'Goto')" color="primary" depressed>Go to group</v-btn>
      <label>or create a new group</label>
      <v-btn @click="$emit('page', 'New')" color="primary" depressed>Create group</v-btn>
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
    methods: {
        updateGroup() {

        }
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

.v-btn {
  font-size: 1.75em;
  width: 270px;
  height: 70px;
  border-radius: 0;
}
</style>
