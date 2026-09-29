# Documentació Tècnica: Diagrames

Aquest document conté la representació visual del sistema segons els requeriments de l'enunciat del TR (Punt 6.3).

## 1. Diagrama d'Entitat-Relació (E/R)
Representa l'estructura de dades persistent a la base de dades SQL (Laravel).

```mermaid
erDiagram
    ESDEVENIMENT ||--o{ SEIENT : conté
    SEIENT ||--o| VENDA : genera
    
    ESDEVENIMENT {
        int id PK
        string titol
        string recinte
        datetime data_hora
        decimal preu_base
        string imatge_url
    }
    
    SEIENT {
        int id PK
        int esdeveniment_id FK
        string codi
        string estat "disponible, reservat, venut"
        string reserva_id
        datetime expira_en
    }
    
    VENDA {
        int id PK
        int seient_id FK
        string nom_comprador
        string email_comprador
        datetime data_compra
    }
```

## 2. Diagrama de Seqüència (Reserva i Compra via Socket.IO)
Representa el flux en temps real i la gestió de concurrència (Punt 4).

```mermaid
sequenceDiagram
    participant U1 as Usuari A
    participant S as Servidor Sockets (Node.js)
    participant L as API Backend (Laravel)
    participant U2 as Usuari B

    Note over U1, U2: Ambdós usuaris veuen el mapa sincronitzat
    
    U1->>S: Seleccionar Seient [12A]
    S->>S: Validar Disponibilitat (Memòria)
    S-->>U1: Confirmació Reserva (OK)
    S-->>U2: Broadcast: Seient [12A] -> RESERVAT
    
    Note right of S: Inicia temporitzador 3 minuts
    
    U2->>S: Seleccionar Seient [12A]
    S-->>U2: Error: Seient JA RESERVAT
    
    U1->>L: Finalitzar Compra (Dades Personals)
    L->>L: Validar ID Reserva
    L->>L: Persistir VENDA a DB
    L-->>U1: Resposta: Compra OK
    L->>S: Signal: Seient [12A] -> VENUT
    S-->>U1: Notificació d'èxit
    S-->>U2: Broadcast: Seient [12A] -> VENUT (Definitiu)
```

## 3. Diagrama de Casos d'Ús
Funcionalitats principals segons els rols (Usuari i Administrador).

```mermaid
useCaseDiagram
    actor "Usuari" as U
    actor "Administrador" as A

    package "Sistema de Venda" {
        usecase "Consultar Cartellera" as UC1
        usecase "Reservar Seient (Real-time)" as UC2
        usecase "Realitzar Pagament / Checkout" as UC3
        usecase "Consultar Entrades" as UC4
        
        usecase "Crear/Editar Esdeveniments" as UC5
        usecase "Monitoritzar Sala en Viu" as UC6
        usecase "Consultar Informes Vendes" as UC7
    }

    U --> UC1
    U --> UC2
    U --> UC3
    U --> UC4

    A --> UC5
    A --> UC6
    A --> UC7
```
