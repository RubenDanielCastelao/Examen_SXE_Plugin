# WordPress Plugin: Lectura de Números


## Descripción

Este plugin para WordPress, añade la capacidad de mostrar la lectura en letras de los 
números en el contenido y títulos de los post de la página de WordPress.

La lectura en letras se obtiene a partir de datos almacenados en una tabla de la base de datos.

Un ejemplo del funcionamiento:

- 1 (Uno)

El contenido a la derecha del '1' sería añadido automáticamente por el plugin.

## Explicación del Código

### Arrays de Números

El plugin define dos arrays:

```php
$numeros = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
$numLetra = array('Uno', 'Dos', 'Tres', 'Cuatro', 'Cinco', 'Seis', 'Siete', 'Ocho', 'Nueve', 'Cero');
```

Estos arrays contienen los números del 0 al 9 y sus correspondientes representaciones en letras.

### Creación de la Tabla en la Base de Datos

El plugin crea una tabla en la base de datos de WordPress para **almacenar la relación 
entre los números y su lectura** en letras. La tabla se crea al cargar los plugins.

```php
function crearTabla(){

    global $wpdb;  
    $table_name = $wpdb->prefix . 'cambioNumero';  

    $charset_collate = $wpdb->get_charset_collate();  

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        numeros varchar(255) NOT NULL,
        numLetra varchar(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );   
    dbDelta( $sql );    
}

add_action('plugins_loaded', 'crearTabla');
```

### Inserción de Datos en la Tabla

La función `insertarEnTabla` comprueba si la tabla está vacía y, en caso afirmativo, **inserta los datos de los arrays en la tabla**.

```php
function insertarEnTabla(){
    global $wpdb, $numLetra, $numeros; 
    $table_name = $wpdb->prefix . 'cambioNumero';
    $hasSomething = $wpdb->get_results( "SELECT * FROM $table_name" ); 
    if ( count($hasSomething) == 0 ) { 
        for ($i = 0; $i < count($numLetra); $i++) { 
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

add_action('plugins_loaded', 'insertarEnTabla');
```

### Lectura de la Tabla

La función `leerTabla` **obtiene todos los datos almacenados en la tabla**.

```php
function recogerTabla(){
    global $wpdb; 
    $table_name = $wpdb->prefix . 'cambioNumero';
    $results = $wpdb->get_results( "SELECT * FROM $table_name" );
    return $results; //Devolvemos los datos
}
```

### Gestión de los datos de la tabla y Reemplazo en el Contenido

La función `recolectarInfoTabla` lee los datos de la tabla y **reemplaza los números en el contenido y títulos** con su lectura en letras.

```php
function reemplazarTexto($text){

    $words = recogerTabla(); 
    foreach ($words as $result){ 
        $cars[] = $result->numeros;
        $places[] = $result->numeros . " " . "(".$result->numLetra.")";
    }
    return str_replace($cars, $places, $text); 
}
add_filter('the_content', 'reemplazarTexto');
add_filter('the_title', 'reemplazarTexto');
```

## Autor

- _Rubén Núñez_
- _Version: 3.3_
