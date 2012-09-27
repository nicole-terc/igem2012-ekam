<?php
    require_once('authorize.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <title>Enzimes</title>
    </head>
    
    <body>
        
        <div id="logo">
            <img src="images/Igem.png" height="90px" alt="igem" />
        </div>
            
        <div id="centered_container" style="width:490px;">
            <p style="font-weight:bold; text-align: center;">Add enzime</p>
            <p style="text-align: center;">Please. Fill in <span style="color:red;">ALL</span> the blanks and be happy.</p>
            
            <?php
                $id = '';
                $name = '';
                $supplier = '';
                $sequence = '';
		$type = '';
                $buffer1 = '';
                $buffer2 = '';
                $buffer3 = '';
                $buffer4 = '';
                $buffers = '';
                $optimum_temperature = '';
                $heat_inactivation = '';
                $methylation_block = '';
                $star_activity = '';
                $uml = '';
                $price = '';
                $infos_source = '';
                if(isset($_POST['submit'])){
                    $id = $_POST['id'];
                    $name = $_POST['name'];
                    $supplier = $_POST['supplier'];
                    $sequence = $_POST['sequence'];
                    $type = $_POST['type'];
                    $buffer1 = $_POST['buffer1'];
                    $buffer2 = $_POST['buffer2'];
                    $buffer3 = $_POST['buffer3'];
                    $buffer4 = $_POST['buffer4'];
                    $buffers = $buffer1 . ',' . $buffer2 . ',' . $buffer3 . ',' . $buffer4;
                    $optimum_temperature = $_POST['optimum_temperature'];
                    $heat_inactivation = $_POST['heat_inactivation'];
                    $methylation_block = $_POST['methylation_block'];
                    $star_activity = $_POST['star_activity'];
                    $uml = $_POST['uml'];
                    $price = $_POST['price'];
                    $infos_source = $_POST['infos_source'];
                    
                    // All blanks filled?
                    if( !empty($name) && !empty($supplier) && !empty($sequence) && !empty($type)
                        && is_numeric($buffer1) && is_numeric($buffer2) && is_numeric($buffer3) && is_numeric($buffer4)
			&& !empty($optimum_temperature) && !empty($heat_inactivation) && !empty($star_activity)
                        && !empty($methylation_block) && !empty($infos_source) && !empty($uml) && !empty($price) ){
                            
                            // DB connection
                            $dbc = mysqli_connect(  'localhost',
                                                    'root',
                                                    '',
                                                    'igem')
                                    or die('Error connecting to database.');
                            
                            /* First check if ENZIME already exists */
                            if( isset($_POST['id']) && !empty($_POST['id'])){
                                // query exists?;
                                $query = "SELECT * FROM enzimas WHERE id=$id";
                                $res = mysqli_query($dbc,$query)
                                        or die('Error querying database.');
                                if(mysqli_num_rows($res)){
                                    // modify
                                    $sequence_wo_cut = str_replace("|","",$sequence);
                                    $query = "UPDATE enzimas SET nombre='$name', proveedor='$supplier', secuencia='$sequence', secuencia_sin_corte='$sequence_wo_cut', " . 
                                            "tipo='$type', buffers='$buffers', temperatura_optima='$optimum_temperature', " . 
                                            "temperatura_inactivacion='$heat_inactivation', bloqueo_metilacion='$methylation_block', " . 
                                            "star_activity='$star_activity', fuente='$infos_source', uml='$uml', precio='$price' WHERE id=$id";
                                    mysqli_query($dbc,$query)
                                        or die('Error querying database.');
                                    mysqli_close($dbc);
                                    
                                    /* Confirmation for edition */
                                    echo '<p style="text-align: center; font-size: 80%; font-style: italic; color: green;">EDITED. Share this joy.</p>';
                                    $name = '';
                                    $supplier = '';
                                    $sequence = '';
                                    $type = '';
                                    $buffer1 = '';
                                    $buffer2 = '';
                                    $buffer3 = '';
                                    $buffer4 = '';
                                    $buffers = '';
                                    $optimum_temperature = '';
                                    $heat_inactivation = '';
                                    $methylation_block = '';
                                    $star_activity = '';
                                    $infos_source = '';
                                    $uml = '';
                                    $price = '';
                                    
                                }
                                else { /* it should always exist*/
                                    echo '<h1 style="font-size:500%;color:red;text-align:center;">Dont mess with me bitch!</h1>';
                                    mysqli_close($dbc);
                                    return true;
                                }
                                
                            }
                            else{
                                $sequence_wo_cut = str_replace("|","",$sequence);
                                $query = "INSERT INTO enzimas (nombre,proveedor,secuencia,secuencia_sin_corte,tipo,buffers,temperatura_optima,temperatura_inactivacion,bloqueo_metilacion,star_activity,fuente,uml,precio) " .
                                        "VALUES ('$name','$supplier','$sequence','$sequence_wo_cut','$type','$buffers','$optimum_temperature','$heat_inactivation','$methylation_block','$star_activity','$infos_source','$uml','$price')";
                                mysqli_query($dbc,$query)
                                        or die('Error querying database.');
                                
                                mysqli_close($dbc);

                                // confirmation message add to DB
                                echo '<p style="text-align: center; font-size: 80%; font-style: italic; color: green;">Share this joy. One enzime added.</p>';
                                $name = '';
                                $supplier = '';
                                $sequence = '';
                                $type = '';
                                $buffer1 = '';
                                $buffer2 = '';
                                $buffer3 = '';
                                $buffer4 = '';
                                $buffers = '';
                                $optimum_temperature = '';
                                $heat_inactivation = '';
                                $methylation_block = '';
                                $star_activity = '';
                                $infos_source = '';
                                $uml = '';
                                $price = '';
                            }
                            
                    }
                    else{ // please fill them
                        echo '<p style="text-align: center; font-size: 80%; font-style: italic; color: red;">To be happy you must fill in ALL the blanks.</p>';
                    }
                    
                }
                else if( isset($_GET['id']) && isset($_GET['name']) && isset($_GET['supplier']) && isset($_GET['sequence']) && isset($_GET['type'])
                         && isset($_GET['buffers']) && isset($_GET['optimum_temperature']) && isset($_GET['heat_inactivation']) && isset($_GET['methylation_block'])
                         && isset($_GET['star_activity']) && isset($_GET['infos_source']) && isset($_GET['uml']) && isset($_GET['price']) ){
                    $id = $_GET['id'];
                    $name = $_GET['name'];
                    $supplier = $_GET['supplier'];
                    $sequence = $_GET['sequence'];
                    $type = $_GET['type'];
                    $aux = explode(",",$_GET['buffers']);
                    $buffer1 = $aux[0];
                    $buffer2 = $aux[1];
                    $buffer3 = $aux[2];
                    $buffer4 = $aux[3];
                    $optimum_temperature = $_GET['optimum_temperature'];
                    $heat_inactivation = $_GET['heat_inactivation'];
                    $methylation_block = $_GET['methylation_block'];
                    $star_activity = $_GET['star_activity'];
                    $infos_source = $_GET['infos_source'];
                    $uml = $_GET['uml'];
                    $price = $_GET['price'];
                    
                    if( !empty($_GET['id']) && !empty($_GET['name']) && !empty($_GET['supplier']) && !empty($_GET['sequence']) && !empty($_GET['type'])
                         && !empty($_GET['buffers']) && !empty($_GET['optimum_temperature']) && !empty($_GET['heat_inactivation']) && !empty($_GET['methylation_block'])
                         && !empty($_GET['star_activity']) && !empty($_GET['infos_source']) ){
                        echo '<p style="text-align: center; font-size: 80%; font-style: italic; color: purple;">Ready for edition.</p>';
                    }
                    else{
                        echo 'NOT A VALID ENZIME.';
                        return true;
                    }
                }
                else{ // wont do it by itself
                    echo '<p style="text-align: center; font-size: 80%; font-style: italic;">This Database won\'t gather that info by itself.</p>';
                }
            
            ?>
            
            <div id="form_container">
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input style="display:none;" type="text" id="id" name="id" value="<?php echo $id;?>" /><br/>
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo $name;?>" /><br/>
                    <label for="supplier">Supplier:</label>
                    <input type="text" id="supplier" name="supplier" value="<?php echo $supplier;?>" /><br/>
                    <label for="sequence">Sequence with Cut:</label>
                    <input type="text" id="sequence" name="sequence" value="<?php echo $sequence;?>" /><br/>
					
                    <label for="type">Enzime Type:</label>
                    <?php
                        switch($type){
                            case 'blunt':
                                echo 'Blunt <input id="type" name="type" type="radio" value="blunt" checked="checked" />';
                                echo 'Sticky <input id="type" name="type" type="radio" value="sticky" />';
                                break;
                            default:
                                echo 'Blunt <input id="type" name="type" type="radio" value="blunt" />';
                                echo 'Sticky <input id="type" name="type" type="radio" value="sticky" checked="checked" />';
                                break;
                        }
                    ?>
					
                    <label for="buffer1">Buffer 1:</label>
                    <input type="text" id="buffer1" name="buffer1" value="<?php echo $buffer1;?>" />&nbsp;%<br/>
                    <label for="buffer2">Buffer 2:</label>
                    <input type="text" id="buffer2" name="buffer2" value="<?php echo $buffer2;?>" />&nbsp;%<br/>
                    <label for="buffer3">Buffer 3:</label>
                    <input type="text" id="buffer3" name="buffer3" value="<?php echo $buffer3;?>" />&nbsp;%<br/>
                    <label for="buffer4">Buffer 4:</label>
                    <input type="text" id="buffer4" name="buffer4" value="<?php echo $buffer4;?>" />&nbsp;%<br/>
                    <label for="optimum_temperature">Optimum Temperature:</label>
                    <input type="text" id="optimum_temperature" name="optimum_temperature" value="<?php echo $optimum_temperature;?>" />&nbsp;°C<br/>
                    <label for="heat_inactivation">Heat Inactivation:</label>
                    <input type="text" id="heat_inactivation" name="heat_inactivation" value="<?php echo $heat_inactivation;?>" />&nbsp;°C<br/>
                    <label for="methylation_block">Methylation Block:</label>
					
                    <?php
                        switch($methylation_block){
                            case 'dam':
                                echo 'dam <input id="methylation_block" name="methylation_block" type="radio" value="dam" checked="checked" />';
                                echo 'dcm <input id="methylation_block" name="methylation_block" type="radio" value="dcm" />';
                                echo 'No <input id="methylation_block" name="methylation_block" type="radio" value="no" /><br/>';
                                break;
                            case 'dcm':
                                echo 'dam <input id="methylation_block" name="methylation_block" type="radio" value="dam" />';
                                echo 'dcm <input id="methylation_block" name="methylation_block" type="radio" value="dcm" checked="checked" />';
                                echo 'No <input id="methylation_block" name="methylation_block" type="radio" value="no" /><br/>';
                                break;
                            default:
                                echo 'dam <input id="methylation_block" name="methylation_block" type="radio" value="dam" />';
                                echo 'dcm <input id="methylation_block" name="methylation_block" type="radio" value="dcm" />';
                                echo 'No <input id="methylation_block" name="methylation_block" type="radio" value="no" checked="checked" /><br/>';
                                break;
                        }
                    ?>
					
                    <label for="star_activity">Star Activity:</label>
                    
                    <?php
                        switch($star_activity){
                            case 'yes':
                                echo 'Yes <input id="star_activity" name="star_activity" type="radio" value="yes" checked="checked" />';
                                echo 'No <input id="star_activity" name="star_activity" type="radio" value="no" />';
                                break;
                            default:
                                echo 'Yes <input id="star_activity" name="star_activity" type="radio" value="yes" />';
                                echo 'No <input id="star_activity" name="star_activity" type="radio" value="no" checked="checked" />';
                                break;
                        }
                    ?>
                    
                    <label for="uml">U/ml:</label>
                    <input type="text" id="uml" name="uml" value="<?php echo $uml;?>" />U/ml<br/>
                    <label for="price">Price:</label>
                    <input type="text" id="price" name="price" value="<?php echo $price;?>" /><br/>
                    <label for="infos_source">Info's Source:</label>
                    <input type="text" id="infos_source" name="infos_source" value="<?php echo $infos_source;?>" /><br/>
                    <p style="text-align:right;margin:0 38px 0 0;"><input type="submit" name="submit" value="Add" id="submit"/></p>
                </form>
            </div>
            
        </div>
        
        <img id="gear_trans" src="images/gear_trans_cut.png" alt="gear" />
        <img id="gear_trans_small" src="images/gear_trans.png" alt="gear_small" />
        <img id="pet" src="images/pet.png" alt="pet" />
        <div id="purple_button"><a href="manage_data.php" target="_self">Manage Data</a></div>
        
    </body>
    
</html>
