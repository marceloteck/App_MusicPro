<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { user, avatarUrl } from "@resources/plugins/DadosUser.js";
import { NavRouterIndex } from '@resources/plugins/NavRouterLink.js';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  band: {
    type: Object,
    required: true
  }
});
const pageTitle = props.title;

const RefInfoModal = ref(null);
const page = usePage();

// Simulando o usuário logado
const users = ref({
  name: user.value.name,
  photo: avatarUrl 
});


const currentUserId = computed(() => page.props.auth.user.id)


// Membro atual da banda
const currentMember = computed(() => {
  if (!props.band?.members || !currentUserId.value) return null
  return props.band.members.find(m => m.user_id === currentUserId.value) || null
})


// Verifica se o membro é admin ou editor dentro da banda
const isAdmin = computed(() => currentMember.value?.role === 'admin')
const isEditor = computed(() => currentMember.value?.role === 'editor')

// Verifica se o usuário logado (globalmente) é admin ou editor
const isAdminOrEditor = computed(() => {
  return isAdmin.value || isEditor.value
})


const backPage = () => {
  router.visit('/bands');
};

const Active = (routeActive) => { if (route().current(routeActive)) return 'active'; }

const UserModal = () => {
  if (RefInfoModal.value) {
    RefInfoModal.value.ExecuteInfoBandModal();
  }
};


</script>
<template>
<ColorNav />
<InfoBandModal ref="RefInfoModal" />
    <div class="LayoutPageAcess">

        <nav class="navbar">
          <div class="container-fluid">
              <a @click="backPage" class="navbar-brand">
                <div class="title_supPage">
                  <span class="material-symbols-outlined">keyboard_backspace</span>{{ pageTitle }}
                </div>
              </a>   
               
              <div class="user-menu" v-if="isAdminOrEditor">
                <div class="user-avatar" @click="UserModal">
                  <span class="material-symbols-outlined">more_vert</span>
                </div>
              </div>
          </div>
        </nav>

        <!-- <div class="icon-bar">
          <Link v-for="Nav in NavRouterIndex" :key="Nav" :href="route(Nav.route)" :class="Active(Nav.route)">
            <span class="material-symbols-outlined">{{ Nav.icon }}</span>
            <label>{{ Nav.title }}</label>
          </Link> 
        </div> -->

    </div>
</template>

<style scoped>

.LayoutPageAcess .navbar{
    width: 100vw;
    background-color: #ededed;
    overflow: auto;
    position: fixed !important;
    top: -1px;
    border-bottom: 1px solid #e5e3e3;
    display: flex;
    color: #333;

  }
  .LayoutPageAcess .navbar .material-symbols-outlined{
        margin-right: 10px;
      
    }


/* User Menu */
.LayoutPageAcess .user-menu {
  position: relative;
  display: flex;
  align-items: center;
  cursor: pointer;
}

/* Avatar */
.LayoutPageAcess .user-avatar img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid white;
}
.LayoutPageAcess .navbar span{
        margin-right: 10px;
        padding: 5px;
      
    }


</style>