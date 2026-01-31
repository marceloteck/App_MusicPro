<script setup>
import { ref } from 'vue';
import { user, avatarUrl } from "@resources/plugins/DadosUser.js";

const Active = (routeActive) => { if (route().current(routeActive)) return 'active'; }


const users = ref({
  name: user.value.name,
  photo: avatarUrl 
});

const alertaVisivel = ref(false);

const copiarTexto = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href); // Copia a URL atual
    alertaVisivel.value = true;
    setTimeout(() => {
      alertaVisivel.value = false;
    }, 1500);
  } catch (err) {
    console.error("Erro ao copiar:", err);
  }
}

const emit = defineEmits(['chamarfuncao']);
const UserModal = () => {
  emit('chamarfuncao'); 
};


</script>
<template>
    <div class="TobnavBar">
        <div v-if="alertaVisivel" class="alertaMb">Link copiado para a área de transferência!</div>
        <nav class="navbar">
          <div class="container-fluid">
              <a @click="copiarTexto" class="navbar-brand">
                <b><LogoApp color="#333" /></b>
              </a>   
              <div class="user-menu">
                <div class="user-avatar" @click="UserModal()">
                    <img :src="users?.photo" alt="User Avatar" />
                </div>
              </div>
          </div>

          
              
        </nav>
    </div>
</template>
<style lang="css">
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
.TobnavBar .user-menu {
  position: relative;
  display: flex;
  align-items: center;
  cursor: pointer;
}

/* Avatar */
.TobnavBar .user-avatar img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid white;
}
.TobnavBar .navbar{
    width: 100vw;
    background-color: #dedede;
    overflow: auto;
    position: fixed !important;
    top: -1px;
    border-bottom: 1px solid #e5e3e3;
    display: flex;
    color: #333;

  }
.TobnavBar .navbar span{
        margin-right: 10px;
        padding: 5px;
      
    }


</style>