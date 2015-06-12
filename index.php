<?php
header('Content-Type: application/csv');
header('Content-Disposition: attachement; filename=file-img.csv');

include("config.php");
include("db.php");
include("util.php");


if( DEBUGMODE ){
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
}

$directory = "../../../../../../../../.." . PATHASSOLUTO;
$contatore = 0;

$connection = Database::getConnection(); 
$machato = FALSE;
$sapNonTrovato = "123";


$aprifile = file( FILEDAELABORARE ); // APRO IL FILE
$numerorighe = count($aprifile); // CONTO QUANTE RIGHE CI SONO
//$fp = fopen('csv/file_img.csv', 'w+');

$array_to_csv = Array( Array("sap","@path","@path2") );


function adFile2Db($filePath) {
  global $contatore;
  global $connection; 

  $contatore++;
  $pathDefinitivo = str_replace('../../../../../../../../..', '', $filePath);

  $sqlPathImg = "INSERT INTO ImgDbHD ( pathimg) VALUES (\"". $pathDefinitivo ."\");";

  if ($result = $connection->query( $sqlPathImg )) { 
    //echo "Inserisco:[". $contatore . "] ". $pathDefinitivo . '<br />';
  } else{
    echo "<br />Errore insterimento:" . $pathDefinitivo . "<br /> Errore:" . $connection->error . "<br />";
  } 
}


function searchFile($folder) {
  
  $folder = rtrim($folder, "/") . '/';
  
  if ($hd = opendir($folder)) {
    
    while (false !== ($file = readdir($hd))) { 
      if($file != '.' && $file != '..') {
        if(is_dir($folder . $file)) {
          
          searchFile($folder. $file);
        
        } else {
         
          adFile2Db( $folder .$file );
        
        }
      }
    }
    closedir($hd); 
  }

}


function convert_to_csv($input_array, $output_file_name, $delimiter){
    /** open raw memory as file, no need for temp files */
    $temp_memory = fopen('csv/report.csv', 'w+');
    /** loop through array  */
    foreach ($input_array as $line) {
        /** default php csv handler **/
        fputcsv($temp_memory, $line, $delimiter);
    }
    /** rewrind the "file" with the csv lines **/
    fseek($temp_memory,0);
    
    /** Send file to browser for download */
    fpassthru($temp_memory);
    
}


function ritornaPath($stringa){
  $pathImg = "";
  $result;

  global $connection; 

  $querySql = "SELECT pathimg FROM ImgDbHD WHERE pathimg LIKE \"%". puliscistringa($stringa) ."%\" LIMIT 0,5";

  if ($result_obj = $connection->query( $querySql )) { 
    //error_log("Cerco valori:". $stringa );

    //echo "<p>*** Cerco valori:". $stringa ." ***</p>";

    while($result = $result_obj->fetch_array(MYSQLI_ASSOC)) {
      // collect the array
      //echo $result;
      //echo $result[pathimg];
      return mageggia($result["pathimg"]);
      //print_r( $result[0] );
      //echo "</pre>";

    }

  }else{
    echo "<br /> Errore:" . $connection->error . "<br />";
  }

  //TODO devo capire come funzione il return
    return "";
}


if ($result = $connection->query("SELECT DATABASE();")) { 
  error_log("*** Eccedo al DB ***");
} 


if( AGGIORNADB ){

  //Azzero gl'indici della tabella
  if ($result = $connection->query("ALTER TABLE ImgDbHD AUTO_INCREMENT = 1;")) { 
    error_log("*** Azzero INDICI ***");
  } 

  //Cancello tutto il contenuto
  if ($result = $connection->query("TRUNCATE ImgDbHD;")) { 
    error_log("*** Cancello vecchi valori ***");
  } 

  //Faccio la ricerca e l'inserimento dei valori nel db
  searchFile($directory);
  error_log("*** Cancello vecchi valori ***");

}else{

    error_log("*** Cerco i valori ***");

    for($i=1; $i<$numerorighe; $i++) { // CICLO FOR CHE ESAMI RIGA PER RIGA IL FILE
      // inizia da uno perche deve saltare l'intestazione del csv

      $dati = explode(";",$aprifile[$i]); // SUDDIVIDO I VARI CAMPI

      $pathRitornato = ritornaPath( $dati[0] );

      array_push($array_to_csv, Array( puliscistringa($dati[0]) , $pathRitornato) );

    }
}


convert_to_csv($array_to_csv, 'report.csv', ',');

$connection->close();

error_log("*** Sconnesso da db ***");

?>