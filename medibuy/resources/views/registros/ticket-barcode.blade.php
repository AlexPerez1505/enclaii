<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: 5cm 2.5cm;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            font-size: 8.5px;
            font-family: Arial, sans-serif;
            background: #fff;
        }
        .ticket {
            width: 189px;    /* 5cm */
            height: 94px;    /* 2.5cm */
            box-sizing: border-box;
            padding: 0;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
        }
      .logo {
            width: 75px;
            height: 30px;
            margin-bottom: 1px;
            margin-top: 1px;
            object-fit: contain;
            display: block;
            margin-left: 65px;
            margin-right: auto;
        }
        .datos {
            text-align: center;
            line-height: 1.1;
            width: 100%;
            margin-bottom: 2px;
        }
        .info-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            width: 100%;
        }
        .tipo {
            font-weight: bold;
            font-size: 10px;
            white-space: nowrap;
        }
        .marca-modelo {
            font-size: 9px;
            white-space: nowrap;
        }
        .barcode {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            margin-left:30px;
        }
        .barcode svg, .barcode img {
            display: block;
            margin: 0 auto !important;
        }
        .serie {
            font-size: 13px;
            display: block;
            text-align: center;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <!-- Logo arriba -->
        
       
        
        <div class="datos">
            <div class="info-row">
                <span class="tipo" style="font-family:'Arial Black', Arial, sans-serif; text-transform: uppercase;">
                    {{ $registro->subtipo_equipo }}
                </span>
                <span class="marca-modelo" style="font-family:'Arial Black', Arial, sans-serif; text-transform: uppercase;">
                    | {{ $registro->marca }} {{ $registro->modelo }}
                </span>
            </div>
        </div>
        <div class="barcode">
            {!! DNS1D::getBarcodeHTML($barcodeData, 'C128', 1.1, 32) !!}
        </div>
        <span class="serie">Serie: <b>{{ $registro->numero_serie }}</b></span>
    </div>
</body>
</html>
