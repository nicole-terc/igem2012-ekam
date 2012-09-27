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
        
        <h2>DNA to PROTEIN</h2>
        
        <?php
            require_once('utilities.php');
        
            function adn2protein($frame){
                global $ARRAY_CODONES;
                $adn_lenght = (int)(strlen($frame)/3);
                $prot = '';
                for($i = 0; $i < $adn_lenght; $i++){
                    $codon = substr($frame, 3*$i, 3);
                    for($j = 0; $j < 64; $j++){
                        if($ARRAY_CODONES[0][$j] == $codon){
                            $prot.=$ARRAY_CODONES[1][$j];
                            break;
                        }
                    }
                }
                return $prot;
            }
            
            $input = '';
            $output1 = '';
            $output2 = '';
            $output3 = '';
            if(isset($_POST['submit'])){
                $input = $_POST['input'];
                if(!empty($input)){
                    $adn = preg_replace("/\s\s+/","",$input);   /* quita exceso de blank spaces y enters */
                    $adn = str_ireplace(" ", "", $adn);      /* quita espacio simples */
                    $adn = str_replace("\t","", $adn);       /* quita tabs */
                    $adn = strtoupper($adn);
                     if(check($adn,array('A','T','G','C'))){ /* validated input */
                        
                        $adn_frame1 = $adn;
                        $adn_frame2 = substr($adn, 1);
                        $adn_frame3 = substr($adn, 2);
                        
                        $output1 = adn2protein($adn_frame1);
                        $output2 = adn2protein($adn_frame2);
                        $output3 = adn2protein($adn_frame3);
                        
                     }
                     else
                         echo 'You must enter a valid ADN string';
                     
                }
                else
                    echo 'You forgot to write something!';
            }
        ?>
        
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            
            <label for="input">Input:</label><br/>
            <textarea id="input" name="input"><?php echo $input; ?></textarea><br/>
            
            <label for="output1">Frame 1:</label><br/>
            <div id="output1" name="output1" ><p><?php echo $output1; ?></p></div><br/>
            
            <label for="output2">Frame 2:</label><br/>
            <div id="output2" name="output2" ><p><?php echo $output2; ?></p></div><br/>
            
            <label for="output3">Frame 3:</label><br/>
            <div id="output3" name="output3" ><p><?php echo $output3; ?></p></div><br/>
            
            <input class="clearInput" type="button" value="" onclick="clr_input();" />
            <input class="translateInput" type="submit" value="" name="submit" />
                        
        </form>

    </body>
</html>