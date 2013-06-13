<?php

ini_set('display_errors', 1);
?>
<a href="phpinfo.php"> hello</a>
<?php
require_once("/home/weboniselab/projects/apps2013/php/testApp/DbWrapper/DbWrapper.php");


$conn = DbWrapper::getInstance();
if (!empty($_POST['test_case'])) {
    switch ($_POST['test_case']) {
        case 1:
            $results = $conn->select('*')->from(array('organisations'))->result();
            getResults($results);
            break;
        case 2:
            $results = $conn->select('*')->from(array('organisations'))->where(array('id> ' => '10'))->limit(10)->result();
            getResults($results);
            break;
        case 3:
            $results = $conn->select('*')->from(array('organisations'))->where(array('AND' => array('id > ' => '10', 'id <=' => '50')))->result();
            getResults($results);
            break;

        case 4:
            $results = $conn->select('*')->from(array('organisations'))->where(array('created > ' => "'2013-02-10 00:00:00'"))->result();
            getResults($results);
            break;
        case 5:
            $results = $conn->select('*')->from(array('organisations'))->where(array('between' => array('id', '10', '50')))
                ->orderBy('name', 'DESC')->result();
            getResults($results);
            break;
        case 6:
            $results = $conn->select('*')->from(array('organisations'))->where(array('id=' => '2'))->result();
            getResults($results);
            break;
        case 7:
            $results = $conn->select(array('organisations.name', 'count(users.id)'))->from(array('organisations', 'users'))
                ->where(array('organisations.id=' => 'users.organisation_id'))->groupBy('organisations.name')->result();

            getResults($results);
            break;
        case 8:
            $results = $conn->select('*')->from(array('organisations'))->where(array('name=' => "'Financial Ombudsman'"))->result();
            getResults($results);
            break;
        case 9:
            $results = $conn->select('*')->from(array('users'))->where(array('organisation_id=' => "6"))->result();
            getResults($results);
            break;
        default:
            echo "Sorry You entered wrong number";
            break;
    }

}


/*print_r($_POST);
die();


echo 'List 10 organization whose id is greater than 10<br><br>';
$results = $conn->select('*')->from(array('organisations'))->where(array('id> ' => '10'))->limit(10)->result();
getResults($results);
echo "</br></br>";

echo 'List Organization whose id is greater than 10 and less than equal to 50</br></br>';
$results = $conn->select('*')->from(array('organisations'))->where(array('id > ' => '10', 'id <=' => '50'))->result();
getResults($results); //
echo "</br></br>";

echo 'List all organization who has bee created after 2013-02-10 00:00:00</br></br>';
$results = $conn->select('*')->from(array('organisations'))->where(array('created > ' => "'2013-02-10 00:00:00'"))->result();
getResults($results);
echo "</br></br>";

echo 'List all organisations who has id between 10 to 50 and its order should be descending by name</br></br>';
$results = $conn->select('*')->from(array('organisations'))->where(array('between' => array('id', '10', '50')))
    ->orderBy('name', 'DESC')->result();
getResults($results);
echo "</br></br>";


echo 'display information about organization whose id is 2</br></br>';
$results = $conn->select('*')->from(array('organisations'))->where(array('id=' => '2'))->result();
getResults($results);
echo "</br></br>";

echo 'return a count of users per organization with organization name</br></br>';
$results = $conn->select(array('organisations.name', 'count(users.id)'))->from(array('organisations', 'users'))
    ->where(array('organisations.id=' => 'users.organisation_id'))->groupBy('organisations.name')->result();

getResults($results);
echo "</br></br>";

echo 'display information about organization whose name is "Org Name 30"</br></br>';
$results = $conn->select('*')->from(array('organisations'))->where(array('name=' => "'Financial Ombudsman'"))->result();
getResults($results);

echo "</br></br>";
echo 'Display all the users of organization_id 30</br></br>';
$results = $conn->select('*')->from(array('users'))->where(array('organisation_id=' => "6"))->result();
getResults($results);
echo "</br></br>";*/

function getResults($results) {
    if (isset($results) && !empty($results)) {

        ?>
    <table>


        <?php
        foreach ($results as $result) {
            ?>
            <tr>
                <?php
                foreach ($result as $fields) {
                    echo "<td> $fields </td>";
                }
                ?>

            </tr>
            <?php

        }
        ?>
    </table>

    <?php
    }
}

?>
