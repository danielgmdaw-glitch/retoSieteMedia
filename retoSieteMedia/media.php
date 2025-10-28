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
        $nombre = limpiar_campos($_POST["nombres$i"]);
        if ($nombre != "") 
        {
            $jugadores[] = $nombre;
        }
    }

    $numJugadores = count($jugadores);
    $numCartas = intval(limpiar_campos($_POST["numcartas"]));//con intval convertimos las strings a un valor númerico(int), y nos aseguramos que sea un entero porque no se pueden repartir 2,3 cartas
    $apuesta = floatval(limpiar_campos($_POST["apuesta"]));//con floatval nos aseguramos que la apuesta puedas ser un numero decimal

    if ($numJugadores == 0) 
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
    
        //Busco la puntuacion mas alta sin pasarse de 7.5, me determina quien puede ganar
        $maxPuntuaciones = 0;
        for ($i=0; $i < $numJugadores; $i++) 
        { 
            if($puntuaciones[$i] <= 7.5 && $puntuaciones[$i] > $maxPuntuaciones)
            {
                $maxPuntuaciones = $puntuaciones[$i];
            }
        }
    
    
    
    }
    
}//fin
?>