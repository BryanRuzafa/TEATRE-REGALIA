# Foundations: Sistema de reserva temporal de seients amb bloqueig concurrent

## Context
En la nostra plataforma d'alta demanda per a la venda d'entrades, hi ha la forta possibilitat que múltiples usuaris vulguin seleccionar els mateixos seients quasi instantàniament. Sense una gestió correcta de la concurrència al servidor, això generaria duplicitats en les reserves de seients (dos usuaris creient tenir el mateix seient a la vegada). Es fa necessari un sistema per bloquejar un seient temporalment exclusiu per a un client durant un temps límit que li permetrà completar el pagament.

## Objectius
1. **Evitar duplicitats de reserves:** Garantir el bloqueig unívoc d'un seient utilitzi la sessió lligada per Socket.IO i l'UUID de l'usuari/compra.
2. **Propagació reactiva i temps real:** L'estat (Disponible, Reservat temporal, Venut) cal propagar-lo instantàniament a tots els clients actius via Websockets perquè els mapes de l'interfície web s'actualitzin en remot sense interferència manual (no realitzar request pulling al front, excepte a nivell de la càrrega inicial).
3. **Mecanisme de caducitat i disponibilitat restaurada:** El sistema del backend utilitzarà un cronòmetre que desfarà les reserves provisionals que no deriven en venda després d'un lapse fix (p. ex., 3 a 5 minuts). 

## Restriccions Tècniques Limitades al Front / Back 
- **Font de la Veritat (Backend):** La lògica del client no avalua l'ocupació en última instància, l'usuari clica, s'hi demana l'intent de bloqueig a Node.js, i ell aprova o desapròva tornant un booleà afirmatiu/negatiu per el trigger de visualització al client i el broadcast de seient per a tothom. Tot això s'emet a través de la xarxa, el front té restricció i dependència constant del Server.
- **Rendiment en el Socket.IO:** Farem servir una càrrega de mapa en la sol·licitud inicial `GET \` d'estat de Vue + Pinia i no hi enviarem tot el model massivament amb els Websockets, sinó que l'esdeveniment emetrà transaccions delta (`id_seient`, `ID d'Usuari que reserva`, `estat`).
- **Problemes de connvi:** En cas de tall i refresc (F5) el front podrà carregar un nou estat consolidat. A la recuperació recuperarem l'intent de pagament pel client reconeixent cookies de l'user ID i tornant el seu seient als estat `seleccionat per mi` mentre el temporitzador no acabi en comptes de trepitjar.
