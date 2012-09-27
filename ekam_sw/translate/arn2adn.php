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
        
        <h2>RNA to DNA</h2>
        
        <?php
        
            require_once('utilities.php');
        
            $input = '';
            $output = '';
            if(isset($_POST['submit'])){
                $input = $_POST['input'];
                if(!empty($input)){
                    $arn = preg_replace("/\s\s+/","",$input);   /* quita exceso de blank spaces y enters */
                    $arn = str_ireplace(" ", "", $arn);      /* quita espacio simples */
                    $arn = str_replace("\t","", $arn);       /* quita tabs */
                    $arn = strtoupper($arn);
                     if(check($arn,array('A','U','G','C'))){ /* validated input */
                        $output = str_replace("U","T",$arn); /* ready to go */
                     }
                     else
                         echo 'You must enter a valid ARN string';
                     
                }
                else
                    echo 'You forgot to write something!';
            }
        ?>
        
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            
            <label for="input">Input:</label><br/>
            <textarea id="input" name="input"><?php echo $input; ?></textarea><br/>
            <label for="output">Output:</label><br/>
            <textarea id="output" name="output" readonly="readonly"><?php echo $output; ?></textarea><br/>
            <input class="clearInput" type="button" value="" onclick="clr_input();" />
            <input class="translateInput" type="submit" value="" name="submit" />
                        
        </form>

    </body>
</html>