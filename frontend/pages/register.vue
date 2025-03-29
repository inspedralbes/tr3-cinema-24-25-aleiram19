<template>
  <div>
    <LoginForm :isLoginProp="false" />
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router';

const route = useRoute();
// El componente LoginForm ya maneja el parámetro redirect de la URL
</script>

<script>
export default {
  name: 'RegisterPage',
  head() {
    return {
      title: 'Registrarse - CineXeperience',
      meta: [
        { hid: 'description', name: 'description', content: 'Crea una cuenta en CineXeperience' }
      ]
    }
  }
}
</script>