<script setup>
import { ref } from 'vue';
import { NavRouterPlayer } from '@resources/plugins/NavRouterLink.js';

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
    <div class="NavBarPlayer">
      <NavbarTop @chamarfuncao="ExecuteUserModal"/>

        <div class="icon-bar">
          <Link v-for="Nav in NavRouterPlayer" :key="Nav" :href="route(Nav.route)" :class="Active(Nav.route)">
            <span class="material-symbols-outlined">{{ Nav.icon }}</span>
            <label>{{ Nav.title }}</label>
          </Link> 
        </div>
    </div>
</template>
<style scoped>

.NavBarPlayer .icon-bar {
  background-color: rgba(230, 235, 236, 0.3);
  backdrop-filter: blur(8px);
}
.NavBarPlayer .icon-bar .active {
    color: #166bb9 !important;
    font-weight: bold;
    border-top: 3px solid #166bb9;
}

.NavBarPlayer .icon-bar .active label {
    color: #166bb9 !important;
}

.alertaMb {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: #333;
  color: #fff;
  padding: 20px;
  border-radius: 5px;
  opacity: 1;
  transition: opacity 0.5s;
}


/* User Menu */
.NavBarMobile .user-menu {
  position: relative;
  display: flex;
  align-items: center;
  cursor: pointer;
}

/* Avatar */
.NavBarMobile .user-avatar img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid white;
}
.NavBarMobile .navbar{
    width: 100vw;
    background-color: #dedede;
    overflow: auto;
    position: fixed !important;
    top: -1px;
    border-bottom: 1px solid #e5e3e3;
    display: flex;
    color: #333;

  }
.NavBarMobile .navbar span{
        margin-right: 10px;
        padding: 5px;
      
    }


/*
.NavBarMobile .cardList i{
    float: right;
    position: absolute;
    right: 15px;
    margin-top: -12px;
    padding: 15px;
  }
.NavBarMobile .cardList i{
    float: right;
    position: absolute;
    right: 15px;
    margin-top: -12px;
    padding: 15px;
  }
*/
</style>