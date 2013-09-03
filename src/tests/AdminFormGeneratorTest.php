<?php

/**
 * Description of AdminFormGeneratorTest
 *
 * @author Andrew Grzesczcak <agrzeszczak@andrewgrz.com>
 */

class AdminFormGeneratorTest extends PHPUnit_Framework_TestCase {
    
    /**
     *
     * @var DatabaseConnection
     */
    private $mysqli;
    
    public function setUp() 
    {
        //Create DB Connection
        $user="mydbtest";
        $password='letmein';
        $dbname="mydbtest";
        $this->mysqli = new mysqli('127.0.0.1', $user, $password, $dbname);
    }

    public function tearDown() 
    {
        //close out the connection for the next test
        $this->mysqli->close();
    }
    
    public function testConstructSuccesses()
    {
        $form = new \AutoGenForms\AdminForm($this->mysqli, 'testTable');
        $this->assertInstanceOf('\AutoGenForms\AdminForm', $form);
        
        $form = new \AutoGenForms\AdminForm($this->mysqli, 'testTable', array('table' => 'columns'));
        $this->assertInstanceOf('\AutoGenForms\AdminForm', $form);
        
        $form = new \AutoGenForms\AdminForm($this->mysqli, 'testTable', array('table' => 'columns'), array('column1', 'column2'));
        $this->assertInstanceOf('\AutoGenForms\AdminForm', $form);
    }
    
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage No Mysqli Database Connection Class Provided.
     */
    public function testExceptionNoConnection ()
    {
        $form = new \AutoGenForms\AdminForm('BadStringConnection', 'testTable');
    }
    

    public function testGetColumns ()
    {
        $form = new \AutoGenForms\AdminForm($this->mysqli, 'table1');
        $columns = $form->getColumns();
        $this->assertTrue(is_array($columns), 'Get Columns is not returning an array');
        $this->assertEquals(5, count($columns));
        $this->assertEquals('id_table1', $columns[0]['field']);
    }
    
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Table Does Not Exist
     */
    public function testGetColumnBadTable ()
    {
        $form = new \AutoGenForms\AdminForm($this->mysqli, 'bad_table');
        $form->getColumns();
    }
    
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Table Does Not Have Forgein Key in $tablesToColumns.
     */
    public function testBadtableToColumns ()
    {
        $form = new \AutoGenForms\AdminForm($this->mysqli, 'table2');
        $form->displayForm();
    }
    
    public function testDisplayForm ()
    {
        $tablesToColumns = array(
            'table1' => 'foregin_key_return'
        );
         $form = new \AutoGenForms\AdminForm($this->mysqli, 'table1', $tablesToColumns);
         $output = $form->displayForm();
         $this->assertTrue(is_string($output));
         $this->assertFalse(strlen($output) == 0);
    }
    
    public function testjavascript ()
    {
         $form = new \AutoGenForms\AdminForm($this->mysqli, 'table1');
         $output = $form->javascript();
         $this->assertTrue(is_string($output));
         $this->assertFalse(strlen($output) == 0);
    }
    
}
