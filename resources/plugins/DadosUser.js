import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();

// Garante que os dados do usuário sejam acessados corretamente
const user = computed(() => page.props?.auth?.user ?? null);

// Gera a URL do avatar do usuário
const avatarUrl = computed(() => {
    if (user.value?.avatar) {
        return user.value.avatar; // Se o usuário tiver um avatar, usa a URL fornecida
    } else if (user.value?.name) {
        const initial = user.value.name.charAt(0).toUpperCase(); // Pega a primeira letra do nome
        return `https://ui-avatars.com/api/?name=${initial}&background=random&color=fff`; // Gera um avatar baseado na inicial
    }
    return "/default-avatar.png"; // Usa um avatar padrão caso não tenha nome ou avatar
});

export { user, avatarUrl };


/* COMO USAR
if (user.value) {
  console.log("ID:", user.value.id);
  console.log("Nome:", user.value.name);
  console.log("Email:", user.value.email);
  console.log("avatarUrl:", avatarUrl);
}
*/
