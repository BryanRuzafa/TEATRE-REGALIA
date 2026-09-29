export default defineEventHandler(async (event) => {
  const backendUrl = 'http://127.0.0.1:8000/api'
  const path = event.path.replace(/^\/api/, '')
  const target = `${backendUrl}${path}`

  console.log(`[API Proxy] ${event.method} ${event.path}`)

  try {
    // Escollim el mètode de petició manual per tenir control total sobre l'error
    const body = event.method !== 'GET' ? await readBody(event).catch(() => undefined) : undefined
    
    return await $fetch(target, {
      method: event.method,
      headers: getProxyRequestHeaders(event),
      body: body,
      retry: 0 // No reintentar per veure l'error real
    })
  } catch (error) {
    const errorMsg = error.response?._data?.message || error.message
    console.error(`[BRIDGE ERROR] Destí: ${target} | Error: ${errorMsg}`)
    
    throw createError({
      statusCode: 502,
      statusMessage: `Error de connexió interna: ${errorMsg}`
    })
  }
})
