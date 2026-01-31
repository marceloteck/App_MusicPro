<template>
<CapaAbertura type="time"/>
<Loading />
<NavBarSubPage :title="props.title"/>
    <div class="layoutPage">
        <slot />
    </div>
</template>

<style lang="css" scoped>
.layoutPage{
    margin-top: 60px;
}
</style>
<script setup>
const props = defineProps({
  title: {
    type: String,
    required: true
  }
});
</script>
