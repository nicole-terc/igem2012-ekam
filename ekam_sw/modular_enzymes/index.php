<!DOCTYPE html>
<html>
    
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="http://fonts.googleapis.com/css?family=Skranji&subset=latin,latin-ext" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <title>Modular Enzimes</title>
        
        <script type="text/javascript" language="javascript">  
            
            var seq_num = 1;
            
            function addSequence() {
                if(seq_num < 15){
                    var divTag = document.createElement("div");
                    divTag.id = "sequence_"+(++seq_num); 
                    divTag.className = "sequence_form";
                    divTag.innerHTML =  '<hr/><h3>Sequence '+ seq_num +'</h3>' +
                                        '<p>Promoter</p>' +
                                        '<textarea id="promoter_'+ seq_num +'" name="promoter_'+ seq_num +'" ></textarea>' +
                                        '<p>RBS</p>' +
                                        '<textarea id="rbs_'+ seq_num +'" name="rbs_'+ seq_num +'" ></textarea>' +
                                        '<p>Gene</p>' +
                                        '<textarea id="gene_'+ seq_num +'" name="gene_'+ seq_num +'" ></textarea>' +
                                        '<p>Terminator</p>' +
                                        '<textarea id="terminator_'+ seq_num +'" name="terminator_'+ seq_num +'" ></textarea>';

                    var allSequences = document.getElementById('all_sequences');
                    allSequences.appendChild(divTag);
                    
                    document.getElementById("seq_num").value = seq_num;
                    window.scroll(0,document.height);
                    
                }
            }
            
            function removeSequence(seq_n){
                if(1 < seq_num){
                    var allSequences = document.getElementById('all_sequences');
                    var divTagID = "sequence_" + (seq_num--);
                    var divTag = document.getElementById(divTagID);
                    allSequences.removeChild(divTag);
                    document.getElementById("seq_num").value = seq_num;
                }
            }
        </script> 
        
        <script type="text/javascript" src="highslide/highslide-full.js"></script>
        <link rel="stylesheet" type="text/css" href="highslide/highslide.css" />
        <script type="text/javascript">
            // override Highslide settings here
            // instead of editing the highslide.js file
            hs.graphicsDir = 'highslide/graphics/';
        </script>
        
    </head>
    
    <body>
        
        <?php
        
            $sticky_vector='';
            $sticky_enzymes_list_yes='';
            $sticky_enzymes_list_no='';
            $vector='';
            $secuencias='';
            $listaEnzimas='';
            
            /* ==================================== FUNCIONES ==================================== */
            
            /* CHECK FOR VALID CHARACTERS */
            function check($string,$valid_chars){
                $n = strlen($string);
                $m = count($valid_chars);

                for($i = 0; $i < $n; $i++){
                    $current_char = $string{$i};
                    $aux = false;

                    for($j = 0; $j < $m; $j++)
                        if($current_char === $valid_chars[$j]){
                            $aux = true;
                            break;
                        }

                    if(!$aux)
                        return false;

                }
                return true;
            }
            /* CHECK FOR VALID CHARACTERS */
        
            /* CHECK FOR VALID INPUT */
            function validate($post){ //recibe todo en mayus
                $v_vector = $post['vector'];
                if( empty($v_vector) || !check($v_vector,array('A','T','G','C')) )
                    return 'vector_error';
                
                $v_seq_num = $post['seq_num'];
                for( $i = 1; $i <= $v_seq_num; $i++){
                    if( ( !empty($post['promoter_'.$i]) || !empty($post['rbs_'.$i]) ||
                        !empty($post['gene_'.$i]) || !empty($post['terminator_'.$i]) ) &&
                        ( check($post['promoter_'.$i],array('A','T','G','C')) &&
                        check($post['rbs_'.$i],array('A','T','G','C')) &&
                        check($post['gene_'.$i],array('A','T','G','C')) &&
                        check($post['terminator_'.$i],array('A','T','G','C'))) ){
                        
                        /*echo 'sequence_'.$i.'_OK<br/>';*/
                        
                    }
                    else{
                        return 'sequence_' . $i . '_error';
                    }
                }
                return '';
            }
            /* CHECK FOR VALID INPUT */

            /* ------------------- App Heart! ------------------- */ 
            
            /*////////////////////Compatibles////////////////////////////////*/
            
            function Compatibles($EnzimaA, $EnzimaB){
                if($EnzimaA['Type']==$EnzimaB['Type'] && $EnzimaA['Type']=='blunt'){
                    return false;
                }
                /*Se agregan las multiples secuencias antes de mandarla comprobar*/
                if(!$EnzimaB['Multiples'])
                    $EnzimaB = agregarMultiples($EnzimaB);
                if($EnzimaB['Multiples'] === 'no')
                    return false;

                /* aqui se checan*/
                if(Comprobar($EnzimaB) && $EnzimaA['Temperatura'] == $EnzimaB['Temperatura']){
                    /*Agregado para las multiples secuencias, las secuencias se agregan arriba*/

                    for($i = 0; $i < count($EnzimaA['Multiples']); $i++){
                        for($j = 0; $j <count($EnzimaB['Multiples']); $j++){
                            if($EnzimaA['Multiples'][$i] === $EnzimaB['Multiples'][$j])
                                return false;
                        }
                    }
                    /*=====*/

                    $buffer = 0;
                    $hayBuffer = false;
                    for($i = 0; $i < 4; $i++){
                        if($EnzimaA['Buffers'][$i] >= 50 && $EnzimaB['Buffers'][$i] >= 50){
                          if((abs($EnzimaA['Buffers'][$i] - $EnzimaB['Buffers'][$i]) 
                                  <= abs($EnzimaA['Buffers'][$buffer]-$EnzimaB['Buffers'][$buffer])) 
                                  && ($EnzimaA['Buffers'][$i] + $EnzimaB['Buffers'][$i])/2 
                                  >= ($EnzimaA['Buffers'][$buffer] + $EnzimaB['Buffers'][$buffer])/2){
                              $buffer = $i;
                              $hayBuffer = true;
                          }  
                        }
                    }
                    if($hayBuffer){
                        return true;
                    }
                    return false;
                }
            }

            /*/////////////////////Comprobar/////////////////////////////*/
            function Comprobar($Enzima){
                global $vector, $secuencias;
                //$pos = strpos($vector, $Enzima['Secuencia']);
                //echo 'ComprobarVector ', $vector, " --- ",$Enzima['Secuencia']," --- ", $pos , '<br/>';
                /**/
                if(!$Enzima['Multiples'])
                    $Enzima = agregarMultiples($Enzima);
                if($Enzima['Multiples'] === 'no')
                    return false;

                if(CortaEnVector($vector, $Enzima)){
                    return false;
                }
                $secuencias_length = count($secuencias);

                for($i = 0; $i < $secuencias_length; $i++){
                    $prom_rbs_gen = $secuencias[$i][0] . $secuencias[$i][1]; //tomar promotor y rbs del primer gen;
                    $length_prom_rbs = strlen($prom_rbs_gen) - strlen($Enzima['Secuencia']) + 1; //espacio incortable
                    $prom_rbs_gen .= $secuencias[$i][2]; // Agregar gen

                    for($k = 0; $k < count($Enzima['Multiples']); $k++){
                        $indexOf = strpos($prom_rbs_gen, $Enzima['Multiples'][$k]);

                        if(($indexOf !== false && $indexOf < $length_prom_rbs) /*Revisar si esta en el espacio incortable*/
                                || strpos($secuencias[$i][3],$Enzima['Multiples'][$k])!== false){ /*Revisar si esta en terminador*/
                            //echo '<br/>COMPROBAR FALSE!<br/>';
                            return false;
                        }
                        else if(substr_count($secuencias[$i][2], $Enzima['Multiples'][$k])>10){
                            //echo '<br/>COMPROBAR FALSE! Corta Mucho';
                            return false;
                        }
                    }
                 }

                 return true;
            }

            /*/////////////////////CortaEnVector/////////////////////////////*/
            function CortaEnVector($vector,$enzima){
                $vector = $vector . $vector . $vector;
                for($k = 0; $k < count($enzima['Multiples']); $k++){
                    if(strpos($vector, $enzima['Multiples'][$k]) === false){
                        return false;
                    }
                }
                return true;
            }

            /*/////////////////////agregarMultiples///////////////////////////*/
            /**********************************************************
             * Code	Meaning                         Etymology
             * A	A                           Adenosine	
             * T/U	T                           Thymidine/Uridine
             * G	G                           Guanine
             * C	C                           Cytidine
             * K	G or T                      Keto
             * M	A or C                      Amino
             * R	A or G                      Purine
             * Y	C or T                      Pyrimidine
             * S	C or G                      Strong	
             * W	A or T                      Weak
             * B	C or G or T                 not A (B comes after A)	
             * V	A or C or G                 not T/U (V comes after U)
             * H	A or C or T                 not G (H comes after G)	
             * D	A or G or T                 not C (D comes after C)
             * X/N	G or A or T or C            any	
             * .	not G or A or T or C		
             * -	gap of indeterminate length	
             ***********************************************************/

            function agregarMultiples($Enzima){
                global $listaEnzimas;
                //echo "<br/>AGREAGAR MULTIPLES! para". $Enzima['index'] ."<br/>";
                $multiples = array('');

                for($k = 0; $k < strlen($Enzima['Secuencia']); $k++){
                    //echo 'Char: ', $Enzima['Secuencia'][$k], '<br/>';
                    $length_actual = count($multiples);
                    switch ($Enzima['Secuencia']{$k}){
                        case 'K':
                            for($i = 0; $i < $length_actual; $i ++){
                                array_push($multiples, $multiples[$i] . 'G' ); 
                                $multiples[$i] .= 'T';
                            }
                            break;
                        case 'M':
                            for($i = 0; $i < $length_actual; $i ++){
                                array_push($multiples, $multiples[$i] . 'A' ); 
                                $multiples[$i] .= 'C';
                            }
                            break;
                        case 'R':
                            for($i = 0; $i < $length_actual; $i ++){
                                array_push($multiples, $multiples[$i] . 'A' ); 
                                $multiples[$i] .= 'G';
                            }
                            break;
                        case 'Y':
                            for($i = 0; $i < $length_actual; $i ++){
                                array_push($multiples, $multiples[$i] . 'C' ); 
                                $multiples[$i] .= 'T';
                            }
                            break;
                        case 'S':
                            for($i = 0; $i < $length_actual; $i ++){
                                array_push($multiples, $multiples[$i] . 'C' ); 
                                $multiples[$i] .= 'G';
                            }
                            break;
                        case 'W':
                            for($i = 0; $i < $length_actual; $i ++){
                                array_push($multiples, $multiples[$i] . 'A' ); 
                                $multiples[$i] .= 'T';
                            }
                            break;
                        case 'B':
                            for($i = 0; $i < $length_actual; $i ++){
                                array_push($multiples, $multiples[$i] . 'C' );
                                array_push($multiples, $multiples[$i] . 'G' ); 
                                $multiples[$i] .= 'T';
                            }
                            break;
                        case 'V':
                            for($i = 0; $i < $length_actual; $i ++){
                                array_push($multiples, $multiples[$i] . 'A' );
                                array_push($multiples, $multiples[$i] . 'C' ); 
                                $multiples[$i] .= 'G';
                            }
                            break;
                        case 'H':
                            for($i = 0; $i < $length_actual; $i ++){
                                array_push($multiples, $multiples[$i] . 'A' );
                                array_push($multiples, $multiples[$i] . 'C' ); 
                                $multiples[$i] .= 'T';                    
                            }
                            break;
                        case 'D':
                            for($i = 0; $i < $length_actual; $i ++){
                                array_push($multiples, $multiples[$i] . 'A' );
                                array_push($multiples, $multiples[$i] . 'G' ); 
                                $multiples[$i] .= 'T';                    
                            }
                            break;
                        case 'N':
                            for($i = 0; $i < $length_actual; $i ++){
                                array_push($multiples, $multiples[$i] . 'A' );
                                array_push($multiples, $multiples[$i] . 'T' );
                                array_push($multiples, $multiples[$i] . 'G' ); 
                                $multiples[$i] .= 'C';
                            }
                            break;
                        default:
                            for($i = 0; $i < $length_actual; $i ++){
                                $multiples[$i] .= $Enzima['Secuencia']{$k};
                            }
                    }
                    //echo "<br/> Multiples: $i de <br/>";
                    //var_dump($multiples);
                    if(count($multiples)>10){
                        $multiples = 'no';
                        break;
                    }
                } 

                $Enzima['Multiples'] = $multiples;
                $listaEnzimas[$Enzima['index']] = $Enzima;

                return $Enzima;
            }

            /*/////////ARMAR Construct///////////////*/

            function ArmarConstruct($secuencias, $stackParejas){
                $secuencias_length = count($secuencias);
                $construct_pedazos = array();
                $construct_junto = array($stackParejas[0]);
                $construct_secuencia = $stackParejas[0]['Secuencia'];

                for($i = 0; $i < $secuencias_length; $i++){
                    /*pedazo1-pedazo2-... Pedazo = {E1-(promotor,rbs,gen,terminador)-E2}*/
                    $aux = array($stackParejas[$i], $secuencias[$i], $stackParejas[$i+1]);
                    $construct_pedazos[$i] = $aux;

                    /*Enzima-secuencia-enzima-...*/
                    $construct_junto[2*$i+1] = $secuencias[$i];
                    $construct_junto[2*$i+2] = $stackParejas[$i+1];

                    /*Secuencia sin divisiones ATGCATGC...*/ 
                    for($j = 0; $j < 4; $j++){
                        $construct_secuencia .= $secuencias[$i][$j];
                    }
                    $construct_secuencia .= $stackParejas[$i+1]['Secuencia'];
                }
                $construct = array($construct_pedazos, $construct_junto, $construct_secuencia);
                return $construct;
            }
                
            /* ==================================== FIN FUNCIONES ================================= */
                
        
            if(isset($_POST['submit'])){                
                $fixed_post = $_POST;
                    
                $fixed_post['vector'] = preg_replace("/\s\s+/","",$fixed_post['vector']);
                $fixed_post['vector'] = str_ireplace(" ", "", $fixed_post['vector']);
                $fixed_post['vector'] = str_replace("\t","", $fixed_post['vector']);
                $fixed_post['vector'] = strtoupper($fixed_post['vector']);
                
                for( $i = 1; $i <=$fixed_post['seq_num'] ; $i++){
                    
                    $fixed_post['promoter_'.$i] = preg_replace("/\s\s+/","",$fixed_post['promoter_'.$i]);
                    $fixed_post['promoter_'.$i] = str_ireplace(" ", "", $fixed_post['promoter_'.$i]);
                    $fixed_post['promoter_'.$i] = str_replace("\t","", $fixed_post['promoter_'.$i]);
                    $fixed_post['promoter_'.$i] = strtoupper($fixed_post['promoter_'.$i]);
                    
                    $fixed_post['rbs_'.$i] = preg_replace("/\s\s+/","",$fixed_post['rbs_'.$i]);
                    $fixed_post['rbs_'.$i] = str_ireplace(" ", "", $fixed_post['rbs_'.$i]);
                    $fixed_post['rbs_'.$i] = str_replace("\t","", $fixed_post['rbs_'.$i]);
                    $fixed_post['rbs_'.$i] = strtoupper($fixed_post['rbs_'.$i]);
                    
                    $fixed_post['gene_'.$i] = preg_replace("/\s\s+/","",$fixed_post['gene_'.$i]);
                    $fixed_post['gene_'.$i] = str_ireplace(" ", "", $fixed_post['gene_'.$i]);
                    $fixed_post['gene_'.$i] = str_replace("\t","", $fixed_post['gene_'.$i]);
                    $fixed_post['gene_'.$i] = strtoupper($fixed_post['gene_'.$i]);
                    
                    $fixed_post['terminator_'.$i] = preg_replace("/\s\s+/","",$fixed_post['terminator_'.$i]);
                    $fixed_post['terminator_'.$i] = str_ireplace(" ", "", $fixed_post['terminator_'.$i]);
                    $fixed_post['terminator_'.$i] = str_replace("\t","", $fixed_post['terminator_'.$i]);
                    $fixed_post['terminator_'.$i] = strtoupper($fixed_post['terminator_'.$i]);
                    
                }
                $error_type = validate($fixed_post);
                
                $sticky_vector=$fixed_post['vector'];
                $sticky_enzymes_list_yes=$fixed_post['enzymes_to_use'];
                $sticky_enzymes_list_no=$fixed_post['enzymes_not_to_use'];
                
                if ( $error_type == '' ){ // no hay error
                    $vector = $fixed_post['vector'];
                    $number_of_seq = $fixed_post['seq_num'];
                    $secuencias=array();
                    for($i=1; $i<=$number_of_seq; $i++)
                        array_push($secuencias,array($fixed_post['promoter_'.$i],$fixed_post['rbs_'.$i],$fixed_post['gene_'.$i],$fixed_post['terminator_'.$i]));

                    require_once 'connectionDB.php';

                    $list_yes = preg_replace("/\s\s+/","",$fixed_post['enzymes_to_use']);   /* quita exceso de blank spaces y enters */
                    $list_yes = str_ireplace(" ", "", $list_yes);      /* quita espacio simples */
                    $list_yes = str_replace("\t","", $list_yes);       /* quita tabs */
                    $list_yes = explode(',',$list_yes);

                    $enzyme_names_yes = "";
                    $aux = count($list_yes);
                    for($i=0; $i < ($aux-1) ; $i++)
                        $enzyme_names_yes .= "nombre='$list_yes[$i]' OR ";
                    $aux--;
                    $enzyme_names_yes .= "nombre='$list_yes[$aux]'";

                    $query = "SELECT * FROM enzimas WHERE " . $enzyme_names_yes . " ORDER BY LENGTH (secuencia)";
                    $result = mysqli_query($dbc,$query) or die("Error querying Database.");
                    $ids_list_to_remove = array();
                    $lista = array();
                    $cont=0;
                    while($row = mysqli_fetch_array($result)){
                        /*echo $row['nombre'] . "<br/>";*/
                        array_push($ids_list_to_remove,$row['id']);
                        $aux = array(
                                    'index'=>$cont++,
                                    'usada'=>false,
                                    'Multiples'=> false,
                                    'Id'=>$row['id'],
                                    'Nombre'=>$row['nombre'],
                                    'Proveedor'=>$row['proveedor'],
                                    'Secuencia'=>$row['secuencia_sin_corte'],
                                    'Corte'=>$row['secuencia'],
                                    'Type'=>$row['tipo'],
                                    'Buffers'=>explode(",",$row['buffers']),
                                    'Temperatura'=>$row['temperatura_optima'],
                                    'Inactivacion'=>$row['temperatura_inactivacion'],
                                    'Metilacion'=>$row['bloqueo_metilacion'],
                                    'StarActivity'=>$row['star_activity'],
                                    'Fuente'=>$row['fuente'],
                                    'Concentracion'=>$row['uml'],
                                    'Precio'=>$row['precio'],
                                    );
                        array_push($lista,$aux);
                    }

                    $list_no = preg_replace("/\s\s+/","",$fixed_post['enzymes_not_to_use']);   /* quita exceso de blank spaces y enters */
                    $list_no = str_ireplace(" ", "", $list_no);      /* quita espacio simples */
                    $list_no = str_replace("\t","", $list_no);       /* quita tabs */
                    $list_no = explode(',',$list_no);

                    $enzyme_names_no = "";
                    $aux = count($list_no);
                    for($i=0; $i < ($aux-1) ; $i++)
                        $enzyme_names_no .= "nombre='$list_no[$i]' OR ";
                    $aux--;
                    $enzyme_names_no .= "nombre='$list_no[$aux]'";

                    $query = "SELECT * FROM enzimas WHERE " . $enzyme_names_no;
                    $result = mysqli_query($dbc,$query) or die("Error querying Database.");
                    while($row = mysqli_fetch_array($result)){
                        array_push($ids_list_to_remove,$row['id']);
                    }

                    /*"SELECT * FROM enzimas WHERE id!=$ids_list_to_remove[0] AND id!=$..  ORDER BY LENGTH(secuencia)"*/
                    $query = "SELECT * FROM enzimas WHERE ";
                    array_push($ids_list_to_remove,74); // enzima dummy TERKIANOS id=74
                    $aux = count($ids_list_to_remove);
                    for( $i = 0; $i < ($aux-1); $i++ )
                        $query .= "id!=" . $ids_list_to_remove[$i] . " AND ";
                    $aux--;
                    $query .= "id!=" . $ids_list_to_remove[$aux] . " ORDER BY LENGTH(secuencia)";
                    $result = mysqli_query($dbc,$query) or die("Error querying Database.");
                    while($row = mysqli_fetch_array($result)){
                        $aux = array(
                                    'index'=>$cont++,
                                    'usada'=>false,
                                    'Multiples'=> false,
                                    'Id'=>$row['id'],
                                    'Nombre'=>$row['nombre'],
                                    'Proveedor'=>$row['proveedor'],
                                    'Secuencia'=>$row['secuencia_sin_corte'],
                                    'Corte'=>$row['secuencia'],
                                    'Type'=>$row['tipo'],
                                    'Buffers'=>explode(",",$row['buffers']),
                                    'Temperatura'=>$row['temperatura_optima'],
                                    'Inactivacion'=>$row['temperatura_inactivacion'],
                                    'Metilacion'=>$row['bloqueo_metilacion'],
                                    'StarActivity'=>$row['star_activity'],
                                    'Fuente'=>$row['fuente'],
                                    'Concentracion'=>$row['uml'],
                                    'Precio'=>$row['precio'],
                                    );
                        array_push($lista,$aux);
                    }
                
                    /*  VARS READY
                        $secuencias
                        $vector
                        $lista
                        $number_of_seq
                    */
                    /*****************************************************************************************************/
                    /*****************************************************************************************************/
                    /*****************************************************************************************************/

                    $stackParejas = array();
                    $numero_enzimas_requeridas = count($secuencias)+1;
                    $continuarBuscando = true;
                    $hayConstruct = false;
                    $lista_length = count($lista);
                    $count = 0;
                    $listaEnzimas = $lista;


                    for($i = 0; $i<$lista_length; $i++){ /*Elegir la primera enzima del stack*/
                        if(Comprobar($listaEnzimas[$i])){
                            $listaEnzimas[$i]['usada'] = true;
                            array_push($stackParejas,$listaEnzimas[$i]);
                            break;
                        }
                    }

                    /*AGREGADO ITERATIVO*/
                    $Enzima = $stackParejas[0];
                    $indexB = 0;

                    /*////////////BuscarParejas////////////////////// */
                    while($continuarBuscando){

                        //echo '<br/>========================= <br/>';
                        //echo '#Veces Corrido: ', $count,"<br/> #Enzimas en Stack: ", count($stackParejas), "<br/> #Enzimas Requeridas: ", $numero_enzimas_requeridas, "<br/> ContinuarBuscando: ", $continuarBuscando;
                        //var_dump($stackParejas);

                        if(count($stackParejas) < $numero_enzimas_requeridas){
                            if($indexB < count($listaEnzimas)){ 
                                /*--------------------*/
                                if($listaEnzimas[$indexB]["usada"]){ /*Revizar si no está en el Stack*/
                                    $count ++;
                                    //echo $indexB,' usada! <br/>';
                                    /**/
                                    $indexB ++; 
                                    /*BuscaParejas($Enzima, $listaEnzimas, $indexB+1);*/
                                }
                                else if(Compatibles($Enzima, $listaEnzimas[$indexB])){ /*Comprobar Buffers y temperaturas*/
                                    if(count($stackParejas) == $numero_enzimas_requeridas-1){ /*Si es la ultima enzima, comprobar compatibilidad con la primer enzima*/
                                        if(Compatibles($stackParejas[0], $listaEnzimas[$indexB])){
                                            $listaEnzimas[$indexB]["usada"] = true;
                                            array_push($stackParejas, $listaEnzimas[$indexB]);
                                            $count++;
                                            //echo $indexB, ' esta sigue!<br/>';
                                            /**/
                                            $Enzima = $listaEnzimas[$indexB];
                                            $indexB = 0;
                                            /*BuscaParejas($listaEnzimas[$indexB], $listaEnzimas, 0);*/
                                        }
                                        else{
                                            $count++;
                                            //echo $indexB, ' noCompatibles!<br/>';
                                            /**/
                                            $indexB++;
                                            /*BuscaParejas($Enzima, $listaEnzimas, $indexB+1);*/
                                        }
                                    }
                                    else{
                                        $listaEnzimas[$indexB]["usada"] = true;
                                        array_push($stackParejas, $listaEnzimas[$indexB]);
                                        $count++;
                                        //echo $indexB, ' esta sigue!<br/>';
                                        /**/
                                        $Enzima = $listaEnzimas[$indexB];
                                        $indexB = 0;
                                        /*BuscaParejas($listaEnzimas[$indexB], $listaEnzimas, 0);*/
                                    }
                                }
                                else{ /*Si no son Compatibles*/
                                    $count++;
                                    //echo $indexB, ' noCompatibles!<br/>';
                                    /**/
                                    $indexB++;
                                    /*BuscaParejas($Enzima, $listaEnzimas, $indexB+1);*/
                                }
                                /*--------------------*/
                            }

                            else{ //Si ya se recorrió toda la lista y no se encontro ninguna compatible*/
                                array_pop($stackParejas);
                                $listaEnzimas[$Enzima['index']]['usada'] = false;
                                $nuevo_index = count($stackParejas)-1;

                                if($nuevo_index > -1){ /*Stack no vacio*/
                                    $nuevo_index = $stackParejas[$nuevo_index]['index'];
                                    /**/
                                    $indexB = $Enzima['index']+1;
                                    $Enzima = $listaEnzimas[$nuevo_index];
                                    /*BuscaParejas($listaEnzimas[$nuevo_index], $listaEnzimas, $Enzima['index']+1);*/
                                }
                                else{ /*Stack Vacio!*/
                                    $nuevo_index = 0;
                                    $lista_length = count($listaEnzimas);
                                    for($k = $Enzima['index']+1; $k < $lista_length; $k++){
                                        if(!$listaEnzimas[$k]['usada'] && Comprobar($listaEnzimas[$k])){
                                            $nuevo_index=$k;
                                            break;
                                        }
                                    }
                                    if($nuevo_index == 0){ /*Ya no hay enzimas utiles*/
                                        $continuarBuscando = false;
                                        array_push($stackParejas, false);
                                    }
                                    else{ /*Agregar nueva enzima*/
                                        $listaEnzimas[$nuevo_index]['usada'] = true;
                                        array_push($stackParejas, $listaEnzimas[$nuevo_index]);
                                        /**/
                                        $Enzima = $listaEnzimas[$nuevo_index];
                                        $indexB = 0;
                                        /*BuscaParejas($listaEnzimas[$nuevo_index], $listaEnzimas, 0);*/
                                    }
                                }
                            }
                        }
                        else{ 
                           $continuarBuscando = false;
                           array_push($stackParejas, true);
                        }

                    }
                    /* /////////////////////////// END BUSCAR PAREJAS /////////////////////////// */

                    /*echo '<br/>============================<br/>END LISTA!!!!<br/>-------------------<br/>';*/
                    if(array_pop($stackParejas)){
                        /*echo '<br/>contruct!<br/> ENZIMAS: <br/>';*/
                        $hayConstruct = true;
                    }
                    else{
                        /*echo '<br/>noContruct!<br/>';*/
                        $stackParejas = array();
                    }
                    /*var_dump($stackParejas);*/

                    if($hayConstruct){
                        /*echo '<br/>============================<br/>IMPRIMIR CONSTRUCT!!!!<br/>-------------------<br/>';*/
                        //ImprimirConstruct
                        $ConstructFinal = ArmarConstruct($secuencias,$stackParejas);
                        /*var_dump($ConstructFinal);*/
                        //var_dump($stackParejas);
                        
                        ?>
                            <!--    ANSWER HTML 
                                    MARKUP BEGIN    -->
                            
                            <br/>
                            <br/>
                            
                            <div id="output_container">
                                
                                <div id="opcion2">
                                    
                                    <?php
                                    
                                        echo '<div class="enzyme_out">'.
                                                "<a href=\"#\" style=\"display: block;\" class=\"highslide\" onclick=\"return hs.htmlExpand(this, { contentId: 'highslide-html-enzime-0' } )\">".
                                                '<p>' . $ConstructFinal[1][0]['Nombre'] . '</p></a>'.
                                                '</div>';
                                        echo '<div class="highslide-html-content" id="highslide-html-enzime-0">'.
                                                    '<div class="highslide-header">'.
                                                        '<ul>'.
                                                            '<li class="highslide-move">'.
                                                                '<a href="#" onclick="return false">Move</a>'.
                                                            '</li>'.
                                                            '<li class="highslide-close">'.
                                                                '<a href="#" onclick="return hs.close(this)">Close</a>'.
                                                            '</li>'.
                                                        '</ul>'.
                                                    '</div>'.
                                                    '<div class="highslide-body">'.
                                                        '<p><span style="font-weight: bold;">Name: </span>'.$ConstructFinal[1][0]['Nombre'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Sequence: </span>'.$ConstructFinal[1][0]['Corte'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Type: </span>'.$ConstructFinal[1][0]['Type'].'</p>';
                                                
                                        for( $i = 0; $i < count($ConstructFinal[1][0]['Buffers']); $i++){
                                                echo '<p><span style="font-weight: bold;">Buffer'.($i+1).': </span>'.$ConstructFinal[1][0]['Buffers'][$i].'</p>';
                                        }
                                                
                                                echo '<p><span style="font-weight: bold;">Optimum Temperature: </span>'.$ConstructFinal[1][0]['Temperatura'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Heat Inactivation: </span>'.$ConstructFinal[1][0]['Inactivacion'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Blocked when methylated: </span>'.$ConstructFinal[1][0]['Metilacion'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Star Activity: </span>'.$ConstructFinal[1][0]['StarActivity'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Supplier: </span>'.$ConstructFinal[1][0]['Proveedor'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Concentration: </span>'.$ConstructFinal[1][0]['Concentracion'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Price: </span>'.$ConstructFinal[1][0]['Precio'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Source: </span><a href="'.$ConstructFinal[1][0]['Fuente'].'" target="_blank">'.$ConstructFinal[1][0]['Fuente'].'</a></p>'.
                                                    '</div>'.
                                                    '<div class="highslide-footer">'.
                                                        '<div>'.
                                                            '<span class="highslide-resize" title="Resize">'.
                                                                '<span></span>'.
                                                            '</span>'.
                                                        '</div>'.
                                                    '</div>'.
                                                '</div>';
                                    
                                        for( $i = 1; $i <= $number_of_seq; $i++ ){
                                            /*echo '<div class="sequence_out"><p>Sequence ' . $i . '</p></div>';*/
                                            echo '<div class="sequence_out">'.
                                                    "<a href=\"#\" style=\"display: block;\" class=\"highslide\" onclick=\"return hs.htmlExpand(this, { contentId: 'highslide-html-sequence-$i' } )\">".
                                                    '<p>Sequence ' . $i . '</p></a>'.
                                                    '</div>';
                                            echo '<div class="highslide-html-content" id="highslide-html-sequence-'.$i.'">'.
                                                        '<div class="highslide-header">'.
                                                            '<ul>'.
                                                                '<li class="highslide-move">'.
                                                                    '<a href="#" onclick="return false">Move</a>'.
                                                                '</li>'.
                                                                '<li class="highslide-close">'.
                                                                    '<a href="#" onclick="return hs.close(this)">Close</a>'.
                                                                '</li>'.
                                                            '</ul>'.
                                                        '</div>'.
                                                        '<div class="highslide-body">'.
                                                            '<p style="font-weight:bold;">Promoter:</p>'.
                                                            '<div>'.$ConstructFinal[1][2*$i-1][0].'</div>'.
                                                            '<p style="font-weight:bold;">RBS:</p>'.
                                                            '<div>'.$ConstructFinal[1][2*$i-1][1].'</div>'.
                                                            '<p style="font-weight:bold;">Gene:</p>'.
                                                            '<div>'.$ConstructFinal[1][2*$i-1][2].'</div>'.
                                                            '<p style="font-weight:bold;">Terminator:</p>'.
                                                            '<div>'.$ConstructFinal[1][2*$i-1][3].'</div>'.
                                                        '</div>'.
                                                        '<div class="highslide-footer">'.
                                                            '<div>'.
                                                                '<span class="highslide-resize" title="Resize">'.
                                                                    '<span></span>'.
                                                                '</span>'.
                                                            '</div>'.
                                                        '</div>'.
                                                    '</div>';
                                            
                                            /*echo '<div class="enzyme_out"><p>' . $ConstructFinal[1][2*$i]['Nombre'] . '</p></div>';*/
                                            echo '<div class="enzyme_out">'.
                                                    "<a href=\"#\" style=\"display: block;\" class=\"highslide\" onclick=\"return hs.htmlExpand(this, { contentId: 'highslide-html-enzime-$i' } )\">".
                                                    '<p>' . $ConstructFinal[1][2*$i]['Nombre'] . '</p></a>'.
                                                    '</div>';
                                            echo '<div class="highslide-html-content" id="highslide-html-enzime-'.$i.'">'.
                                                        '<div class="highslide-header">'.
                                                            '<ul>'.
                                                                '<li class="highslide-move">'.
                                                                    '<a href="#" onclick="return false">Move</a>'.
                                                                '</li>'.
                                                                '<li class="highslide-close">'.
                                                                    '<a href="#" onclick="return hs.close(this)">Close</a>'.
                                                                '</li>'.
                                                            '</ul>'.
                                                        '</div>'.
                                                        '<div class="highslide-body">'.
                                                            '<p><span style="font-weight: bold;">Name: </span>'.$ConstructFinal[1][2*$i]['Nombre'].'</p>'.
                                                            '<p><span style="font-weight: bold;">Sequence: </span>'.$ConstructFinal[1][2*$i]['Corte'].'</p>'.
                                                            '<p><span style="font-weight: bold;">Type: </span>'.$ConstructFinal[1][2*$i]['Type'].'</p>';

                                            for( $j = 0; $j < count($ConstructFinal[1][2*$i]['Buffers']); $j++){
                                                    echo '<p><span style="font-weight: bold;">Buffer'.($j+1).': </span>'.$ConstructFinal[1][2*$i]['Buffers'][$j].'</p>';
                                            }

                                                    echo '<p><span style="font-weight: bold;">Optimum Temperature: </span>'.$ConstructFinal[1][2*$i]['Temperatura'].'</p>'.
                                                            '<p><span style="font-weight: bold;">Heat Inactivation: </span>'.$ConstructFinal[1][2*$i]['Inactivacion'].'</p>'.
                                                            '<p><span style="font-weight: bold;">Blocked when methylated: </span>'.$ConstructFinal[1][2*$i]['Metilacion'].'</p>'.
                                                            '<p><span style="font-weight: bold;">Star Activity: </span>'.$ConstructFinal[1][2*$i]['StarActivity'].'</p>'.
                                                            '<p><span style="font-weight: bold;">Supplier: </span>'.$ConstructFinal[1][2*$i]['Proveedor'].'</p>'.
                                                            '<p><span style="font-weight: bold;">Concentration: </span>'.$ConstructFinal[1][2*$i]['Concentracion'].'</p>'.
                                                            '<p><span style="font-weight: bold;">Price: </span>'.$ConstructFinal[1][2*$i]['Precio'].'</p>'.
                                                            '<p><span style="font-weight: bold;">Source: </span><a href="'.$ConstructFinal[1][2*$i]['Fuente'].'" target="_blank">'.$ConstructFinal[1][2*$i]['Fuente'].'</a></p>'.
                                                        '</div>'.
                                                        '<div class="highslide-footer">'.
                                                            '<div>'.
                                                                '<span class="highslide-resize" title="Resize">'.
                                                                    '<span></span>'.
                                                                '</span>'.
                                                            '</div>'.
                                                        '</div>'.
                                                    '</div>';
                                            
                                        }
                                    
                                    ?>
                                    
                                </div>
                                
                                <br/>
                                <br/>
                                
                                <div id="output_buttons">
                                    <div class="seq2syn" onclick="seq2syn();"></div>
                                    <div class="complete_seq" onclick="showCompleteSeq();"></div>
                                </div>
                                    
                            </div>
                            
                            <hr/>
                            
                            <!--    ANSWER HTML 
                                    MARKUP BEGIN    -->
                            
                        <?php
                        
                    }
                    else{
                        print('No hay respuesta!');
                    }
                    
                }
                else{
                    echo '<p style="color: red; text-align: center;">Input data error: '.$error_type.'</p>';
                    echo '<p style="color: red; text-align: center;">Ensure that every DNA sequence has at least one correct field.</p>';
                }
                /*****************************************************************************************************/
                /*****************************************************************************************************/
                /*****************************************************************************************************/
                
            }
        ?>
                            
        <div id="input_container">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                <div id="more_input">
                    <p>Vector</p>
                        <textarea id="vector" name="vector" ><?php echo $sticky_vector; ?></textarea>
                    <p>Enzymes to be used</p>
                        <textarea id="enzymes_to_use" name="enzymes_to_use" placeholder="EnzimeName1,EnzimeName2,EnzimeName3,..."><?php echo $sticky_enzymes_list_yes; ?></textarea>
                    <p>Enzymes to be avoided</p>
                        <textarea id="enzymes_not_to_use" name="enzymes_not_to_use" placeholder="EnzimeName1,EnzimeName2,EnzimeName3,..."><?php echo $sticky_enzymes_list_no; ?></textarea>
                </div>

                <div id="all_sequences">

                    <div id="sequence_1" class="sequence_form">
                        <hr/>
                        <h3>Sequence 1</h3>
                        <p>Promoter</p>
                        <textarea id="promoter_1" name="promoter_1" ></textarea>
                        <p>RBS</p>
                        <textarea id="rbs_1" name="rbs_1" ></textarea>
                        <p>Gene</p>
                        <textarea id="gene_1" name="gene_1" ></textarea>
                        <p>Terminator</p>
                        <textarea id="terminator_1" name="terminator_1" ></textarea>
                    </div>

                </div>

                <div class="submit_container">
                    <input type="hidden" value="1" id="seq_num" name="seq_num" />
                    <div class="add_sequence" onclick="addSequence();"></div>
                    <div class="remove_sequence" onclick="removeSequence();"></div>
                    <input class="input_go" type="submit" value="" name="submit" />
                    <br/>
                    <br/>
                    <br/>
                </div>

            </form>
        </div>
                            
    </body>
    
</html>
