export default defineNuxtPlugin({
  hooks: {
    'app:mounted': () => {
      document.documentElement.dataset.hydrated = 'true'
    },
  },
})
