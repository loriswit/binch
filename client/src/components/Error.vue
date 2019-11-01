<template>
  <v-alert type="error">{{ error }}</v-alert>
</template>

<script>
export default {
    name: "Error",
    props: {
        error: String
    }
}
</script>

<style scoped>

</style>
