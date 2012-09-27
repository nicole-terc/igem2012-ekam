<!DOCTYPE html>
<html>
    <head>
        <link href="http://fonts.googleapis.com/css?family=Skranji&subset=latin,latin-ext" rel="stylesheet" type="text/css"/>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script type="text/javascript">
            function clr_input(){
                var input = document.getElementById('input');
                input.value = '';
            }
        </script>
        
    </head>
    <body>
        
        <h2>PROTEIN to DNA</h2>
        
        <?php
            require_once('utilities.php');
        
            function protein2adn($prot){
                global $ARRAY_CODONES_2;
                $prot_lenght = strlen($prot);
                $adn = '';
                
                for($i = 0; $i < $prot_lenght; $i++){
                    $aminoacid = substr($prot, $i, 1);
                    for($j = 0; $j < 64; $j++){
                        if($ARRAY_CODONES_2[1][$j] == $aminoacid){
                            $adn.=$ARRAY_CODONES_2[0][$j];
                            break;
                        }
                    }
                }
                
                return $adn;
            }
            
            $input = '';
            $output = '';

            if(isset($_POST['submit'])){
                $input = $_POST['input'];
                if(!empty($input)){
                    $protein = preg_replace("/\s\s+/","",$input);   /* quita exceso de blank spaces y enters */
                    $protein = str_ireplace(" ", "", $protein);      /* quita espacio simples */
                    $protein = str_replace("\t","", $protein);       /* quita tabs */
                    $protein = strtoupper($protein);
                     if(check($protein,array('A','R','N','D','C','Q','E','G','H','I','L','K','M','F','P','S','T','W','Y','V','*'))){ /* validated input */
                        $output = protein2adn($protein);
                     }
                     else
                         echo 'You must enter a valid protein sequence.';
                     
                }
                else
                    echo 'You forgot to write something!';
            }
        ?>
        
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            
            <label for="input">Input:</label><br/>
            <textarea id="input" name="input"><?php echo $input; ?></textarea><br/>
            
            <label for="output">Output:</label><br/>
            <div id="output" name="output" ><p><?php echo $output; ?></p></div><br/>
                   
            <input class="clearInput" type="button" value="" onclick="clr_input();" />
            <input class="translateInput" type="submit" value="" name="submit" />
                        
        </form>

    </body>
</html>