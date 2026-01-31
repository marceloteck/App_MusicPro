<template>
<AppHead title="Cadastrar nova conta" />
<div class="container-fluid">
  <div class="login-container">
    <LogoInicial />

    <!-- Login com Google -->
    <button @click="loginWithGoogle" class="btn google-btn mb-3">
      <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" width="20" />
      Cadastrar com Google
    </button>

    <hr class="my-3" style="border-color: rgba(255,255,255,0.2);" />

    <!-- Login Manual -->
    <form @submit.prevent="submitLogin">
        <div v-if="status" :class="['alert', status === 'success' ? 'alert-success' : 'alert-danger', 'alert-dismissible']" role="alert">
          <center><div v-html="message"></div></center>
      </div>

        <div v-if="aviso" :class="aviso ? 'alert alert-danger' : ''">
            {{ aviso }}
        </div>
        <hr v-if="status || aviso" class="my-3" style="border-color: rgba(255,255,255,0.2);" />

        <div class="input-group">
        <i class="bi bi-person"></i>
        <input v-model="form.name" type="text" class="form-control" placeholder="Seu nome" required />
      </div>
      <div class="input-group">
        <i class="bi bi-envelope"></i>
        <input v-model="form.email" @blur="validarEmail" type="email" class="form-control" placeholder="Email" required />
      </div>



      <div class="input-group">
      <span><i class="bi bi-lock"></i></span>
      <input
        :type="showPassword ? 'text' : 'password'"
        v-model="form.password"
        class="form-control"
        placeholder="Senha"
        minlength="8" 
        title="A senha deve ter no mínimo 8 caracteres"
        required
      />
      <button class="btn" type="button" @click="togglePassword">
        <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
      </button>
    </div>

    <div class="input-group">
      <span><i class="bi bi-lock"></i></span>
      <input
        :type="showPassword_confirmation ? 'text' : 'password'"
        v-model="form.password_confirmation"
        class="form-control"
        placeholder="Confirme a Senha"
        minlength="8" 
        title="A senha deve ter no mínimo 8 caracteres"
        required
      />
      <button class="btn" type="button" @click="togglepassword_confirmation">
        <i :class="showPassword_confirmation ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
      </button>
    </div>

      <button type="submit" class="btn login-btn" >Criar conta</button>
    </form>

    <div class="link">
            <Link :href="route('login')">Login</Link>
        </div>

         

  </div>
</div>
</template>



<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: String, // "success" ou "error"
    message: String // Texto da mensagem
});

// Formulário usando useForm do Inertia.js
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '', // Laravel espera esse campo para confirmar a senha
});


const showPassword = ref(false);
const showPassword_confirmation = ref(false);

const togglePassword = () => {
  showPassword.value = !showPassword.value;
};

const togglepassword_confirmation = () => {
  showPassword_confirmation.value = !showPassword_confirmation.value;
};


const aviso = ref(null); // Armazena a mensagem de erro

const validarEmail = () => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Expressão regular para validar e-mails

  if (!emailRegex.test(form.email)) {
    aviso.value = 'Por favor, insira um e-mail válido.';
  } else {
    aviso.value = '';
  }
};

// Observa mudanças na senha para validar automaticamente
watch([() => form.password, () => form.password_confirmation], () => {
    if (form.password.length < 8){
        aviso.value = 'A senha deve ser no minimo 8 caracteres.';
    }else
    if (form.password !== form.password_confirmation) {
        aviso.value = 'As senhas devem ser iguais!';
    } else {
        aviso.value = null; // Remove o aviso se as senhas forem iguais
    }
});

// Função para validar e enviar o formulário
const submitLogin = () => {
    if (aviso.value) return; // Impede o envio caso as senhas estejam erradas

    form.post('/register', {
            preserveScroll: true,
            onSuccess: (response) => {
                //
            },
            onError: (errors) => {
                //
            }
        });
};

// Função para login com Google
const loginWithGoogle = () => {
    window.location.href = '/auth/google';
};
</script>




<style scoped>
.link {
    margin-top: 10px;
    font-size: 14px;
}

.link a,Link {
    color: aliceblue;
    text-decoration: none;
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
.login-container {
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
.login-btn {
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
.login-btn:hover {
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
