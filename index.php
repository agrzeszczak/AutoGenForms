<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Form Demo</title>
    </head>
    <body>
        <h1>Admin Backend</h1>
        <?php
         include 'vendor/autoload.php';
         echo $alert;
        ?>
        <form class="form-inline" action="index.php">
            <?php 
                $tables = array(
                    'table1',
                    'table2'

            );
                $tablesToColumns = array(
                    'table1' => 'foregin_key_return'
                );
            ?>

            <select name="table">
                <?php
                foreach ($tables as $this_table) {

                     $display = ucwords(str_replace("_", " ", $this_table));
                     $selected = "";
                     if($_GET['table'] == $this_table) $selected = 'selected="selected"';
                    echo "<option value = \"$this_table\" $selected >$display</option>";

                }
                ?>
            </select>
            <input type="submit" value="Change Table" />
        </form>

        <?php

        if(in_array($_GET['table'], $tables)){
            
            //Create DB Connection
            $user="mydbtest";
            $password='letmein';
            $dbname="mydbtest";
            $mysqli = new mysqli('127.0.0.1', $user, $password, $dbname);
            if ($mysqli->connect_error) {
                die('Connect Error (' . $mysqli->connect_errno . ') '
                        . $mysqli->connect_error);
            }
            
            $exceptions = array('hidden_column');
            
            $form = new AutoGenForms\AdminForm($mysqli, $_GET['table'],$tablesToColumns, $exceptions);
            echo $form->displayForm();
            echo $form->javascript('api');
        }
        ?>
    </body>
</html>
