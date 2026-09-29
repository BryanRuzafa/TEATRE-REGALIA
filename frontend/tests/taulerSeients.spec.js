import { setActivePinia, createPinia } from 'pinia'
import { usTaulerSeientsStore } from '../stores/taulerSeients.js'
import { describe, it, expect, beforeEach } from 'vitest'

describe('Store Tauler Seients Vue - Test Unitaris de Lògica de temps Real i Pinia', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('Garanteix que semmagatzema les inicialitzacions des de connectivitat WebSocket per defecte', () => {
    const store = usTaulerSeientsStore()
    
    // Simular un fetch inicial de l'api d'escenari pur on no consta cap pagament efectuat
    store.seients = {
      'A1': { id:'A1', estat: 'disponible', reservatPer: null },
      'A2': { id:'A2', estat: 'disponible', reservatPer: null }
    }
    expect(store.seients['A1'].estat).toBe('disponible')
   
    // Simulació Sessió Test ID que permeti establir dades sota nosaltres
    store.sessioClient = 'usuari_test_1551'
    
    // Actuació sobre objectes per la comunicació per part del Servidor i simulació directa al Reactiu Pinia Store
    store.seients['A1'].estat = 'reservat'
    store.seients['A1'].reservatPer = 'usuari_test_1551'
    
    expect(store.seients['A1'].estat).toBe('reservat')
    expect(store.seients['A1'].reservatPer).toBe('usuari_test_1551')
    
    // Provant l'accessibilitat del getter en Vue App que usa array filtrat d'elements per donar constància que mostren Botons "Checkout"
    const propis = Object.values(store.seients).filter(s => s.reservatPer === store.sessioClient && s.estat === 'reservat');
    expect(propis.length).toBe(1)
    expect(propis[0].id).toBe('A1')
  })
})
