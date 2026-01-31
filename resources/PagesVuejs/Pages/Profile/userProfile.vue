<script setup>
import { ref } from 'vue';
import { user, avatarUrl } from "@resources/plugins/DadosUser.js";

const users = ref({
  name: user.value.name || '',
  email: user.value.email || '',
  photo: avatarUrl || '' 
});

const telefone = ref('');

const formatarTelefone = () => {
  let v = telefone.value.replace(/\D/g, ""); // Remove tudo que não for número
  v = v.replace(/^(\d{2})(\d)/g, "($1) $2"); // Adiciona o DDD entre parênteses
  v = v.replace(/(\d{5})(\d)/, "$1-$2"); // Adiciona o traço no número
  telefone.value = v;
};

const Title = "Meu perfil";
</script>
<template>
<AppHead :title="Title"/>
    <div class="profilePage">
        <LayoutSubPages :title="Title">
            <div class="padding">
                <div class="col-md-8">
                    <!-- Column -->
                    <div class="cardpage">
                        <div class="card-img-top">
                        <img src="/Assets/image/backgrounds/background_music.jpeg" alt="Card image cap">
                        </div> 
                        

                        <div class="card-body little-profile text-center">
                            <div class="pro-img"><img :src="users.photo" alt="user"></div>
                            <h3 class="m-b-0">{{ users.name }}</h3>
                            <span class="m-b-0">{{ users.email }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="containermobile">
                <div class="container mt-4">
                <h2 class="text-center mb-3">Editar Perfil</h2>
                
                <div class="accordion accordion-flush" id="profileAccordion">
                    
                    <!-- Dados Pessoais -->
                    <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#personalData" aria-expanded="false">
                        Dados Pessoais
                        </button>
                    </h2>
                    <div id="personalData" class="accordion-collapse collapse" data-bs-parent="#profileAccordion">
                        <div class="accordion-body">
                        <div class="mb-3">
                            <label class="form-label">Nome Completo*</label>
                            <input type="text" class="form-control" :value="users.name" placeholder="Digite seu nome">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-mail*</label>
                            <input type="email" name="email" :value="users.email" class="form-control" placeholder="Digite seu e-mail">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telefone</label>
                             <input type="tel" v-model="telefone" class="form-control" placeholder="(XX) XXXXX-XXXX" maxlength="15" @input="formatarTelefone" />

                        </div>
                        </div>
                    </div>
                    </div>

                    <!-- Configurações de Conta -->
                    <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accountSettings" aria-expanded="false">
                        Configurações de Conta
                        </button>
                    </h2>
                    <div id="accountSettings" class="accordion-collapse collapse" data-bs-parent="#profileAccordion">
                        <div class="accordion-body">
                        <div class="mb-3">
                            <label class="form-label">Senha Atual</label>
                            <input type="password" class="form-control" placeholder="Digite sua senha atual">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nova Senha</label>
                            <input type="password" class="form-control" placeholder="Digite sua nova senha">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar Nova Senha</label>
                            <input type="password" class="form-control" placeholder="Confirme sua nova senha">
                        </div>
                        </div>
                    </div>
                    </div>

                    <!-- Preferências -->
                    <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#preferences" aria-expanded="false">
                        Preferências
                        </button>
                    </h2>
                    <div id="preferences" class="accordion-collapse collapse" data-bs-parent="#profileAccordion">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label">Tema*</label>
                                <select class="form-select">
                                    <option value="light">Claro</option>
                                    <option value="dark">Escuro</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notificações*</label>
                                <select class="form-select">
                                    <option value="enabled">Ativadas</option>
                                    <option value="disabled">Desativadas</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    </div>
                </div>
                <!-- Botão Salvar -->
                <div class="d-grid mt-4">
                    <button class="btn btn-danger btnSalve">Salvar Alterações</button>
                </div>
                </div>
            </div>
        </LayoutSubPages>

    </div>
</template>
<style lang="css" scope>
/* ============================= */
/* BOTÕES */
/* ============================= */
.btnSalve {
    background-color: #D32F2F;
    border: #ac2626;
    padding: 15px;
}

button:focus, 
button:active,
.accordion-button:focus, 
.accordion-button:active {
    outline: none !important;
    box-shadow: none !important;
}

/* ============================= */
/* FORMULÁRIOS */
/* ============================= */
.form-control,
.form-select {
    padding: 15px;
    font-size: 1.1rem;
}

.form-control:focus, 
.form-control:active {
    outline: none !important;
    box-shadow: none !important;
}

/* ============================= */
/* ACORDEON */
/* ============================= */
.accordion-button:not(.collapsed) {
    background-color: #f3f3f3;
}

.accordion-button {
    border-radius: 5px 5px 0px 0px !important;
}

/* ============================= */
/* CONTAINERS PRINCIPAIS */
/* ============================= */
.profilePage {
    overflow: auto;
    position: relative;
    width: 100vw;
    height: 100vh;
    display: flex;
    flex-direction: column;
    z-index: 2;
}

.containermobile {
    overflow: auto;
    flex: 1; /* Ocupa o espaço restante */
    padding-bottom: 350px; /* Espaço extra para evitar sobreposição */
    height: 90vh;
    width: 100vw;
    z-index: 1;
}

/* ============================= */
/* CARD */
/* ============================= */
.profilePage .cardpage {
    margin-top: -15px;
}

.profilePage .card-no-border .card {
    border-color: #d7dfe3;
    border-radius: 4px;
    margin-bottom: 30px;
    box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.05);
}

.profilePage .card-body {
    flex: 1 1 auto;
    padding: 1.25rem;
    background: #f3f3f3;
    line-height: 0.8px;
}

/* ============================= */
/* IMAGENS */
/* ============================= */
.profilePage .card-img-top {
    height: 120px;
    overflow: hidden;
}

.profilePage .card-img-top img {
    width: 170%;
    margin-top: -165px;
    margin-left: -120px;
}

.profilePage .pro-img {
    margin-top: -80px;
    margin-bottom: 20px;
}

.profilePage .little-profile .pro-img img {
    width: 128px;
    height: 128px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
    border-radius: 100%;
}

/* ============================= */
/* TEXTOS */
/* ============================= */
.profilePage h3 {
    line-height: 30px;
    font-size: 21px;
    color: #455a64;
}

.profilePage .btn-rounded.btn-md {
    padding: 12px 35px;
    font-size: 16px;
}

</style>