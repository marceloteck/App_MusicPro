<script setup>
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  sources: {
    type: Array,
    default: () => [],
  },
});

const Title = 'Fontes externas';

const defaultSelectors = {
  result_item: "//div[contains(@class,'item') or contains(@class,'result')]",
  result_link: ".//a",
  result_title: ".//*[self::h1 or self::h2 or self::h3]",
  result_artist: ".//*[contains(@class,'artist') or contains(@class,'autor')]",
};

const form = useForm({
  name: '',
  base_url: '',
  search_url: '',
  enabled: true,
  selectors: { ...defaultSelectors },
});

const editForm = ref(null);
const selectorsText = ref(JSON.stringify(form.selectors, null, 2));
const editSelectorsText = ref('');

const startEdit = (source) => {
  editForm.value = useForm({
    name: source.name,
    base_url: source.base_url,
    search_url: source.search_url,
    enabled: source.enabled,
    selectors: source.selectors || { ...defaultSelectors },
  });
  editForm.value.id = source.id;
  editSelectorsText.value = JSON.stringify(editForm.value.selectors, null, 2);
};

const submit = () => {
  try {
    form.selectors = JSON.parse(selectorsText.value || '{}');
  } catch (error) {
    alert('Seletores inválidos. Verifique o JSON.');
    return;
  }
  form.post(route('admin.sources.store'), {
    preserveScroll: true,
    onSuccess: () => form.reset('name', 'base_url', 'search_url'),
  });
};

const update = () => {
  if (!editForm.value?.id) return;
  try {
    editForm.value.selectors = JSON.parse(editSelectorsText.value || '{}');
  } catch (error) {
    alert('Seletores inválidos. Verifique o JSON.');
    return;
  }
  editForm.value.put(route('admin.sources.update', editForm.value.id), {
    preserveScroll: true,
  });
};

const removeSource = (id) => {
  if (!confirm('Remover esta fonte?')) return;
  useForm({}).delete(route('admin.sources.destroy', id), {
    preserveScroll: true,
  });
};

const hasEdit = computed(() => !!editForm.value?.id);
</script>

<template>
  <AppHead :title="Title" />
  <LayoutSubPages :title="Title">
    <div class="container-fluid">
      <p class="text-muted">
        Cadastre fontes externas informando URLs de busca e seletores XPath para capturar títulos,
        artistas e links de cifras.
      </p>

      <div class="row">
        <div class="col-lg-6">
          <div class="card mb-4">
            <div class="card-body">
              <h5 class="card-title">Nova fonte</h5>
              <form @submit.prevent="submit">
                <input v-model="form.name" class="form-control" placeholder="Nome" required />
                <input v-model="form.base_url" class="form-control" placeholder="Base URL" required />
                <input v-model="form.search_url" class="form-control" placeholder="URL de busca (use {query})" />
                <label class="form-label">Seletores XPath</label>
                <textarea class="form-control" rows="6" v-model="selectorsText"></textarea>
                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" v-model="form.enabled" id="enabled" />
                  <label class="form-check-label" for="enabled">Ativo</label>
                </div>
                <button class="btn btn-primary mt-3" type="submit" :disabled="form.processing">
                  Salvar
                </button>
              </form>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card mb-4" v-if="hasEdit">
            <div class="card-body">
              <h5 class="card-title">Editar fonte</h5>
              <form @submit.prevent="update">
                <input v-model="editForm.name" class="form-control" placeholder="Nome" required />
                <input v-model="editForm.base_url" class="form-control" placeholder="Base URL" required />
                <input v-model="editForm.search_url" class="form-control" placeholder="URL de busca" />
                <label class="form-label">Seletores XPath</label>
                <textarea class="form-control" rows="6" v-model="editSelectorsText"></textarea>
                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" v-model="editForm.enabled" id="enabledEdit" />
                  <label class="form-check-label" for="enabledEdit">Ativo</label>
                </div>
                <button class="btn btn-primary mt-3" type="submit" :disabled="editForm.processing">
                  Atualizar
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">Fontes cadastradas</div>
        <div class="card-body p-0">
          <table class="table mb-0">
            <thead>
              <tr>
                <th>Nome</th>
                <th>Base</th>
                <th>Busca</th>
                <th>Status</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="source in props.sources" :key="source.id">
                <td>{{ source.name }}</td>
                <td class="text-break">{{ source.base_url }}</td>
                <td class="text-break">{{ source.search_url }}</td>
                <td>
                  <span class="badge" :class="source.enabled ? 'bg-success' : 'bg-secondary'">
                    {{ source.enabled ? 'Ativo' : 'Inativo' }}
                  </span>
                </td>
                <td>
                  <button class="btn btn-sm btn-outline-primary me-2" @click="startEdit(source)">
                    Editar
                  </button>
                  <button class="btn btn-sm btn-outline-danger" @click="removeSource(source.id)">
                    Remover
                  </button>
                </td>
              </tr>
              <tr v-if="props.sources.length === 0">
                <td colspan="5" class="text-center text-muted">
                  Nenhuma fonte cadastrada ainda.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </LayoutSubPages>
</template>

<style scoped>
input,
textarea {
  margin-bottom: 12px;
}
</style>
