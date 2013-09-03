<?php
namespace AutoGenForms;

/**
 * This class generates the form.
 *
 * @author Andrew Grzesczcak <agrzeszczak@andrewgrz.com>
 */
class AdminForm {

    /**
     * The Table that we are pulling from.
     * 
     * @var string 
     */
    protected $table;
    
    /**
     * The list of the columns that we are excluding.
     * 
     * @var array 
     */
    protected $exceptions;
    
    /**
     * The list of forgein key names
     * 
     * @var array 
     */
    protected $tablesToColumns;

    /**
     * @var MySqli database connection string.
     */
    protected $mysqli;


    /**
     * Create a new instance of the class.
     * 
     * @param DatabaseConnection $mysqli Required: The mysqli database connection string
     * @param string $table Required: The string of the table to get the structure from
     * @param array $tablesToColumns Optional: The columns to show for forgein key relations
     * @param array $exceptions Optional: The column names to hide
     * 
     */
    public function __construct($mysqli, $table, $tablesToColumns = array(), $exceptions = array()) 
    {
        //check for string 
        if(!is_object($mysqli) OR get_class($mysqli) != 'mysqli') {
            //Echo out the error if $mysqli is not a connection
            throw new \Exception('No Mysqli Database Connection Class Provided.');
        }
        else {
            if (!$mysqli->ping()) {
                //Echo out the error if $mysqli is not a connection
                throw new \Exception('Database Connection Failure.');
            }
            else {
                
                //asign the values to the class variables
                $this->mysqli = $mysqli;

                //Check to see if $table is a string
                if(is_string($table)) $this->table = $table;
                else throw new \Exception('$table is not a string');

                //Check to see if $tablesToColumns is an array
                if(is_array($tablesToColumns)) $this->tablesToColumns = $tablesToColumns;
                else throw new \Exception('$tablesToColumns is not an array.');

                //Check to see if $exceptions is an array
                if(is_array($exceptions))$this->exceptions = $exceptions;
                else throw new \Exception('$exceptions is not an array.');
                
            }
        }
    }

    /**
     * Get a list of the columns in the database with their info.
     * 
     * @return array The fields of the table
     */
    public function getColumns () 
    {
        //Set the return to an array. 
        $fields = array();
        
        $query = "DESCRIBE `$this->table`";
        
        $stmt = $this->mysqli->prepare($query);
       
        $stmt -> execute();
        $stmt -> bind_result($field, $type, $null, $key, $default, $extra);
        
        while ($stmt->fetch()){
            $fields[]=array(
                'field' => $field, 
                'type' => $type,
                'null' => $null, 
                'key' => $key, 
                'default' => $default, 
                'extra' => $extra);
        }
        
        $stmt->close();
        
        if(empty($fields)) {
            throw new \Exception('Table Does Not Exist.');
        }
        
        return $fields;

    }
    
    private function generateSelectForForgeinKey ($table) {
        
        $mysqli = $this->mysqli;
        $tablesToColumns = $this->tablesToColumns;
        
        //we need to make sure there is an entry in $tablesToColumns for the table
        if(array_key_exists($table, $tablesToColumns)) {
            
            $first = true;
            $i=0;
            //We provided a table column
            if(is_array($tablesToColumns[$table])) {

                foreach ($tablesToColumns[$table] as $column){

                    //skip the commas if we are not on the first one
                    if($first === false) {
                        $columnList.=", ";
                        $binds .= ', ';
                    }
                    $columnList.= "`$column`";
                    $binds .= '$v'.$i;
                    $first = false;

                    $i++;
                }

            }

            else {
                $columnList.= '`'.$tablesToColumns[$table].'`';
                $binds .= '$vi';
            }

            $query = "SELECT `id_$table`, $columnList FROM `$table`
                    ORDER BY $columnList";

            $stmt = $mysqli->prepare($query);
            $stmt->execute();
            $eval = '$stmt->bind_result($id, '.$binds.');';
            eval($eval);
            $input = '<select name="'.$table.'_id" id="'.$table.'_id">';
            while($stmt->fetch()){
                $eval = '$input.="<option value=\"$id\">'.$binds.'</option>";';
                eval($eval);
            }

            $input .= "</select>";

            return $input;
            
        }
        else {
            throw new \Exception('Table Does Not Have Forgein Key in $tablesToColumns.');
        }
    }
    
    private function generateSelectForPrimaryKey ($table) {
        
        $mysqli = $this->mysqli;
        
        $first = true;
        $i=0;
        
        $query = "SELECT `id_$table` FROM `$table` ORDER BY `id_$table` ";

        $stmt = $mysqli->prepare($query);
        $stmt->execute();
        $stmt->bind_result($id);
        $input = "<select name='id' id ='id'>";
        $input.="<option value='new'>Create New</option>";
        while($stmt->fetch()){
            $input.="<option>$id</option>";
        }
        
        $input .= "</select>";
        
        return $input;
        
    }
    /**
     * Outputs the form with the supplied info.
     * 
     * @return string The output from the script
     */
    public function displayForm(){
        
        $output = "";
        
        $output .= "<form class=\"form-horizontal\" method=\"POST\">";
        $output .= "<input type = \"hidden\" name = \"table\" value = \"$this->table\" />";
        $header = ucwords($this->table);
        
        $output .= "<h2>Table: $header</h2>";
        
        $columns = $this->getColumns();
        
        foreach($columns as $val){
            
            if(!in_array($val['field'], $this->exceptions)){
                
                if($val['type'] == "enum('0','1')") $input = $this->makeBool($val['field']);
                elseif(strpos($val['type'], 'enum(') !== false) $input = $this->makeEnum($val['field'], $val['type']);
                
                if(strpos ($val['type'], "varchar") !== false) {
                    $input = '<input type = "text" name = "'.$val['field'].'" id = "'.$val['field'].'"/><br />';
                }
                
                if(strpos ($val['type'], "decimal") !== false) {
                    $input = '<input type = "tel" name = "'.$val['field'].'" id = "'.$val['field'].'"/><br />';
                }
                
                if(strpos ($val['type'], "int") !== false) {
                    $input = '<input type = "tel" name = "'.$val['field'].'" id = "'.$val['field'].'"/><br />';
                }
                
                if(strpos ($val['type'], "datetime") !== false) {
                    $input = '<input type = "datetime" name = "'.$val['field'].'" /><br />';
                }
                
                elseif(strpos ($val['type'], "date") !== false) {
                    $input = '<input type = "date" name = "'.$val['field'].'" /><br />';
                }
                   
                //Foreign key
                if($val['key'] == "MUL"){
                    $table =  str_replace("_id", "", $val['field']);
                    $input = $this->generateSelectForForgeinKey($table);
                }
                
                //Primary key
                if($val['key'] == "PRI"){
                    $table =  str_replace("id_", "", $val['field']);
                    $input = $this->generateSelectForPrimaryKey($table);
                }

                //Pretty Up The Field
                $field = ucwords(str_replace("_", " ", $val['field']));
                
                $output .= '<div class="control-group">
                    <label class="control-label">'.$field.'</label>
                    <div class="controls">
                      '.$input.'
                    </div>
                 </div>';
            }
        }
        $output.= '<div class="control-group">
            <div class="controls">
                <input type="submit" value="Save Changes" class="btn" id="submit"/>
            </div>
         </div>
        </form>';
        
        return $output;
    }
    
    /**
     * Handles a dropdown for columns with enum('0','1').
     * Makes them a yes or no
     * 
     * @param string $columnname
     * @param strin $currentValue
     * @return string $input line
     */
    private function makeBool ($columnname, $currentValue="1") {
        
        $input = $val['field']."<select name=\"$columnname\" id = \"$columnname\">";
        
        if($currentValue == "1"){
            $input .= "<option value='1'>Yes</option>";
            $input .= "<option value='0'>No</option>";
        }
        
        else {
            $input .= "<option value='0'>No</option>";
            $input .= "<option value='1'>Yes</option>";
        }
        
        $input .= "<select>";
        
        return $input;
        
    }
    
    /**
     * Handles a dropdown for columns with enum
     * Returns a select with the list of options
     * 
     * @param string $columnname
     * @param strin $currentValue
     * @return string $input line
     */
    private function makeEnum($columnname, $type){

        $input = $val['field']."<select name=\"$columnname\" id = \"$columnname\">";
        
        if(preg_match_all("/\'(.*?)\'/",$type,$match)) {            
            foreach($match[1] as $enum){
                $display = ucwords($enum);
                $input .= "<option value='$enum'>$display</option>";
            }
        }
        
        $input .= "<select>";
        
        return $input;
        
    }
    
    /**
     * Creates a string of jQuery based on the supplied data
     * 
     * @return string The jQuery in the script tags
     */
    public function javascript($callbackUrl){

        $output = "<script>";
        $output .= "$('#id').change(function() {
            var id = $('#id').val();
            ";
        $output .= 'var xmlhttp;
    xmlhttp=new XMLHttpRequest();

    xmlhttp.onreadystatechange=function(){

      if (xmlhttp.readyState===4 && xmlhttp.status===200){

            var reply=xmlhttp.responseText;  
            
            var parsed = $.parseJSON(reply);
            ';
        $output .= $this->columnsToVariables();
        $output .= '
            
        }
        
    }
    xmlhttp.open("GET","'.$callbackUrl.'",true);
    xmlhttp.send();';
        
        $output .= "});</script>";
        
        return $output;
        
    }
    
    /**
     * Creates the variable names for the columns so the rows can be updated when info is pulled.
     * 
     * @return string The variables
     */
    private function columnsToVariables(){
        
        $columns = $this->getColumns();
        
        $output = "";
        
        foreach ($columns as $data){
            
            $column = $data['field'];
            
            $output .= "$('#$column').val(parsed.$column);
";
            
        }
        return $output;
    }

}
