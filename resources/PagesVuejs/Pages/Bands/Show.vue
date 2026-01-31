<template>
  <AppHead title="Bandas e Listas de Músicas" />

  <LayoutPageAcess :title="band.name" :band="band">
    <div v-if="message" class="text-center p-4">
      <h2>{{ message }}</h2>
      <button class="btn btn-primary mt-3" @click="goBack">Voltar para minhas bandas</button>
      <img src="/Assets/image/error/403.webp" alt="Logo" width="200" class="mt-3" />
    </div>

    <div v-if="band">
      <!-- Nav Tabs -->
      <ul class="nav nav-tabs nav-fill mb-3" id="bandTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab">Eventos Ativos</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="evconcluido-tab" data-bs-toggle="tab" data-bs-target="#evconcluido" type="button" role="tab">Eventos Concluidos</button>
        </li>        
      </ul>

      <!-- Tab Content -->
      <div class="tab-content" id="bandTabContent">
        <!-- Membros -->
        <div class="tab-pane fade" id="evconcluido" role="tabpanel">
          <div class="row">
              <div class="col-md-6"> 
                <div class="container p-3">
                  <div v-for="member in band.members" :key="member.id" class="mb-2 member-item">
                    {{ member.user.name }} <span class="text-muted">{{ member.role }}</span>
                  </div>
                </div>
              </div>
          </div>
        </div>

        <!-- Eventos -->
<div class="tab-pane fade show active" id="events" role="tabpanel">
  <div class="row">
    <div class="col-md-6"> 
      <div class="container p-3"> 
        <div v-if="isAdmin || isEditor" class="mb-4">
          <button class="btn btn-outline-primary w-100 mb-3" @click="toggleEventForm">
            {{ showEventForm ? 'Cancelar' : 'Adicionar Novo Evento' }}
          </button>

          <div v-if="showEventForm">
            <form @submit.prevent="createEvent">
              <input v-model="newEvent.title" placeholder="Título" class="form-control mb-2" required />
              <input v-model="newEvent.date" type="date" class="form-control mb-2" required />
              <textarea v-model="newEvent.description" placeholder="Descrição" class="form-control mb-2"></textarea>
              <button type="submit" class="btn btn-danger w-100">Criar Evento</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div v-if="events.length">
    <div class="row">
      <div class="col-md-6">
        <div class="container p-3">
          <div v-for="event in events" :key="event.id" @click="viewEvent(event.id)" class="card mb-2 p-2 shadow-sm">
            <h5>{{ event.title }}</h5>
            <p class="mb-0"><strong>Data:</strong> {{ formatDate(event.date) }}</p>
            <p v-if="event.description">{{ event.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else>
    <p class="text-muted">Não há eventos cadastrados.</p>
  </div>
</div>



      </div>
    </div>
  </LayoutPageAcess>
</template>


<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const showEventForm = ref(false)

function toggleEventForm() {
  showEventForm.value = !showEventForm.value
}


const props = defineProps({
  band: { type: Object, required: true },
  events: { type: Array, required: true },
  message: { type: String, required: false }
})

const page = usePage()
const currentUserId = computed(() => page.props.auth.user.id)

const newEvent = ref({
  title: '',
  date: '',
  description: '',
})

const goBack = () => {
  router.visit('/bands')
}

const currentMember = computed(() => {
  if (!props.band?.members || !currentUserId.value) return null;
  return props.band.members.find(m => m.user_id === currentUserId.value) || null;
});

const isAdmin = computed(() => currentMember.value?.role === 'admin');
const isEditor = computed(() => currentMember.value?.role === 'editor');


const formatDate = date => new Date(date).toLocaleDateString('pt-BR')

const createEvent = async () => {
  await router.post(`/bands/${props.band.id}/events`, newEvent.value, {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire('Evento criado!', '', 'success')
      newEvent.value = { title: '', date: '', description: '' }
    },
    onError: errors => {
      Swal.fire('Erro', Object.values(errors).flat().join('\n'), 'error')
    }
  })
}

const viewEvent = id => router.visit(`/bands/${props.band.id}/events/${id}`)
</script>

<style scoped>

.member-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  border: 1px solid #eee;
  border-radius: 4px;
  margin-bottom: 0.5rem;
}

.member-info {
  display: flex;
  flex-direction: column;
}

.member-name {
  font-weight: 500;
}

.band-show {
  padding: 1rem;
}

.alert {
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 4px;
}

.alert-danger {
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  color: #721c24;
}

.members-list, .events-list {
  margin: 1rem 0;
}

.member-item, .event-item {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-bottom: 0.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.member-item:hover, .event-item:hover {
  background-color: #f8f9fa;
  border-color: #28a745;
}

.member-name {
  font-weight: bold;
  margin-right: 1rem;
}

.member-role {
  color: #666;
}

.create-list {
  margin-top: 1rem;
  padding: 1rem;
  background-color: #f8f9fa;
  border-radius: 4px;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-group input, .form-group textarea {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  transition: border-color 0.3s ease;
}

.form-group input:focus, .form-group textarea:focus {
  outline: none;
  border-color: #28a745;
}

.btn-create {
  padding: 0.5rem 1rem;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-create:hover {
  background-color: #218838;
}
</style>
