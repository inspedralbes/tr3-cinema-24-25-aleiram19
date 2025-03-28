<template>
  <div class="min-h-screen flex flex-col bg-[#051D40]">
    <LandingPageNavBar />
    <div class="pt-20"><PromocionesDetalle /></div>
    <LandingPageFooter />
  </div>
</template>

<script>
export default {
  name: 'PromocionesPage',
  head() {
    return {
      title: 'Promociones - CineXeperience',
      meta: [
        { hid: 'description', name: 'description', content: 'Descubre las mejores promociones y ofertas en CineXeperience' }
      ]
    }
  }
}
</script>
