<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { user, avatarUrl } from "@resources/plugins/DadosUser.js";
import { NavRouterSubPage } from '@resources/plugins/NavRouterLink.js';

const props = defineProps({
  title: {
    type: String,
    required: true
  }
});
const pageTitle = props.title;

const RefUserModal = ref(null);

// Simulando o usuário logado
const users = ref({
  name: user.value.name,
  photo: avatarUrl 
});


const backPage = () => {
  /*if (window.history.length > 1) {
    window.history.back();
  } else {
    
  }*/
  router.visit('/');
};

const Active = (routeActive) => { if (route().current(routeActive)) return 'active'; }



</script>
<template>
<ColorNav />
    <div class="LayoutSupPage">

        <nav class="navbar">
          <div class="container-fluid">
              <a @click="backPage" class="navbar-brand">
                <div class="title_supPage">
                  <span class="material-symbols-outlined">keyboard_backspace</span>{{ pageTitle }}
                </div>
              </a>   
          </div>
        </nav>

        <div class="icon-bar">
          <Link v-for="Nav in NavRouterSubPage" :key="Nav" :href="route(Nav.route)" :class="Active(Nav.route)">
            <span class="material-symbols-outlined">{{ Nav.icon }}</span>
            <label>{{ Nav.title }}</label>
          </Link> 
        </div>

    </div>
</template>

<style scoped>
.LayoutSupPage .navbar{
    width: 100vw;
    background-color: #ededed;
    overflow: auto;
    position: fixed !important;
    top: -1px;
    border-bottom: 1px solid #e5e3e3;
    display: flex;
    color: #333;

  }



</style>