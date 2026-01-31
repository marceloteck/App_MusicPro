<template>
<AppHead title="Forgot" />
<div class="container-fluid">
  <div class="Forgot-container">
    <div class="app-logo">🎵 Tocare Music</div>

   <hr class="my-3" style="border-color: rgba(255,255,255,0.2);" />
    <h1>Recuperar Senha</h1>
    <p>Digite seu e-mail para receber um link de redefinição de senha.</p>
    <br>

    <form @submit.prevent="submitForgotPassword">
      
      <div v-if="status" :class="['alert', status === 'success' ? 'alert-success' : 'alert-danger', 'alert-dismissible']" role="alert">
          <center><div v-html="message"></div></center>
      </div>

      <div class="input-group">
        <i class="bi bi-envelope"></i>
        <input  class="form-control" name="email" type="email" v-model="form.email" placeholder="Digite seu e-mail" required />
      </div>


      <button type="submit" class="btn Forgot-btn">Enviar Link</button>
    </form>
     <div class="link">
            <Link :href="route('login')">Login</Link> |
            <Link :href="route('register')">Criar conta</Link>
        </div>

  </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    status: String, // "success" ou "error"
    message: String // Texto da mensagem
});


const form = ref({ email: '' });


const submitForgotPassword = () => {
  router.post('/forgot-password', form.value, {
    onSuccess: (page) => {
      //
    },
    onError: (errorMessages) => {
      //
    },
  });
};
</script>

<style scoped>
h1{
text-align: center;
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
.Forgot-container {
  width: 100%;
  max-width: 400px;
  height: 99.98vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 2rem;
  text-align: center;
}
.app-logo {
  font-size: 2rem;
  font-weight: bold;
  color: #ffffff;
  margin-bottom: 20px;
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
.Forgot-btn {
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
.Forgot-btn:hover {
  background: #233446;
}


</style>
