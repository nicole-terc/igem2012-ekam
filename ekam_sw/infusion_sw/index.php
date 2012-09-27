<!DOCTYPE html>
<html>
    
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="http://fonts.googleapis.com/css?family=Skranji&subset=latin,latin-ext" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <title>In-Fusion</title> 
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
            $sticky_vector_enzyme = '';
            $sticky_enzymes_list_yes='';
            $sticky_enzymes_list_no='';
            $vector='';
            $secuencias='';
            $secuencias_original = '';
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
                $v_enzyme = $post['vector_enzyme'];
                $random_bp = $post['number_of_random_bp'];
                
                if( empty($v_vector) || !check($v_vector,array('A','T','G','C')) )
                    return 'vector_error';
                if(empty($v_enzyme))
                    return 'vector_enzyme_error';
                if(empty($random_bp) || is_int($random_bp))
                    return 'length_prefix/suffix_error';
                
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
            
            /*==================== BUSCAR ENZIMA VECTOR ====================*/
            function BuscarEnzimaVector($aleatorio, $index, $final){
                global $secuencias, $lista;
                $lista_length = count($lista);
                $enzima = false;
                /*echo '<br/> //------> INICIO BuscarEnzimaVector<br/>', $aleatorio, ' || ', $index, ' || ', $final, '<br/>';*/

                if(!$final){ //Se busca una enzima cuya secuencia termine donde empieza el aleatorio inicial, definido por el vector
                    $aux = $secuencias[0];
                    $aux[0] = $aleatorio . $aux[0];
                    for($i = $index; $i < $lista_length; $i++){
                        if($lista[$i]['Multiples'] === false){
                            $lista[$i] = agregarMultiples($lista[$i],'Corte');
                        }
                        if($lista[$i]['Multiples'] != 'no'){
                            for($k = 0; $k < count($lista[$i]['Multiples']); $k++){
                                $sec_final = explode('||', $lista[$i]['Multiples'][$k]);
                                $sec_final = $sec_final[1]; //se toma solo la mitad final de la secuencia de la enzima
                                if(!$sec_final)
                                    $sec_final = $aleatorio;
                                if(strpos($aleatorio, $sec_final) === 0 
                                        && ComprobarEnSecuencia($lista[$i],$aux)){ /*se revisa que este al incio de la secuencia aleatoria*/
                                    $enzima = $lista[$i];
                                    break;
                                }
                            }
                        }
                        if($enzima !== false)
                            break;
                    }

                }
                else{ //Se busca una enzima cuya secuencia inicie donde termina el aleatorio final, definido por el vector
                    $aux = $secuencias[count($secuencias)-1];
                    $aux[3] .= $aleatorio;
                    for($i = $index; $i < $lista_length; $i++){
                        if($lista[$i]['Multiples'] === false){
                            $lista[$i] = agregarMultiples($lista[$i],'Corte');
                        }
                        if($lista[$i]['Multiples'] != 'no'){
                            for($k = 0; $k < count($lista[$i]['Multiples']); $k++){
                                $sec_inicial = explode('||', $lista[$i]['Multiples'][$k]);
                                $sec_inicial = $sec_inicial[0];
                                if(!$sec_inicial)
                                    $sec_inicial = $aleatorio;
                                if(strrpos($aleatorio, $sec_inicial) == (strlen($aleatorio) - strlen($sec_inicial)) //se revisa que este al final de la secuencia aleatoria
                                        && ComprobarEnSecuencia($lista[$i],$aux)){
                                    $enzima = $lista[$i];
                                    break;
                                }
                            }
                        }
                        if($enzima !== false)
                            break;
                    }
                }
                /*echo 'Regresa: <br/>';
                var_dump($enzima);
                echo '<br/>------------------------FIN BuscarEnzimaVector --------------------------<br/>';*/

                return $enzima;
            }

            /* ==================== BUSCAR ENZIMA GLOBAL ====================*/
            function BuscarEzimaGlobal ($index){
                global $secuencias, $lista;   
                /*echo '<br/> //------> INICIO BuscarEnzimaGlobal<br/>', $index, '<br/>';*/
                /*var_dump($secuencias);*/

                $secuencias_length = count($secuencias) -1;
                $lista_length = count($lista);
                $enzima = false;

                for($i = $index; $i < $lista_length; $i++){
                    $paso = false;

                    if($lista[$i]['Multiples'] === false){
                            $lista[$i] = agregarMultiples($lista[$i],'Corte');
                        }
                    if($lista[$i]['Multiples'] != 'no'){
                            $paso = true;
                            for($k = 0; $k < count($lista[$i]['Multiples']); $k++){
                                /*conseguir las secuencias de la enzima*/
                                $sec_enzima = explode('||', $lista[$i]['Multiples'][$k]);
                                $enzima_secuencia = $sec_enzima[0] . $sec_enzima[1];

                                /* ----------------- Checar en la primer secuencia ----------------- */
                                $prom_rbs_gen = $secuencias[0][0] . $secuencias[0][1]; //tomar promotor y rbs del primer gen, no incluir secuencia de enzima
                                $length_prom_rbs = strlen($prom_rbs_gen) - strlen($lista[$i]['Secuencia']) + 1; //espacio incortable
                                $prom_rbs_gen .= $secuencias[0][2]; // Agregar gen
                                $terminador = $secuencias[0][3] . $sec_enzima[0];
                                $indexOf = strpos($prom_rbs_gen, $enzima_secuencia);


                                if(($indexOf !== false && $indexOf < $length_prom_rbs) /*Revisar si esta en el espacio incortable*/
                                        || strpos($terminador,$enzima_secuencia)!== false /*Revisar si esta en terminador*/
                                        || substr_count($secuencias[0][2], $enzima_secuencia)>10){  /*Revisar si corta mucho en la enzima*/
                                    $paso = false;
                                    break;
                                }
                                /*------------------*/

                                /* ----------------- Checar en todas las secuencias menos en la primera y la ultima ----------------- */
                                for($j = 1; $j < $secuencias_length; $j++){
                                    $prom_rbs_gen = $sec_enzima[1] . $secuencias[$j][0] . $secuencias[$j][1]; //tomar mitad final de la secuencia de la enzima, promotor y rbs del primer gen;
                                    $length_prom_rbs = strlen($prom_rbs_gen) - strlen($lista[$i]['Secuencia']) + 1; //espacio incortable
                                    $prom_rbs_gen .= $secuencias[$j][2]; // Agregar gen
                                    $terminador = $secuencias[$j][3] . $sec_enzima[0];
                                    $indexOf = strpos($prom_rbs_gen, $enzima_secuencia);


                                    if(($indexOf !== false && $indexOf < $length_prom_rbs) /*Revisar si esta en el espacio incortable*/
                                            || strpos($terminador,$enzima_secuencia)!== false /*Revisar si esta en terminador*/
                                            || substr_count($secuencias[$j][2], $enzima_secuencia)>10){  /*Revisar si corta mucho en la enzima*/
                                        $paso = false;
                                        break;
                                    }
                                }
                                /*------------------*/

                                /* ----------------- Checar en la ultima secuencia ----------------- */
                                $prom_rbs_gen = $sec_enzima[1] . $secuencias[$secuencias_length][0] . $secuencias[0][1]; //tomar promotor y rbs del primer gen, no incluir secuencia de enzima
                                $length_prom_rbs = strlen($prom_rbs_gen) - strlen($lista[$i]['Secuencia']) + 1; //espacio incortable
                                $prom_rbs_gen .= $secuencias[$secuencias_length][2]; // Agregar gen
                                $terminador = $secuencias[$secuencias_length][3];
                                $indexOf = strpos($prom_rbs_gen, $enzima_secuencia);


                                if(($indexOf !== false && $indexOf < $length_prom_rbs) /*Revisar si esta en el espacio incortable*/
                                        || strpos($terminador,$enzima_secuencia)!== false /*Revisar si esta en terminador*/
                                        || substr_count($secuencias[$secuencias_length][2], $enzima_secuencia)>10){  /*Revisar si corta mucho en la enzima*/
                                    $paso = false;
                                    break;
                                }
                                /*------------------*/

                            }
                        }

                    if($paso){
                        $enzima = $lista[$i];
                        break;
                    }
                }

                /*echo 'Regresa: <br/>';
                var_dump($enzima);
                echo '<br/>------------------------FIN BuscarEnzimaGlobal --------------------------<br/>'; */

                return $enzima;
            }

            /* ==================== COMPROBAR EN SECUENCIA ====================*/
            function ComprobarEnSecuencia ($enzima, $secuencia){
                /*echo '<br/> //------> INICIO ComprobarEnSecuencia <br/>';
                var_dump($enzima);
                var_dump($secuencia);
                echo '<br/>----<br/>';*/
                if($enzima['Multiples'] === false)
                    $enzima = agregarMultiples($enzima,'Corte');
                if($enzima['Multiples'] == 'no')
                    return false;

                for($k = 0; $k < count($enzima['Multiples']); $k++){
                    $enzima_secuencia = str_replace('||', '', $enzima['Multiples'][$k]);
                    $prom_rbs_gen = $secuencia[0] . $secuencia[1]; //tomar promotor y rbs de la secuencia
                    $length_prom_rbs = strlen($prom_rbs_gen) - strlen($enzima['Secuencia']) + 1; //espacio incortable
                    $prom_rbs_gen .= $secuencia[2]; // Agregar gen
                    $terminador = $secuencia[3];
                    $indexOf = strpos($prom_rbs_gen, $enzima_secuencia);

                    /*echo '<br/>ComprobarEnSecuencia ||',$indexOf,'||', $length_prom_rbs, '||', $prom_rbs_gen, '||', $terminador, '<br/>';*/

                    if(($indexOf !== false && $indexOf < $length_prom_rbs) /*Revisar si esta en el espacio incortable*/
                            || strpos($terminador,$enzima_secuencia)!== false /*Revisar si esta en terminador*/
                            || substr_count($secuencia[2], $enzima_secuencia)>10){  /*Revisar si corta mucho en la enzima*/
                        /*echo '<br/>------------------------FIN ComprobarEnSecuencia FALSE --------------------------<br/>';*/
                        return false;
                    }
                }

                /*echo '<br/>------------------------FIN ComprobarEnSecuencia TRUE --------------------------<br/>';*/
                return true;
            }

            /* ==================== OBTENER ALEATORIO ====================*/
            function ObtenerAleatorio($enzima, $numeroBases){
               /* echo '<br/> //------> INICIO ObtenerAleatorio <br/>', $enzima['Secuencia'], ' || ', $numeroBases, '<br/>';*/
                $numeroBases -= strlen($enzima['Secuencia']);

                while(true){
                    $devolver = true;
                    $aux = '';
                    for($i = 0; $i < $numeroBases; $i++){
                        $n = rand(0, 3);
                        switch ($n){
                            case 0: 
                                $aux .= 'A'; break;
                            case 1: 
                                $aux .= 'T'; break;
                            case 2: 
                                $aux .= 'G'; break;
                            case 3: 
                                $aux .= 'C'; break;
                        }
                    }
                    for($k = 0; $k < count($enzima['Multiples']); $k++){
                        $sec_enzima = explode('||', $enzima['Multiples'][$k]);
                        $auxMultiple = $sec_enzima[1] . $aux . $sec_enzima[0];
                        for($j = 0; $j< count($enzima['Multiples']); $j++){
                            $aux_aBuscar = str_replace('||', '', $enzima['Multiples'][$j]); 
                            if(strpos($auxMultiple, $aux_aBuscar) !== false){
                                $devolver = false;
                            }
                        }
                    }
                    /*echo 'Regresa: <br/>', $aux, '<br/>';
                    echo '<br/>------------------------FIN ObtenerAleatorio --------------------------<br/>';            */
                    if($devolver){
                        $sec_enz = explode('||', $enzima['Corte']);
                        $aux = $sec_enz[1]. $aux . $sec_enz[0];
                        return $aux;
                    }
                }
            }
            /* ==================== AGREAGAR MULTIPLES ====================*/
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

            function agregarMultiples($Enzima,$propiedad){
                global $lista;
                $multiples = array('');

                for($k = 0; $k < strlen($Enzima[$propiedad]); $k++){
                    $length_actual = count($multiples);
                    switch ($Enzima[$propiedad]{$k}){
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
                                $multiples[$i] .= $Enzima[$propiedad]{$k};
                            }
                    }
                    if(count($multiples)>16){
                        $multiples = 'no';
                        break;
                    }
                } 

                $Enzima['Multiples'] = $multiples;
                if($Enzima['index'] !== false)
                    $lista[$Enzima['index']] = $Enzima;

                return $Enzima;
            }
             /*/////////ARMAR Construct///////////////*/

            function ArmarConstruct($secuencias, $aleatorios, $enzima_global_corte){
                $secuencias_length = count($secuencias);
                $construct_pedazos = array();
                $construct_junto = array($aleatorios[0]);
                $construct_secuencia = $aleatorios[0];

                for($i = 0; $i < $secuencias_length; $i++){
                    if($i == 0){
                        $aux_inicio = $aleatorios[$i];
                        $aux_final = $aleatorios[$i+1] . $enzima_global_corte[1];
                    }
                    else if ($i == $secuencias_length -1){
                        $aux_inicio = $enzima_global_corte[0] . $aleatorios[$i];
                        $aux_final = $aleatorios[$i+1];
                    }
                    else{
                        $aux_inicio = $enzima_global_corte[0] . $aleatorios[$i];
                        $aux_final = $aleatorios[$i+1] . $enzima_global_corte[1];
                    }
                    
                    /*pedazo1-pedazo2-... Pedazo = {E1-(promotor,rbs,gen,terminador)-E2}*/
                    $aux = array($aux_inicio, $secuencias[$i], $aux_final);
                    $construct_pedazos[$i] = $aux;

                    /*Aleatorio-secuencia-Aleatorio-...*/
                    $construct_junto[2*$i+1] = $secuencias[$i];
                    $construct_junto[2*$i+2] = $aleatorios[$i+1];

                    /*Secuencia sin divisiones ATGCATGC...*/ 
                    for($j = 0; $j < 4; $j++){
                        $construct_secuencia .= $secuencias[$i][$j];
                    }
                    $construct_secuencia .= $aleatorios[$i+1];
                }
                $construct = array($construct_pedazos, $construct_junto, $construct_secuencia);
                return $construct;
            }
            /* ==================================== FIN FUNCIONES ==================================== */
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
                $sticky_vector_enzyme = $fixed_post['vector_enzyme'];
                $sticky_enzymes_list_yes=$fixed_post['enzymes_to_use'];
                $sticky_enzymes_list_no=$fixed_post['enzymes_not_to_use'];                
                
                if ( $error_type == '' ){ // no hay error
                    $vector = $fixed_post['vector'];                    
                    $numero_bases_aleatorias = $fixed_post['number_of_random_bp'];
                    $number_of_seq = $fixed_post['seq_num'];
                    $secuencias=array();
                    for($i=1; $i<=$number_of_seq; $i++)
                        array_push($secuencias,array($fixed_post['promoter_'.$i],$fixed_post['rbs_'.$i],$fixed_post['gene_'.$i],$fixed_post['terminator_'.$i]));

                    require_once 'connectionDB.php';
                    
                    /* ******************************** Conseguir enzima vector ******************************** */
                    $enzima_vector = preg_replace("/\s\s+/","",$fixed_post['vector_enzyme']);   /* quita exceso de blank spaces y enters */
                    $enzima_vector = str_ireplace(" ", "", $enzima_vector);      /* quita espacio simples */
                    $enzima_vector = str_replace("\t","", $enzima_vector);  /*Quita tabs*/
                    
                    $query = "SELECT * FROM enzimas WHERE nombre='".$enzima_vector."'";
                    $result = mysqli_query($dbc,$query) or die("Error querying Database.");
                    
                    $row = mysqli_fetch_array($result);
                        /*echo $row['nombre'] . "<br/>";*/
                        $enzima_vector = array(
                                    'index'=>false,
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
                    /* ******************************** Conseguir Listas de enzimas ******************************** */
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

                    $query = "SELECT * FROM enzimas WHERE (" . $enzyme_names_yes . ") AND tipo='blunt' ORDER BY LENGTH (secuencia)";
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
                    $query = "SELECT * FROM enzimas WHERE (";
                    array_push($ids_list_to_remove,74); // enzima dummy TERKIANOS id=74
                    $aux = count($ids_list_to_remove);
                    for( $i = 0; $i < ($aux-1); $i++ )
                        $query .= "id!=" . $ids_list_to_remove[$i] . " AND ";
                    $aux--;
                    $query .= "id!=" . $ids_list_to_remove[$aux] . ") AND tipo='blunt' ORDER BY LENGTH(secuencia)";
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


                     /*Variables:
                    -$secuencias
                    -$vector
                    -$enzima_vector
                    -$lista
                    -$numero_bases_aleatorias
                    -$number_of_seq                 
                */
                  /*****************************************************************************************************/
                  /*****************************************************************************************************/
                  /*****************************************************************************************************/
                $secuencias_original = $secuencias;
                $continuar = false;
                $enzima_vector_length = strlen($enzima_vector['Secuencia']);
                $enzima_vector_corte_length = explode('||', $enzima_vector['Corte']);
                $numero_secuencias = count($secuencias_original);
                $vector_circular =  $vector . substr($vector, 0, $enzima_vector_length - 1);  /*Hacer el vector 'Circular' con la secuencia de la enzima*/

                /*Agregadas las ambiguas*/
                if($enzima_vector['Multiples'] === false){
                    $enzima_vector = agregarMultiples($enzima_vector, 'Secuencia');
                    /*echo 'Enzima Vector <br/>';*/
                    /*var_dump($enzima_vector);*/
                }
                if($enzima_vector['Multiples'] != 'no'){
                    $count = 0;
                    $continuar = true;
                    for($k = 0; $k < count($enzima_vector['Multiples']); $k++){
                        /*echo "K ", $k, "---", substr_count($vector_circular, $enzima_vector['Multiples'][$k]);*/
                        if(strpos($vector_circular, $enzima_vector['Multiples'][$k]) !== false){
                            $count += substr_count($vector_circular, $enzima_vector['Multiples'][$k]);
                            /*/enzima_vector_length += $numero_bases_aleatorias;*/
                            if($count > 1)
                                break;
                            $index_enzima = strpos($vector_circular, $enzima_vector['Multiples'][$k]) + strlen($enzima_vector_corte_length[0]) -1 + $enzima_vector_length +$numero_bases_aleatorias ;

                        }
                    }
                    if($count != 1){
                        $continuar = false;
                        echo "<p style='color:red; font-weight: bold;'>Error! The enzyme cuts multiple times or doesn't cut within the vector.</p> <br/>";
                    }
                }
                if($continuar){
                    $secuencias = $secuencias_original;
                    $aleatorias = array_fill(0, $numero_secuencias +1 , '');

                    /*Obtener secuencias 'aleatorias' del vector*/
                    /*Movido a la busqueda de multiples*/
                    $enzima_vector_length += $numero_bases_aleatorias;
                    /*$index_enzima = strpos($vector_circular, $enzima_vector['Secuencia']) + strlen($enzima_vector_corte_length[0]) -1 + $enzima_vector_length ;*/
                    $vector_circular = substr($vector, strlen($vector) - $enzima_vector_length, $enzima_vector_length ) /*Hacer el vector 'Circular' con las bases aleatorias*/
                        . $vector
                        . substr($vector, 0, $enzima_vector_length);

                    $aleatorias[0] = substr($vector_circular, $index_enzima - $numero_bases_aleatorias + 1, $numero_bases_aleatorias);
                    $aleatorias[$numero_secuencias] = substr($vector_circular, $index_enzima + 1, $numero_bases_aleatorias);
                    /*var_dump($aleatorias);*/
                    /*Obtener Enzimas establecidas por el vector*/
                    $enzima_inicio = BuscarEnzimaVector($aleatorias[0], 0, 0);
                    $enzima_final = BuscarEnzimaVector($aleatorias[$numero_secuencias], 0, 1);

                    /*
                    echo '<br/> ------------------- ENZIMAS DEL VECTOR ------------------- <br/>';
                    echo 'enzima_inicio<br/>';
                    var_dump($enzima_inicio);
                    echo '<br/>enzima final<br/>';
                    var_dump($enzima_final);*/


                    if(!$enzima_inicio || !$enzima_final){ /*no avanzar si no hay enzimas para las partes del vector*/
                        echo "ERROR!: I wasn't able to find an enzyme that match your linearized vector ends, please choose a different enzyme to cut your vector or a different random sequences length and try again.";
                    }
                    else{
                        $secuencias[0][0] = $aleatorias[0] . $secuencias[0][0];
                        $secuencias[$numero_secuencias-1][3] = $secuencias[$numero_secuencias-1][3] . $aleatorias[$numero_secuencias];
                        /*Conseguir enzima Global y sus aleatorios*/

                        $enzima_global = BuscarEzimaGlobal(0);

                        if(!$enzima_global){ // no continuar si no hay enzima global
                            echo "ERROR!: I wasn't able to find an enzyme that doesn't cut in any of the parts, please select the non global option and try again.";
                        }
                        /* '<br/> ===================== RESULTADOS FINALES ===================== <br/>';*/
                        else{ 
                            $enzima_global_corte = explode('||', $enzima_global['Corte']);
                            $enzima_inicio_corte = explode('||', $enzima_inicio['Corte']);
                            $enzima_final_corte = explode('||', $enzima_final['Corte']);
                            for($i = 1; $i < $numero_secuencias; $i++){ //Agregar Secuencias aleatorias
                                $aleatorias[$i] = ObtenerAleatorio($enzima_global, $numero_bases_aleatorias); // obtener secuencia
                                $secuencias[$i-1][3] .= $aleatorias[$i] . $enzima_global_corte[1]; // agregarla al final del terminador de la pasada
                                $secuencias[$i][0] = $enzima_global_corte[0] . $aleatorias[$i] . $secuencias[$i][0]; //agregar al incio de la actual
                            }
                            /*Agregar un armar construct para presentarlo de varias maneras*/
                           $ConstructFinal = ArmarConstruct($secuencias_original,$aleatorias, $enzima_global_corte);
                            /*
                            echo '<br/> ===================== ENZIMAS FINALES ===================== <br/>';
                            echo '<br/>Enzima Inicial<br/>';
                            var_dump($enzima_inicio);
                            echo '<br/>Enzima Final<br/>';
                            var_dump($enzima_final);
                            echo '<br/>Enzima Global<br/>';
                            var_dump($enzima_global);

                            echo '<br/> ===================== SECUENCIAS FINALES ===================== <br/>';
                            var_dump($secuencias);
                            echo 'Op1';
                            var_dump($ConstructFinal[0]);
                            echo 'Op2';
                            var_dump($ConstructFinal[1]);
                            echo 'Op3';
                            var_dump($ConstructFinal[2]);
                            */
                           
                           /* ---------- ANSWER -----------------------------------------------------------------------------*/
                           
                           ?>
                            <!--    ANSWER HTML 
                                    MARKUP BEGIN    -->
                            
                            <br/>
                            <br/>
                            
                            <div id="output_container">
                                
                                <div id="opcion2">
                                    
                                    <?php
                                        $nombre_aleatorios = 'InFusion';
                                        echo '<div class="enzyme_out">'.
                                                "<a href=\"#\" style=\"display: block;\" class=\"highslide\" onclick=\"return hs.htmlExpand(this, { contentId: 'highslide-html-enzime-0' } )\">".
                                                '<p>' . $nombre_aleatorios .' 1' . '</p></a>'.
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
                                                        '<p><span style="font-weight: bold;">Name: </span>'.$nombre_aleatorios.' 1'.'</p>'.
                                                        '<p><span style="font-weight: bold;">Final Sequence: </span>'.$ConstructFinal[1][0].'</p>'.
                                                        '<p><span style="font-weight: bold;">Sequence as Prefix: </span>'.$enzima_inicio_corte[0].$ConstructFinal[1][0].'</p>'.
                                                        '<p><span style="font-weight: bold;">Digest with enzyme: </span>'.$enzima_inicio['Nombre'].'</p>'.
                                                            '<br/><hr/><br/>'.
                                                            '<p><span style="font-weight: bold;">Name: </span>'.$enzima_inicio['Nombre'].'</p>'.
                                                            '<p><span style="font-weight: bold;">Sequence: </span>'.$enzima_inicio['Corte'].'</p>'.
                                                            '<p><span style="font-weight: bold;">Type: </span>'.$enzima_inicio['Type'].'</p>';
                                                
                                                    for( $b = 0; $b < count($enzima_inicio['Buffers']); $b++){
                                                            echo '<p><span style="font-weight: bold;">Buffer'.($b+1).': </span>'.$enzima_inicio['Buffers'][$b].'</p>';
                                                    }
                                                
                                                echo    '<p><span style="font-weight: bold;">Optimum Temperature: </span>'.$enzima_inicio['Temperatura'].'&deg;C</p>'.
                                                        '<p><span style="font-weight: bold;">Heat Inactivation: </span>'.$enzima_inicio['Inactivacion'].'&deg;C</p>'.
                                                        '<p><span style="font-weight: bold;">Blocked when methylated: </span>'.$enzima_inicio['Metilacion'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Star Activity: </span>'.$enzima_inicio['StarActivity'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Supplier: </span>'.$enzima_inicio['Proveedor'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Concentration: </span>'.$enzima_inicio['Concentracion'].' U/ml</p>'.
                                                        '<p><span style="font-weight: bold;">Price: </span>$'.$enzima_inicio['Precio'].' USD</p>'.
                                                        '<p><span style="font-weight: bold;">Source: </span><a href="'.$enzima_inicio['Fuente'].'" target="_blank">'.$enzima_inicio['Fuente'].'</a></p>'.
                                                    '</div>'.
                                                    '<div class="highslide-footer">'.
                                                        '<div>'.
                                                            '<span class="highslide-resize" title="Resize">'.
                                                                '<span></span>'.
                                                            '</span>'.
                                                        '</div>'.
                                                    '</div>'.
                                                '</div>';
                                    
                                        for( $i = 1; $i < $number_of_seq; $i++ ){
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
                                                    '<p>' . $nombre_aleatorios.' '. (2*$i) . '</p></a>'.
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
                                                            '<p><span style="font-weight: bold;">Name: </span>'.$nombre_aleatorios.' '. (2*$i) .'</p>'.
                                                            '<p><span style="font-weight: bold;">Final Sequence: </span>'.$ConstructFinal[1][2*$i].'</p>'.
                                                            '<p><span style="font-weight: bold;">Sequence as Suffix: </span>'.$ConstructFinal[1][2*$i]. $enzima_global_corte[1].'</p>'.
                                                            '<p><span style="font-weight: bold;">Sequence as Prefix: </span>'.$enzima_global_corte[0].$ConstructFinal[1][2*$i].'</p>'.
                                                            '<p><span style="font-weight: bold;">Digest with enzyme: </span>'.$enzima_global['Nombre'].'</p>'.
                                                            '<br/><hr/><br/>'.
                                                            '<p><span style="font-weight: bold;">Name: </span>'.$enzima_global['Nombre'].'</p>'.
                                                            '<p><span style="font-weight: bold;">Sequence: </span>'.$enzima_global['Corte'].'</p>'.
                                                            '<p><span style="font-weight: bold;">Type: </span>'.$enzima_global['Type'].'</p>';
                                                
                                                    for( $b = 0; $b < count($enzima_global['Buffers']); $b++){
                                                            echo '<p><span style="font-weight: bold;">Buffer'.($b+1).': </span>'.$enzima_global['Buffers'][$b].'</p>';
                                                    }
                                                
                                                echo    '<p><span style="font-weight: bold;">Optimum Temperature: </span>'.$enzima_global['Temperatura'].'&deg;C</p>'.
                                                        '<p><span style="font-weight: bold;">Heat Inactivation: </span>'.$enzima_global['Inactivacion'].'&deg;C</p>'.
                                                        '<p><span style="font-weight: bold;">Blocked when methylated: </span>'.$enzima_global['Metilacion'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Star Activity: </span>'.$enzima_global['StarActivity'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Supplier: </span>'.$enzima_global['Proveedor'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Concentration: </span>'.$enzima_global['Concentracion'].' U/ml</p>'.
                                                        '<p><span style="font-weight: bold;">Price: </span>$'.$enzima_global['Precio'].' USD</p>'.
                                                        '<p><span style="font-weight: bold;">Source: </span><a href="'.$enzima_global['Fuente'].'" target="_blank">'.$enzima_global['Fuente'].'</a></p>'.

                                                     
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
                                        /*Agregar ultima secuencia y aleatorio*/
                                        echo '<div class="sequence_out">'.
                                                    "<a href=\"#\" style=\"display: block;\" class=\"highslide\" onclick=\"return hs.htmlExpand(this, { contentId: 'highslide-html-sequence-$i' } )\">".
                                                    '<p>Sequence ' . $number_of_seq . '</p></a>'.
                                                    '</div>';
                                            echo '<div class="highslide-html-content" id="highslide-html-sequence-'.$number_of_seq.'">'.
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
                                                            '<div>'.$ConstructFinal[1][2*$number_of_seq-1][0].'</div>'.
                                                            '<p style="font-weight:bold;">RBS:</p>'.
                                                            '<div>'.$ConstructFinal[1][2*$number_of_seq-1][1].'</div>'.
                                                            '<p style="font-weight:bold;">Gene:</p>'.
                                                            '<div>'.$ConstructFinal[1][2*$number_of_seq-1][2].'</div>'.
                                                            '<p style="font-weight:bold;">Terminator:</p>'.
                                                            '<div>'.$ConstructFinal[1][2*$number_of_seq-1][3].'</div>'.
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
                                                    '<p>' . $nombre_aleatorios.' '. (2*$number_of_seq) . '</p></a>'.
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
                                                            '<p><span style="font-weight: bold;">Name: </span>'.$nombre_aleatorios.' '. (2*$number_of_seq) .'</p>'.
                                                            '<p><span style="font-weight: bold;">Final Sequence: </span>'.$ConstructFinal[1][2*$number_of_seq].'</p>'.
                                                            '<p><span style="font-weight: bold;">Sequence as Suffix: </span>'.$ConstructFinal[1][2*$number_of_seq]. $enzima_final_corte[1].'</p>'.
                                                            '<p><span style="font-weight: bold;">Digest with enzyme: </span>'.$enzima_final['Nombre'].'</p>'.
                                                            '<br/><hr/><br/>'.
                                                            '<p><span style="font-weight: bold;">Name: </span>'.$enzima_final['Nombre'].'</p>'.
                                                            '<p><span style="font-weight: bold;">Sequence: </span>'.$enzima_final['Corte'].'</p>'.
                                                            '<p><span style="font-weight: bold;">Type: </span>'.$enzima_final['Type'].'</p>';
                                                
                                                    for( $b = 0; $b < count($enzima_final['Buffers']); $b++){
                                                            echo '<p><span style="font-weight: bold;">Buffer'.($b+1).': </span>'.$enzima_final['Buffers'][$b].'</p>';
                                                    }
                                                
                                                echo    '<p><span style="font-weight: bold;">Optimum Temperature: </span>'.$enzima_final['Temperatura'].'&deg;C</p>'.
                                                        '<p><span style="font-weight: bold;">Heat Inactivation: </span>'.$enzima_final['Inactivacion'].'&deg;C</p>'.
                                                        '<p><span style="font-weight: bold;">Blocked when methylated: </span>'.$enzima_final['Metilacion'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Star Activity: </span>'.$enzima_final['StarActivity'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Supplier: </span>'.$enzima_final['Proveedor'].'</p>'.
                                                        '<p><span style="font-weight: bold;">Concentration: </span>'.$enzima_final['Concentracion'].' U/ml</p>'.
                                                        '<p><span style="font-weight: bold;">Price: </span>$'.$enzima_final['Precio'].' USD</p>'.
                                                        '<p><span style="font-weight: bold;">Source: </span><a href="'.$enzima_final['Fuente'].'" target="_blank">'.$enzima_final['Fuente'].'</a></p>'.

                                                     
                                                        '</div>'.
                                                        '<div class="highslide-footer">'.
                                                            '<div>'.
                                                                '<span class="highslide-resize" title="Resize">'.
                                                                    '<span></span>'.
                                                                '</span>'.
                                                            '</div>'.
                                                        '</div>'.
                                                    '</div>';
                                    
                                    ?>
                                    
                                </div>
                                
                                <br/>
                                <br/>
                                
                                <!--<div id="output_buttons">
                                    <div class="seq2syn" onclick="seq2syn();"></div>
                                    <div class="complete_seq" onclick="showCompleteSeq();"></div>
                                    
                                    <?php
                                        /*echo '<div class="seq2syn">'.
                                                "<a href=\"#\" style=\"display: block;\" class=\"highslide\" onclick=\"return hs.htmlExpand(this, { contentId: 'highslide-html-seq2syn' } )\">".
                                                '<p>' . ' 1' . '</p></a>'.
                                                '</div>';
                                        echo '<div class="highslide-html-content" id="highslide-html-seq2syn">'.
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
                                                    '<div class="highslide-body">';
                                                        for($i = 0; $i < $number_of_seq; $i++){
                                                            '<p><span style="font-weight: bold;">Name: </span> Sequence '. ($i+1) .'</p>'.
                                                            '<p style="font-weight:bold;">Prefix:</p>'.
                                                            '<div>'.$ConstructFinal[0][$i][0].'</div>'.
                                                            '<p style="font-weight:bold;">Sequence:</p>'.
                                                            '<div>'.$ConstructFinal[0][$i][1][0]. $ConstructFinal[0][$i][1][1]. $ConstructFinal[0][$i][1][2]. $ConstructFinal[0][$i][1][3].'</div>'.
                                                            '<p style="font-weight:bold;">Sufix:</p>'.
                                                            '<div>'.$ConstructFinal[0][$i][2].'</div>'.
                                                            '<hr/>';
                                                        }
                                        
                                               echo '</div>'.
                                                    '<div class="highslide-footer">'.
                                                        '<div>'.
                                                            '<span class="highslide-resize" title="Resize">'.
                                                                '<span></span>'.
                                                            '</span>'.
                                                        '</div>'.
                                                    '</div>'.
                                                '</div>';*/
                                    ?>
                                </div> -->
                                    
                            </div>
                            
                            <hr/>
                            
                            <!--    ANSWER HTML 
                                    MARKUP BEGIN    -->
                            
                        <?php
                        }
                    }
                }
                  
                /*****************************************************************************************************/
                /*****************************************************************************************************/
                /*****************************************************************************************************/
                }
                else{
                    echo '<p style="color: red; text-align: center;">Input data error: '.$error_type.'</p>';
                    echo '<p style="color: red; text-align: center;">Ensure that every DNA sequence has at least one correct field.</p>';
                }
                
               
            }
                    
        ?>
        <div id="input_container">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                <div id="more_input">
                    <p class="little_input">Length of infusion Prefix/Suffix sequences: <input type="text" id="number_of_random_bp" name="number_of_random_bp" value="15"/></p>
                    <p class="little_input">Enzyme for vector linearization: <input type="text" id="vector_enzyme" name="vector_enzyme" <?php echo $sticky_vector_enzyme; ?>/></p>
                    <br/>
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
                    <br/>
                </div>

            </form>
        </div>
    </body>
</html>