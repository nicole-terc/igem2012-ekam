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
            <img src="images/Igem.png" height="110px" alt="igem" />
        </div>
            
        <div id="centered_container" style="width:875px;">
            <p style="font-weight:bold; text-align: center;">Manage - Enzimes.</p>
            <p style="text-align: center;">Here you can delete & visualize the <span style="color:red;">WHOLE</span> Database.</p>
            <p style="text-align: center;">Check the item's box you want to delete.</p>
        
            <div id="form_container2">

                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                <?php
                    $dbc = mysqli_connect(  'localhost',
                                            'root',
                                            '',
                                            'igem')
                            or die('Error connecting to MySQL server.');

                    //Delete the customer rows (only if the form has been submitted)
                    if(isset($_POST['submit'])){
                        if(isset($_POST['todelete'])){
                            foreach ($_POST['todelete'] as $delete_id){
                                $query = "DELETE FROM enzimas WHERE id=$delete_id";
                                mysqli_query($dbc,$query)
                                    or die('Error querying database.');
                            }
                            echo '<p style="text-align: center; font-size: 80%; font-style: italic; color:green;">Removed.</p>';
                        }
                        else{
                            echo '<p style="text-align: center; font-size: 80%; font-style: italic; color:red;">Please select at least one entry to delete.</p>';
                        }
                    }
                    else
                        echo '<p style="text-align: center; font-size: 80%; font-style: italic;">Impossible to edit. One must delete the whole entry and then re-add it.</p>';

                    //Display the customer rows with checkboxes for deleting
                    ?>
                    <table>
                        <tbody>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Name</th>
                                <th>Supplier</th>
                                <th>Cut</th>
                                <th>Sequence</th>
                                <th>Type</th>
                                <th>Buffers</th>
                                <th>Optimum Temperature</th>
                                <th>Heat Inactivation</th>
                                <th>Methylation Block</th>
                                <th>Star Activity</th>
                                <th>U/ml</th>
                                <th>Price</th>
                                <th>Info Source</th>
                                <th>&nbsp;</th>
                            </tr>
                    <?php
                    $query = "SELECT * FROM enzimas ORDER BY nombre";
                    $result = mysqli_query($dbc,$query);
                    while($row = mysqli_fetch_array($result)){
                        echo '<tr><td><div><p><input type="checkbox" value="' . $row['id'] . '" name="todelete[]" /></div></p></td>';
                        echo '<td><div><p>' . $row['nombre'] . '</p></div></td>';
                        echo '<td><div><p>' . $row['proveedor'] . '</p></div></td>';
                        echo '<td><div><p>' . $row['secuencia'] . '</p></div></td>';
                        echo '<td><div><p>' . $row['secuencia_sin_corte'] . '</p></div></td>';
                        echo '<td><div><p>' . $row['tipo'] . '</p></div></td>';
                        echo '<td><div><p>' . $row['buffers'] . '</p></div></td>';
                        echo '<td><div><p>' . $row['temperatura_optima'] . '</p></div></td>';
                        echo '<td><div><p>' . $row['temperatura_inactivacion'] . '</p></div></td>';
                        echo '<td><div><p>' . $row['bloqueo_metilacion'] . '</p></div></td>';
                        echo '<td><div><p>' . $row['star_activity'] . '</p></div></td>';
                        echo '<td><div><p>' . $row['uml'] . '</p></div></td>';
                        echo '<td><div><p>' . $row['precio'] . '</p></div></td>';
                        echo '<td><div><p><a href="' . $row['fuente'] . '" target="_blank">' . $row['fuente'] . '</a></p></div></td>';
                        echo '<td><div><p><a href="index.php?id='.$row['id'].'&amp;name='.$row['nombre'].'&amp;supplier='.$row['proveedor'].
                                '&amp;sequence='.$row['secuencia'].'&amp;sequence_wo_cut='.$row['secuencia_sin_corte'].'&amp;type='.$row['tipo'].'&amp;buffers='.$row['buffers'].
                                '&amp;optimum_temperature='.$row['temperatura_optima'].'&amp;heat_inactivation='.$row['temperatura_inactivacion'].
                                '&amp;methylation_block='.$row['bloqueo_metilacion'].'&amp;star_activity='.$row['star_activity'].
                                '&amp;uml='.$row['uml'].'&amp;price='.$row['precio'].'&amp;infos_source='.$row['fuente'].
                                '">Edit</a></p></div></td>';
                        echo '</tr>';
                        
                    }
                    
                    echo '</tbody></table>';
                    
                    mysqli_close($dbc);
                ?>
                <input type="submit" name="submit" value="Remove" />
                </form>

            </div>
        </div>
        
        <img id="gear_trans" src="images/gear_trans_cut.png" alt="gear" />
        <img id="gear_trans_small" src="images/gear_trans.png" alt="gear_small" />
        <img id="pet" src="images/pet.png" alt="pet" />
        <div id="purple_button"><a href="index.php" target="_self">Add Enzime</a></div>
        
    </body>
    
</html>
