export default defineNuxtRouteMiddleware(async () => {
  const { user, initialize } = useAuth()
  await initialize()

  if (!user.value) return navigateTo('/login')
})
