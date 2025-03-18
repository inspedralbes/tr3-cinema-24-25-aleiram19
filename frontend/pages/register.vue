<template>
  <div>
    <LoginForm :isLoginProp="false" />
  </div>
</template>

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