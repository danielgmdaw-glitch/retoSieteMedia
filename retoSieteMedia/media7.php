<?php
include("media7fun.php");
function limpiar_campos($data) 
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $jugadores = [];
    for ($i=1; $i <= 4; $i++)
    { 
        $nombre = limpiar_campos($_POST["nombre$i"]);
        if ($nombre != "") 
        {
            $jugadores[] = $nombre;
        }
    }

    $numJugadores = count($jugadores);
    $numCartas = intval(limpiar_campos($_POST["numcartas"]));//con intval convertimos las strings a un valor númerico(int), y nos aseguramos que sea un entero porque no se pueden repartir 2.3 cartas
    $apuesta = floatval(limpiar_campos($_POST["apuesta"]));//con floatval nos aseguramos que la apuesta puedas ser un numero decimal

    if ($numJugadores < 4)
    {
        echo "<h3 style='color:red;'>Numero de jugadores no válido.</h3>";
    } elseif ($numCartas <= 0) {
        echo "<h3 style='color:red;'>Numero de cartas no válido</h3>";
    } elseif ($apuesta <= 0) {
        echo "<h3 style='color:red;'>La apuesta debe ser mayor a 0</h3>";
    } else {

        //Creamos la baraja y repartimos las cartas
        $baraja = generarBaraja();
        $cartasJugadores = repartirCartas($baraja, $numJugadores, $numCartas);
    
        //Calculamos las puntuaciones de cada jugador y lo añadimos a un array
        $puntuaciones = [];
        for ($i = 0; $i < $numJugadores; $i++) 
        {
            $puntuaciones[] = calcularPuntuacion($cartasJugadores[$i]);
        }
    
        //Busco la puntuacion mas alta sin pasarme de 7.5, me determina quien puede ganar
        $maxPuntuaciones = 0;
        for ($i=0; $i < $numJugadores; $i++) 
        { 
            if($puntuaciones[$i] <= 7.5 && $puntuaciones[$i] > $maxPuntuaciones)
            {
                $maxPuntuaciones = $puntuaciones[$i];
            }
        }
        
        //Recorro los jugadores y añadimos al array $ganadores los que tengan la maxima puntuacion(puede haber empates)
        $ganadores = [];
        for ($i=0; $i < $numJugadores; $i++) 
        { 
            if ($puntuaciones[$i] == $maxPuntuaciones && $maxPuntuaciones > 0) 
            {
                $ganadores[] = $i;
            }
        }
    
        //Calculamos los Premios
        if($maxPuntuaciones == 7.5)
        {
            $porcentaje = 0.8;
        } else {
            $porcentaje = 0.5;
        }

        $premioTotal = $apuesta * $porcentaje;

        if (count($ganadores) > 0) 
        {
            $premioPorJugador = $premioTotal / count($ganadores);
        } else {
            $premioPorJugador = 0;
        }


        echo "<h1>Resultados del Juego 7.5</h1>";
        if (count($ganadores) > 0) 
        {
            for ($i = 0; $i < count($ganadores); $i++) 
            {
                $indice = $ganadores[$i];
                echo "{$jugadores[$indice]} ha ganado la partida con una puntuación de {$puntuaciones[$indice]}<br>";
            }
            echo "Los ganadores han obtenido " . $premioPorJugador . "€ de premio<br>";
        } else {
            echo "No hay ganadores. El bote acumulado es de {$apuesta}€<br>";
        }

        mostrarTabla($jugadores, $cartasJugadores, $puntuaciones, $numCartas);
        echo "TOTAL PREMIOS#" . count($ganadores) . "#{$premioTotal}";

        //Ficheros
        $fecha = date("dmYHis");//El formato "dmYHis" produce: día(2) mes(2) AÑO(4) Hora(2) minutos(2) segundos(2).
        $nombreFichero = "apuestas_{$fecha}.txt";
        $f = fopen($nombreFichero, "w");

        if ($f) 
        {
            for ($i = 0; $i < count($jugadores); $i++) 
            {
                //explode(" ", ...) divide la cadena por espacios en un array de palabras
                $palabras = explode(" ", strtoupper($jugadores[$i]));
                $iniciales = "";

                foreach ($palabras as $p) 
                {
                    $iniciales .= substr($p, 0, 1);//substr($p, 0, 1) obtiene la primera letra de la palabra.
                }

                // Importe ganado
                if (in_array($i, $ganadores)) //in_array($i, $ganadores) devuelve true si el jugador es ganador.
                {
                    $importeGanado = $premioPorJugador;
                } else {
                    $importeGanado = 0;
                }

                $linea = "{$iniciales}#{$puntuaciones[$i]}#{$importeGanado}\n";
                fwrite($f, $linea);
            }

            $lineaFinal = "TOTAL PREMIOS#" . count($ganadores) . "#{$premioTotal}\n";
            fwrite($f, $lineaFinal);
            fclose($f);

            echo "<strong>Fichero generado correctamente:</strong> {$nombreFichero}";
        } else {
            echo "<strong>Error al crear el fichero.</strong>";
        }

    }//else

    
    
}//fin
?>