<?php
function generarBaraja() 
{
    $valores = ['1', '2', '3', '4', '5', '6', '7', 'J', 'Q' ,'K']; //A = 1
    $palos = ['C', 'D', 'P', 'T'];
    $baraja = [];

    foreach ($valores as $v)
    {
        foreach ($palos as $p) 
        {
            $baraja[] = $v . $p;
        }
    }
    shuffle($baraja);// shuffle aleatoriza el orden de los elementos en la matriz
    return($baraja);
}

function valorCarta($carta) 
{
    $valor = substr($carta, 0, -1); //quitamos el palo

    if(in_array($valor, ['J', 'Q', 'K']))
    {
        return 0.5;
    } 
    else
    {
        return floatval($valor); //floatval() convierte un valor a número decimal.
    }
}

function repartirCartas($baraja, $numJugadores, $numCartas) 
{
    $reparto = [];
    $indice = 0;

    for ($i=0; $i < $numJugadores; $i++)
    { 
        for ($j=0; $j < $numCartas; $j++) 
        { 
            $reparto[$i][] = $baraja[$indice];//añado carta al jugador $i
            $indice++;
        }
    }
    return $reparto;
}
//suma los valores numéricos de sus cartas usando la función valorCarta()
function calcularPuntuacion($cartas) 
{
    $suma = 0;
    foreach ($cartas as $c)
    {
        $suma += valorCarta($c);
    }
    return $suma;
}

function mostrarTabla($jugadores, $cartasJugadores, $puntuaciones, $numCartas) 
{
    echo "<table border='1'>";

    for ($i = 0; $i < count($jugadores); $i++) 
    {
        echo "<tr>";
        echo "<td>{$jugadores[$i]}</td>";

        foreach ($cartasJugadores[$i] as $carta) 
        {
            echo "<td><img src='images/{$carta}.PNG' width='70'></td>";
        }

        echo "</tr>";
    }

    echo "</table>";
}

?>