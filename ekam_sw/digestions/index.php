<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <title>Digestions</title>
        <script type="text/javascript">
            
            var mybool = true;
            
            function toggle(){
                var initial_gr = document.getElementById("initial_grams_dna");
                var final_gr = document.getElementById("final_grams_dna");
                var dna_sequence = document.getElementById("dna_sequence");
                var enzymeA_sequence = document.getElementById("enzymeA_sequence");
                var enzymeB_sequence = document.getElementById("enzymeB_sequence");
                if(mybool){
                    final_gr.removeAttribute("disabled");
                    dna_sequence.removeAttribute("disabled");
                    enzymeA_sequence.removeAttribute("disabled");
                    enzymeB_sequence.removeAttribute("disabled");
                    initial_gr.setAttribute("disabled", "disabled");
                }
                else{
                    initial_gr.removeAttribute("disabled");
                    final_gr.setAttribute("disabled", "disabled");
                    dna_sequence.setAttribute("disabled", "disabled");
                    enzymeA_sequence.setAttribute("disabled", "disabled");
                    enzymeB_sequence.setAttribute("disabled", "disabled");
                }
                mybool =  !mybool;
            }
        </script>
    </head>
    <body>
        
        <?php
        
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
            
            function fixInput($sec){
                $sec = preg_replace("/\s\s+/","",$sec);   /* quita exceso de blank spaces y enters */
                $sec = str_ireplace(" ", "", $sec);      /* quita espacio simples */
                $sec = str_replace("\t","", $sec);       /* quita tabs */
                $sec = strtoupper($sec);
                return $sec;
            }
            
            function calculaPM($secuencia){
                $PM = 157.9;
                for( $i = 0; $i < strlen($secuencia); $i++)
                    switch($secuencia{$i}){
                        case 'A':
                        case 'T': $PM+=617.4; break;
                        case 'G':
                        case 'C': $PM+=618.4; break;
                        default: $PM+=0;
                    }
                return $PM;
            }
            
            function sacarFragmento($dna_sequence, $enzymeA_sequence, $enzymeB_sequence){
                $enzymeA_wo_cut = str_replace("||", "", $enzymeA_sequence);
                $enzymeB_wo_cut = str_replace("||", "", $enzymeB_sequence);
                $indexA = -1;
                $encontrado = false;
                $dna_sequence .= $dna_sequence;
                do{
                    $indexA+=1;
                    $indexA = strpos($dna_sequence, $enzymeA_wo_cut, $indexA);
                    $indexB = -1;
                    while($indexB !== false ){
                        $indexB+=1;
                        $indexB = strpos($dna_sequence, $enzymeB_wo_cut, $indexB);

                        if($indexB > $indexA){
                            $encontrado = true;
                            break;
                        }
                    }
                }while(!$encontrado && $indexA !== false);

                if($encontrado){
                $indexA += strpos($enzymeA_sequence, "||");
                $indexB += strpos($enzymeB_sequence, "||");
                $fragment_length = $indexB - $indexA;
                return substr($dna_sequence, $indexA, $fragment_length);
                }
                else
                    return 'error';
            }
        
            $grams_dna = '';
            $initial_grams_dna = '';
            $final_grams_dna = '';
            $dna_concentration = '';
            $enzymeA_concentration = '';
            $enzymeB_concentration = '';
            $buffer_concentration = '';
            $bsa_concentration = '';
            $final_volume = '';
            $dna_sequence = '';
            $enzymeA_sequence = '';
            $enzymeB_sequence = '';
            
            $nuclease = '';
            $bsa = '';
            $buffer = '';
            $dna = '';
            $enzymeA = '';
            $enzymeB = '';
            
            $showOutput = false;
            $dna_grams = '';
            $dna_sequence_fragment = '';
            $cont=0;
            
            if(isset($_POST['submit'])){
                
                $grams_dna = $_POST['grams_dna'];
                
                if($grams_dna == 'initial')
                    $initial_grams_dna = $_POST['initial_grams_dna'];
                else{
                    $final_grams_dna = $_POST['final_grams_dna'];
                    $dna_sequence = fixInput($_POST['dna_sequence']);
                    $enzymeA_sequence = fixInput($_POST['enzymeA_sequence']);
                    $enzymeB_sequence = fixInput($_POST['enzymeB_sequence']);
                }
                
                $dna_concentration = $_POST['dna_concentration'];
                $enzymeA_concentration = $_POST['enzymeA_concentration'];
                $enzymeB_concentration = $_POST['enzymeB_concentration'];
                $buffer_concentration = $_POST['buffer_concentration'];
                $bsa_concentration = $_POST['bsa_concentration'];
                $final_volume = $_POST['final_volume'];
                
                if( !empty($dna_concentration) && is_numeric($dna_concentration) &&
                    !empty($enzymeA_concentration) && is_numeric($enzymeA_concentration) &&
                    !empty($enzymeB_concentration) && is_numeric($enzymeB_concentration) &&
                    !empty($buffer_concentration) && is_numeric($buffer_concentration) &&
                    !empty($bsa_concentration) && is_numeric($bsa_concentration) &&
                    !empty($final_volume) && is_numeric($final_volume) ){
                    
                    if( $grams_dna == 'initial' ){
                        if( !empty($initial_grams_dna) && is_numeric($initial_grams_dna) ){
                            $showOutput = true;
                            $dna = 1000 * $initial_grams_dna / $dna_concentration;
                            $dna_grams = $initial_grams_dna;
                        }
                    }else{
                        if( !empty($final_grams_dna) && is_numeric($final_grams_dna) &&
                            !empty($dna_sequence) && !empty($enzymeA_sequence) && !empty($enzymeB_sequence) )
                            if( check($dna_sequence,array('A','T','G','C')) &&
                                check($enzymeA_sequence,array('A','T','G','C','|')) &&
                                check($enzymeB_sequence,array('A','T','G','C','|')) ){
                                $dna_sequence_fragment = sacarFragmento($dna_sequence,$enzymeA_sequence,$enzymeB_sequence);
                                $dna_grams = calculaPM($dna_sequence) * $final_grams_dna / calculaPM($dna_sequence_fragment);
                                $dna = 1000 * $dna_grams / $dna_concentration;
                                if($dna_sequence_fragment != 'error')
                                    $showOutput = true;
                            }
                    }
                    
                    
                    $enzymeA = $dna_grams * 10000 / $enzymeA_concentration;
                    $enzymeB = $dna_grams * 10000 / $enzymeB_concentration;
                    $final_volume -= 10;
                    do{
                        $final_volume+=10;
                        $buffer = $final_volume / $buffer_concentration;
                        $bsa = $final_volume / $bsa_concentration;
                        $nuclease = $final_volume - $bsa - $enzymeA - $enzymeB - $buffer - $dna;
                        $cont++;
                    }while($nuclease < 0);
                    
                    $nuclease = number_format($nuclease, 4, '.', '');
                    $bsa  = number_format($bsa, 4, '.', '');
                    $buffer = number_format($buffer, 4, '.', '');
                    $dna = number_format($dna, 4, '.', '');
                    $enzymeA = number_format($enzymeA, 4, '.', '');
                    $enzymeB = number_format($enzymeB, 4, '.', '');
                }
                if(!$showOutput)
                    echo '<p style="color:red; font-weight:bold; text-align:center;">Please, fill in all the blanks. Be sure to provide numeric values and valid DNA sequences.</p>';
                if($dna_sequence_fragment == 'error')
                    echo '<p style="color:red; font-weight:bold; text-align:center;">Be sure to provide enzymes that cut within the given dna sequence.</p>';
            }
            
        ?>
        
        <?php if ($cont > 1) : ?>
                <style type="text/css">
                    #final_volume{
                        color:red;
                        font-weight:bold;
                    }
                </style>
        <?php endif; ?>	
        <div id="digestions_container">
        <h3>Input</h3>
            
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            
            <input name="grams_dna" id="initial_radio" type="radio" value="initial" checked="checked" onchange="toggle()"/>Initial grams of DNA
            <input type="text" id="initial_grams_dna" name="initial_grams_dna" value="<?php echo $initial_grams_dna; ?>"/> &micro;g<br/>
            <input name="grams_dna" id="final_radio" type="radio" value="final" onchange="toggle()"/>Final grams of DNA digestion
            <input type="text" id="final_grams_dna" name="final_grams_dna" value="<?php echo $final_grams_dna; ?>" disabled="disabled"/> &micro;g<br/>
            
            <div id="final_grams_dna_div">
                <label for="dna_sequence">DNA sequence:</label>
                <input type="text" id="dna_sequence" name="dna_sequence" value="<?php echo $dna_sequence; ?>" disabled="disabled"/><br/>
                <label for="enzymeA_sequence">Enzyme A sequence:</label>
                <input type="text" id="enzymeA_sequence" name="enzymeA_sequence" value="<?php echo $enzymeA_sequence; ?>" disabled="disabled"/><br/>
                <label for="enzymeB_sequence">Enzyme B sequence:</label>
                <input type="text" id="enzymeB_sequence" name="enzymeB_sequence" value="<?php echo $enzymeB_sequence; ?>" disabled="disabled"/><br/>
            </div>
            
            <label for="dna_concentration">DNA concentration:</label>
            <input type="text" id="dna_concentration" name="dna_concentration" value="<?php echo $dna_concentration; ?>"/>ng/&micro;l<br/>
            <label for="enzymeA_concentration" >Enzyme A concentration:</label>
            <input type="text" id="enzymeA_concentration" name="enzymeA_concentration" value="<?php echo $enzymeA_concentration; ?>"/>U/ml<br/>
            <label for="enzymeB_concentration" >Enzyme B concentration:</label>
            <input type="text" id="enzymeB_concentration" name="enzymeB_concentration" value="<?php echo $enzymeB_concentration; ?>"/>U/ml<br/>
            <label for="buffer_concentration" >Buffer concentration:</label>
            <input type="text" id="buffer_concentration" name="buffer_concentration" value="<?php echo $buffer_concentration; ?>"/>x<br/>
            <label for="bsa_concentration" >BSA concentration:</label>
            <input type="text" id="bsa_concentration" name="bsa_concentration" value="<?php echo $bsa_concentration; ?>"/>x<br/>
            <label for="final_volume" >Final volume:</label>
            <input type="text" id="final_volume" name="final_volume" value="<?php echo $final_volume; ?>"/>&micro;l<br/>
            <br/>
            <input id="digest_button" type="submit" value="" name="submit" />
            <br/>
            
        </form>
        
        <hr/>
        
        <?php if ($showOutput) : ?>
            <h3>Output</h3>
            <p><span style="font-family: 'calibri';font-size: 1.05em;"><?php echo $nuclease; ?></span> &micro;l of Nuclease free water</p>
            <p><span style="font-family: 'calibri';font-size: 1.05em;"><?php echo $bsa; ?></span> &micro;l of BSA</p>
            <p><span style="font-family: 'calibri';font-size: 1.05em;"><?php echo $buffer; ?></span> &micro;l of Buffer</p>
            <p><span style="font-family: 'calibri';font-size: 1.05em;"><?php echo $dna; ?></span> &micro;l of DNA</p>
            <p><span style="font-family: 'calibri';font-size: 1.05em;"><?php echo $enzymeA; ?></span> &micro;l of Enzyme A</p>
            <p><span style="font-family: 'calibri';font-size: 1.05em;"><?php echo $enzymeB; ?></span> &micro;l of Enzyme B</p>
            <?php if ($grams_dna == 'final') : ?>
            <p>DNA fragment: <br/> <span style="font-family: 'calibri';font-size: 1.05em;"><?php echo $dna_sequence_fragment; ?></span></p>
            <?php endif; ?>	
        <?php endif; ?>	
        
    </div>    
    </body>
</html>
