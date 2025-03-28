<template>
  <div>
    <login-GuestForm />
  </div>
</template>

<script>
export default {
  name: 'GuestCheckoutPage',
  head() {
    return {
      title: 'Checkout como Invitado - CineXperience',
      meta: [
        { hid: 'description', name: 'description', content: 'Continuar con la compra como invitado en CineXperience' }
      ]
    }
  }
}
</script>
