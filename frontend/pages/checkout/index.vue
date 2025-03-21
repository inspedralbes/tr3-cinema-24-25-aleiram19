<template>
  <div>
    <login-UserLoginOptions />
  </div>
</template>

<script>
export default {
  name: 'CheckoutOptionsPage',
  head() {
    return {
      title: 'Completar Compra - CineXperience',
      meta: [
        { hid: 'description', name: 'description', content: 'Elige cómo deseas continuar con tu compra en CineXperience' }
      ]
    }
  }
}
</script>
