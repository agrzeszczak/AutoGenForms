# AutoGenForms
An auto generating form system based off of the database tables.
It does not correctly handle the saving of the data. Just the front end display of the information.

## Warning
This is still in development. Use at your own risk!

## Installation
Include the AdminForm.php file under src into your directory.
There is a testTable.sql under the test directory for testing out the form.

## Dependencies
- jQuery for the javascript
- PHP 5.3.0 for namespaces

## Usage
$mysqli is the mysqli database connection.
$table is the table name in the database we are looking for.
$tablesToColumns is an array of the columns to show for forgein key relations.
$exceptions is an array of the column names to hide.

    $form = new AutoGenForms\AdminForm($mysqli, $table,$tablesToColumns, $exceptions);
    echo $form->displayForm();
    echo $form->javascript('someUrl');

## TODOS
- Template the output in displayForm().
- Make exceptions on a table basis.
- Flush out tests for the actual display.
