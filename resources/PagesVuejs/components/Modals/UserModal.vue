<template>
  <div class="UserModalClass">
    <LayoutModalMobile
      ref="RefUserModal"
      :height="vhModal"
    >
       <div class="user-info" v-if="users">
          <img :src="users.avatar" alt="Foto de Perfil" class="profile-pic">
          <h3>{{ users.name }}</h3>
          <p>{{ users.email }}</p>
        </div>

        <hr v-if="users">
        <!-- Opções do usuário -->
        <div class="actions">
          <template v-if="users">
            <Link href="/profile" class="option">
              <span class="material-symbols-outlined">edit_square</span>&nbsp;&nbsp; Editar Perfil
            </Link>
            <a class="option logout" @click="logout">
              <span class="material-symbols-outlined">logout</span>&nbsp;&nbsp; Sair
            </a>
          </template> 
          <template v-else>
            <Link :href="route('login')" class="option">
              <span class="material-icons">login</span>&nbsp;&nbsp;  Login
            </Link>
            <Link :href="route('register')" class="option">
              <span class="material-icons">person_add</span>&nbsp;&nbsp;  Cadastro
            </Link>
          </template>
        </div>
      
    </LayoutModalMobile>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { user, avatarUrl } from "@resources/plugins/DadosUser.js";

const users = ref({
  name: user.value.name,
  email: user.value.email,
  avatar: avatarUrl
});

const isModalOpen = ref(false);
let vhModal = users.value ? "29vh" : "20vh";

const RefUserModal = ref(null);
const ExecuteUserModal = () => {
  if (RefUserModal.value) {
    RefUserModal.value.openModal(); 
  }
};

defineExpose({ ExecuteUserModal });


const logout = () => {
  router.post('/logout');
};
</script>

<style scoped>

.UserModalClass hr {
    border: none; /* Remove a borda padrão */
    border-top: 1px solid rgb(131, 131, 131); /* Define a cor e espessura */
    margin: 10px 0; /* Ajusta o espaçamento */
}

.UserModalClass .user-info{
    border-bottom: 1x solid #000;
}

.UserModalClass .user-info img{
    width: 40px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid white;
    float: left;
}
.UserModalClass .user-info h3{
    font-size: 1rem;
}
.UserModalClass .user-info h3, p{
    padding-left: 8px;
    float: left;
    line-height: 0.2rem;
    margin-top: 9px;
    min-width: 80%;
    max-width: 100%;
}
.UserModalClass .user-info p{
  font-size: 0.8rem;
}
.UserModalClass  .actions{
    width: 100%;
}
.UserModalClass  .actions a, link{
    float: left;
    text-decoration: none;
    width: 100%;
    padding: 9px;
    line-height: 0;
    align-content: flex-start;
    display: flex;
    align-items: center;
}
</style>
