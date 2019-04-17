<template>
  <Edit :group="null"
        :groupID="null"
        @page="$emit('page', $event)"
        @edit="$emit('edit', $event)"/>
</template>

<script>
import Edit from "@/components/pages/Edit";

export default {
    name: "New",
    components: {
        Edit
    },
    created() {
        this.$emit("group", null);
    }
}
</script>
