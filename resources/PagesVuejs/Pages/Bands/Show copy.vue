<template>
    <AppHead title="Bandas e Listas de Músicas"/>
  <div class="IndexBands">
    <LayoutIndexPages>
    <div class="band-show">
      <div v-if="error" class="alert alert-danger">
        {{ error }}
      </div>
      
      <div v-else>
        <h1>{{ band.name }}</h1>
        <p>{{ band.description }}</p>
        <p>Gênero: {{ band.genre }}</p>

        <h2>Membros</h2>
        <div class="members-list">
          <div v-for="member in band.members" :key="member.id" class="member-item">
            <span class="member-name">{{ member.user.name }}</span>
            <span class="member-role">{{ member.role }}</span>
          </div>
        </div>

        <h2>Eventos</h2>
        <div class="events-list">
          <div v-for="event in band.events" :key="event.id" class="event-item">
            <h3>{{ event.name }}</h3>
            <p>Data: {{ event.date }}</p>
          </div>
        </div>

        <h2>Listas de Eventos</h2>
        <div v-if="isAdmin || isEditor" class="create-list">
          <h3>Criar Nova Lista</h3>
          <form @submit.prevent="createList">
            <div class="form-group">
              <label for="listTitle">Título da Lista</label>
              <input type="text" id="listTitle" v-model="newList.title" required>
            </div>
            <div class="form-group">
              <label for="listDate">Data do Evento</label>
              <input type="date" id="listDate" v-model="newList.date" required>
            </div>
            <div class="form-group">
              <label for="listDescription">Descrição (opcional)</label>
              <textarea id="listDescription" v-model="newList.description"></textarea>
            </div>
            <button type="submit" class="btn-create">Criar Lista</button>
          </form>
        </div>

        <div class="lists-list">
          <div v-for="list in lists" :key="list.id" class="list-item" @click="viewList(list.id)">
            <h3>{{ list.title }}</h3>
            <p>Data: {{ list.date }}</p>
            <p v-if="list.description">Descrição: {{ list.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </LayoutIndexPages>
</div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
  band: {
    type: Object,
    required: true
  }
});

const page = usePage();
const error = computed(() => {
  return page.props.flash?.error || null;
});

const newList = ref({
  title: '',
  date: '',
  description: ''
});

const lists = ref([]);

const isAdmin = computed(() => {
  return props.band.members.some(member => member.role === 'admin');
});

const isEditor = computed(() => {
  return props.band.members.some(member => member.role === 'editor');
});

const createList = async () => {
  try {
    await router.post(`/bands/${props.band.id}/lists`, newList.value);
    newList.value = { title: '', date: '', description: '' };
    // Recarregar as listas após a criação
    loadLists();
  } catch (error) {
    console.error('Erro ao criar lista:', error);
  }
};

const loadLists = async () => {
  try {
    const response = await router.get(`/bands/${props.band.id}/lists`);
    lists.value = response.props.lists;
  } catch (error) {
    console.error('Erro ao carregar listas:', error);
  }
};

const viewList = (listId) => {
  router.visit(`/bands/${props.band.id}/lists/${listId}`);
};

onMounted(() => {
  // Verifica se o usuário tem permissão para ver a banda
  const currentUser = page.props.auth.user;
  const isMember = props.band.members.some(member => member.user_id === currentUser.id);
  
  if (!isMember) {
    // Não precisamos definir o erro aqui, pois o backend já deve ter redirecionado
    console.log('Usuário não tem permissão para ver esta banda');
  }

  loadLists();
});
</script>

<style scoped>
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

.members-list, .events-list, .lists-list {
  margin: 1rem 0;
}

.member-item, .event-item, .list-item {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-bottom: 0.5rem;
  cursor: pointer;
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
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
}

.form-group input, .form-group textarea {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.btn-create {
  padding: 0.5rem 1rem;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>
