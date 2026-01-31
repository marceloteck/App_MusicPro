<template>
    <div :style="{ color: color || '#ffffff' }" class="app-logo">
        Tocare Music
    </div>
    <!-- 
    <img :src="logo" :class="['logo', { 'noIndexLogo': !route().current('index.home') }]" alt="Portfólio" />
    <img style="width: 80px" :src="logodark" class="logo" alt="Portfólio" /> 
    -->
</template>

<style scoped>
.app-logo {
    font-size: 2rem;
    font-weight: bold;
}
</style>

<script setup>
defineProps({
    color: {
        type: String,
        default: '#ffffff' // Define branco como cor padrão
    }
});
</script>
