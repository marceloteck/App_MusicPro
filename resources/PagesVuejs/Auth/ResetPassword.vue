<template>
  <AppHead title="Cadastrar nova conta" />
  <div class="container-fluid">
    <div class="ResetPassword-container">
      <LogoInicial />

      <!-- Se o status NÃO for 'success_altered' ou 'ErrorSolicite', exibe o formulário -->
      <div v-if="!['success_altered', 'ErrorSolicite'].includes(status)">
        <h2>RECUPERAR SENHA</h2>
        <h5 v-if="email">
          Email: <strong>{{ email }}</strong>
        </h5>

        <hr class="my-3 border-light opacity-25" />

        <!-- Formulário de redefinição de senha -->
        <form @submit.prevent="submitResetPassword">
          <!-- Alertas -->
          <div v-if="status" :class="['alert', status === 'success' ? 'alert-success' : 'alert-danger', 'alert-dismissible']" role="alert">
            <center><div v-html="message"></div></center>
          </div>

          <div v-if="aviso" class="alert alert-danger">
            {{ aviso }}
          </div>

          <hr v-if="status || aviso" class="my-3 border-light opacity-25" />

          <!-- Campos de senha -->
          <div class="input-group">
            <i class="bi bi-lock"></i>
            <input 
              v-model="form.password"
              type="password"
              class="form-control"
              placeholder="Nova Senha"
              minlength="8"
              title="A senha deve ter no mínimo 8 caracteres"
              required
            />
          </div>

          <div class="input-group">
            <i class="bi bi-lock"></i>
            <input 
              v-model="form.password_confirmation"
              type="password"
              class="form-control"
              placeholder="Confirme a Senha"
              minlength="8"
              title="A senha deve ter no mínimo 8 caracteres"
              required
            />
          </div>

          <button type="submit" :disabled="aviso" class="btn ResetPassword-btn">
            Alterar senha
          </button>
        </form>

        <!-- Link para login -->
        <div class="link">
          <Link :href="route('login')">Login</Link>
        </div>
      </div>

      <!-- Mensagens pós-reset de senha -->
      <div v-else-if="status === 'success_altered'">
        <h2>{{ message }}</h2>
        <button class="btn btn-success" @click="submitLogin">Fazer Login</button>
      </div>

      <div v-else-if="status === 'ErrorSolicite'">
        <h2>{{ message }}</h2>
        <h4>Faça uma nova solicitação</h4>
         <div class="link">
          <Link :href="route('password.request')">Recuperar senha novamente</Link>
        </div>
      </div>
    </div>
  </div>
</template>




<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: String, // "success" ou "error"
    message: String, // Texto da mensagem
    email: String,
    token: String,
    password: String
});

// Formulário usando useForm do Inertia.js
const form = useForm({
    email: '',  
    token: '',  
    password: '',
    password_confirmation: '', 
});

// Atualiza email e token quando props forem carregados
watch(() => props, (newProps) => {
    form.email = newProps.email || '';
    form.token = newProps.token || '';
}, { immediate: true }); // Executa imediatamente quando o componente monta

const aviso = ref(null); // Armazena a mensagem de erro

// Observa mudanças na senha para validar automaticamente
watch([() => form.password, () => form.password_confirmation], () => {
    if (form.password && form.password.length < 8) {
        aviso.value = 'A senha deve ter no mínimo 8 caracteres.';
    } else if (form.password !== form.password_confirmation) {
        aviso.value = 'As senhas devem ser iguais!';
    } else {
        aviso.value = null; // Remove o aviso se as senhas forem iguais
    }
});

const submitLogin = () => {
  window.location.href = '/login';
}

// Função para validar e enviar o formulário
const submitResetPassword = () => {
    if (aviso.value) return; // Impede o envio caso as senhas estejam erradas

    form.post('/reset-password', {
        preserveScroll: true,
        onSuccess: () => {
           //
        },
        onError: (errors) => {
           // 
        }
    });
};
</script>





<style scoped>
h5 strong{
      color: #ffee05;
}
.link {
    margin-top: 10px;
    font-size: 14px;
}

.link a,Link {
    color: aliceblue;
    text-decoration: none;
    color: #ffee05;
}
.link a:hover,Link:hover{
  color:#ffffff;  
  text-decoration: underline;
}

.container-fluid{
  font-family: 'Arial', sans-serif;
  height: 99.98vh;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
 background: 
        linear-gradient(45deg, rgba(1, 74, 99, 0.9), rgba(2, 74, 111, 0.9)), 
        url("/Assets/image/backgrounds/NOTAS-EN-MUSICA.png");
    background-size: cover;
    background-position: center;
    color: white;
}
.ResetPassword-container {
  width: 100%;
  max-width: 400px;
  height: 99.98vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 2rem;
  text-align: center;
}
.input-group {
  background: rgba(255, 255, 255, 0.295);
  border-radius: 8px;
  padding: 10px;
  display: flex;
  align-items: center;
  margin-bottom: 15px;
}
.input-group input, 
.input-group input:focus {
  background: transparent;
  border: none;
  color: white;
  flex: 1;
  font-size: 1.2rem;
  padding: 5px;
  outline: none;
  
}
.input-group i {
  color: #ffffff;
  font-size: 1.5rem;
  margin-right: 10px;
}
.form-control{
  outline: none;
  box-shadow: none;
}
.form-control::placeholder{
  color: azure;
}
.ResetPassword-btn {
  background: #0b2441;
  border: none;
  border-radius: 8px;
  padding: 15px;
  color: white;
  font-size: 1.3rem;
  transition: 0.3s;
  width: 100%;
  margin-top: 10px;
}
.ResetPassword-btn:hover {
  background: #233446;
}
.google-btn {
  background: white;
  color: black;
  border-radius: 8px;
  padding: 15px;
  font-size: 1.3rem;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  margin-top: 10px;
}
.google-btn img {
  width: 24px;
}

</style>
