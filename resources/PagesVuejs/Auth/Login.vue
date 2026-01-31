<template>
<AppHead title="Login" />
<div class="container-fluid">
  <div class="login-container">
    <LogoInicial />

    <!-- Login com Google -->
    <button @click="loginWithGoogle" class="btn google-btn mb-3">
      <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" width="20" />
      Entrar com Google
    </button>

    <hr class="my-3" style="border-color: rgba(255,255,255,0.2);" />

    <div v-if="status" :class="['alert', status === 'success' ? 'alert-success' : 'alert-danger', 'alert-dismissible']" role="alert">
          <center><div v-html="message"></div></center>
      </div>

    <!-- Login Manual -->
    <form @submit.prevent="submitLogin">
      <div class="input-group">
        <i class="bi bi-person"></i>
        <input v-model="form.email" type="email" class="form-control" placeholder="Email" required />
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

    <div class="form-check">
      <input v-model="form.remember" class="form-check-input" type="checkbox" id="rememberMe">
      <label class="form-check-label" for="rememberMe">
        Manter-me conectado
      </label>
    </div>


      <button type="submit" class="btn login-btn">Entrar</button>
    </form>

    <div class="link">
            <Link :href="route('password.request')">Esqueci minha senha</Link> |
            <Link :href="route('register')">Criar conta</Link>
        </div>

  </div>
</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    status: String, // "success" ou "error"
    message: String // Texto da mensagem
});

const showPassword = ref(false);

const togglePassword = () => {
  showPassword.value = !showPassword.value;
};

    const form = ref({ 
      email: '', 
      password: '', 
      remember: false 
      });

    const submitLogin = () => {
      router.post('/login', form.value);
    };

    const loginWithGoogle = () => {
    window.location.href = '/auth/google';
};
</script>

<style scoped>
.form-check, .form-check-label{
  text-align: left;
}

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
