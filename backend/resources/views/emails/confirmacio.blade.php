<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Playfair Display', serif; background-color: #1a0a0a; color: #f5e6d3; padding: 40px; }
        .tiquet { 
            background: linear-gradient(135deg, #2a0505, #4a0c0c); 
            border: 2px solid #d4af37; 
            padding: 30px; 
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        h1 { color: #d4af37; text-align: center; border-bottom: 1px solid #d4af37; padding-bottom: 20px; }
        .detall { margin: 20px 0; font-size: 1.1rem; }
        .codi { 
            display: block; width: fit-content; margin: 25px auto; 
            background: #d4af37; color: #2a0505; 
            padding: 10px 25px; font-weight: bold; font-size: 1.4rem; letter-spacing: 2px;
            border-radius: 4px; border: 1px solid #856404;
        }
        .footer { text-align: center; font-size: 0.8rem; opacity: 0.6; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="tiquet">
        <h1>TEATRE TR - ENTRADA DIGITAL</h1>
        <p>Benvolgut/da <strong>{{ $vendes->first()->nom_comprador }}</strong>,</p>
        <p>Gràcies per la vostra compra. Aquí teniu els detalls de les vostres localitats per a l'esdeveniment <strong>{{ $vendes->first()->esdeveniment->titol }}</strong>:</p>
        
        <div class="detall">
            <ul>
                @foreach($vendes as $v)
                    <li>Seient: <strong>{{ $v->seient->id_referencia }}</strong> (Fila i Seient assignats)</li>
                @endforeach
            </ul>
        </div>

        <p>El vostre codi únic de confirmació és:</p>
        <div class="codi">{{ $codi }}</div>

        <p>Presenteu aquest codi o aquest correu a l'entrada del recinte.</p>
        
        <div class="footer">
            Aquest és un correu automàtic generat per la plataforma de Venda d'Entrades TR 2DAW.
        </div>
    </div>
</body>
</html>
