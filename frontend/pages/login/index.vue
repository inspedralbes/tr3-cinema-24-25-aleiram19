<template>
  <div>
    <LoginForm />
  </div>
</template>

<script>
export default {
  name: 'LoginPage',
  head() {
    return {
      title: 'Iniciar Sesión - CineXperience',
      meta: [
        { hid: 'description', name: 'description', content: 'Inicia sesión en tu cuenta de CineXperience' }
      ]
    }
  }
}
</script>
