<template>
<AppHead title="Cadastrado com sucesso" />
<div class="container-fluid">
  <div class="login-container">
    <LogoInicial />

    <h2>Cadastro realizado com sucesso!</h2>
    <!-- Login com Google -->
    <button @click="goApp" class="btn google-btn mb-3">
      <img src="/Assets/image/Logo/favicon2.jpg" alt="App tocare Music" width="20" />
      Acessar o App
    </button>

         

  </div>
</div>
</template>



<script setup>
//import {  Inertia  } from '@inertiajs/vue3';

const props = defineProps({
    status: String, // "success" ou "error"
    message: String // Texto da mensagem
});

const goApp = () => {
     window.location.href = '/';
    //Inertia.visit(route('index.home'));
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
