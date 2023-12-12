<?php

/*
Nombre del Plugin: Lectura de numeros
Plugin URI: http://wordpress.org/plugins/lectura-numeros/
Descripcion: Añade a los números su lectura en letras usando acceso a base de datos.
Autor: Rubén Núñez
Version: 3.3
Author URI: https://ma.tt/
*/

//Crea un array con los 10 primeros numeros
$numeros = array(
    '1',
    '2',
    '3',
    '4',
    '5',
    '6',
    '7',
    '8',
    '9',
    '0');

//Crea un array con los 10 primeros numeros escritos
$numLetra = array(
    'Uno',
    'Dos',
    'Tres',
    'Cuatro',
    'Cinco',
    'Seis',
    'Siete',
    'Ocho',
    'Nueve',
    'Cero');

//Recogemos los datos de la tabla, y los ordenamos para su uso en la pagina, implementamos tambien la funcion de reemplazo de texto
function reemplazarTexto($text){

    $words = recogerTabla(); //Obtenemos los datos de la tabla a partir de la funcion 'leerTabla' y los guardamos en 'words'
    foreach ($words as $result){ //Recorremos los datos de la tabla
        $cars[] = $result->numeros;
        $places[] = $result->numeros . " " . "(".$result->numLetra.")";
    }
    return str_replace($cars, $places, $text); //Reemplazamos los numeros por su lectura en letras
}

//Añadimos el filtro a los titulos y al contenido, para que se ejecute la funcion
add_filter('the_content', 'reemplazarTexto');
add_filter('the_title', 'reemplazarTexto');

//Creamos la tabla en la base de datos
function crearTabla(){

    global $wpdb;    //Pedimos el acceso a la base de datos
    $table_name = $wpdb->prefix . 'cambioNumero';    //Creamos el nombre de la tabla usando el prefijo de la DB

    $charset_collate = $wpdb->get_charset_collate();    //Obtenemos el charset de la DB, que usaremos para crear la tabla

    //Ejecutamos el codigo SQL necesario para crear la tabla
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        numeros varchar(255) NOT NULL,
        numLetra varchar(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );    //Pedimos acceso a las funciones de la DB de Wordpress
    dbDelta( $sql );    //Ejecutamos el codigo SQL usando uno de los metodos de la DB de Wordpress
}

add_action( 'plugins_loaded', 'crearTabla');    //Añadimos la accion para que se ejecute la funcion cuando se carguen los plugins

//Insertamos los datos del plugin en la tabla
function insertarEnTabla(){
    global $wpdb, $numLetra, $numeros; //Pedimos el acceso a la DB y a los arrays de numeros y su lectura
    $table_name = $wpdb->prefix . 'cambioNumero';
    $hasSomething = $wpdb->get_results( "SELECT * FROM $table_name" ); //Obtenemos los datos de la tablay los guardamos en 'hasSomething'
    if ( count($hasSomething) == 0 ) { //Comprobamos si la tabla esta vacia
        for ($i = 0; $i < count($numLetra); $i++) { //Si esta vacia, insertamos los datos de los arrays en la tabla
            $wpdb->insert(
                $table_name,
                array(
                    'numLetra' => $numLetra[$i],
                    'numeros' => $numeros[$i]
                )
            );
        }
    }
}

//Añadimos la accion para que se ejecute la funcion cuando se carguen los plugins
add_action( 'plugins_loaded', 'insertarEnTabla');

//Leemos todos los datos de la tabla, y los devolvemos
function recogerTabla(){
    global $wpdb; //Pedimos el acceso a la DB
    $table_name = $wpdb->prefix . 'cambioNumero';
    $results = $wpdb->get_results( "SELECT * FROM $table_name" ); //Obtenemos los datos de la tabla y los guardamos en 'results'
    return $results; //Devolvemos los datos
}

