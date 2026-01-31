<script setup>
import { ref } from 'vue';
import { NavRouterIndex } from '@resources/plugins/NavRouterLink.js';

const RefUsernNavBar = ref(null);

const RefUserModal = ref(null);
const ExecuteUserModal = () => {
  if (RefUserModal.value) {
    RefUserModal.value.ExecuteUserModal(); // Chama a função do filho
  }
};

const Active = (routeActive) => { if (route().current(routeActive)) return 'active'; }

</script>
<template>
<ColorNav />
<UserModal ref="RefUserModal" />
    <div class="NavBarMobile">
        <NavbarTop @chamarfuncao="ExecuteUserModal" />

        <div class="icon-bar">
          <Link v-for="Nav in NavRouterIndex" :key="Nav" :href="route(Nav.route)" :class="Active(Nav.route)">
            <span class="material-symbols-outlined">{{ Nav.icon }}</span>
            <label>{{ Nav.title }}</label>
          </Link> 
        </div>
    </div>
</template>
<style scoped>

</style>