<?php
session_start();
require ('vendor/autoload.php');
$edit = false;
$id = $fname = $lname = $dob = $city = "";
    $mongoManager = new MongoDB\Client();
    $db = $mongoManager->test_database;
    $collection = $db->user_dtl;
    // $filter  = ['city'=>['$in'=> array('Bhy')]];
    $filter  = [];
    $options = ['sort' => ['_id' => 1]];
    $get = $collection->find($filter,$options);
function getEditData($collection, $id)
{
    $options = [];
    $edit_content = $collection->findOne(array('_id'=> new MongoDB\BSON\ObjectID($id)));
    return $edit_content;
}
if (isset($_POST['insert'])) {
    $_SESSION['message'] = "Record Inserted!";
    $insert_data = ['_id' => new MongoDB\BSON\ObjectID, 'first_name' => $_POST['fname'], 'last_name' => $_POST['lname'],'dob'=> date($_POST['dob']) , 'city' => $_POST['city'] ];
    $collection->insertOne($insert_data);
    header('location:/php_mongodb_crud');
    exit;
}

if (isset($_POST['update'])) {
    $_SESSION['message'] = "Record Updated!";
    $new_data = array('$set' => array('first_name' => $_POST['fname'], 'last_name' => $_POST['lname'],'dob'=> date($_POST['dob']) , 'city' => $_POST['city']));
    $condition = array("_id" => new MongoDB\BSON\ObjectID($_GET['edit']));
    $collection->updateOne($condition, $new_data);
    header('location:index.php');
    exit;
}
if (isset($_GET['edit'])) {
    $edit = true;
    $userRecord = getEditData($collection, $_GET['edit']);
    $fname = $userRecord['first_name'];
    $lname = $userRecord['last_name'];
    $dob = $userRecord['dob'];
    $city = $userRecord['city'];
}
if (isset($_GET['delete'])) {
    // var_dump($_GET);
    $_SESSION['message'] = "Record Deleted!";
    $collection->deleteOne(['_id'=> new MongoDB\BSON\ObjectID($_GET['delete'])]);
    header('location:index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Php Mysql Crud App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
</head>
<body>
    <section class="form-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h3>PHP Crud App 2021</h3>
                </div>
                <div class="col-lg-6 m-auto">
                    <?php if (isset($_SESSION['message'])) { ?>
                        <div class="alert alert-primary" role="alert">
                            <?php
                            echo $_SESSION['message'];
                            unset($_SESSION['message']); ?>
                        </div>
                    <?php } ?>

                    <form action="" method="post">
                        <div class="form-group">
                            <label for="">First Name</label>
                            <input type="text" class="form-control" name="fname" id="fanme" value="<?php echo $fname; ?>">
                        </div>
                        <div class="form-group">
                            <label for="">Last Name</label>
                            <input type="text" class="form-control" name="lname" id="lanme" value="<?php echo $lname; ?>">
                        </div>
                        <div class="form-group">
                            <label for="">DOB</label>
                            <input type="date" class="form-control" name="dob" id="dob" value="<?php echo $dob; ?>">
                        </div>
                        <div class="form-group">
                            <label for="">City</label>
                            <input type="text" class="form-control" name="city" id="city" value="<?php echo $city; ?>">
                        </div>
                        <div class="form-group mt-3">
                            <?php if ($edit) { ?>
                                <input type="submit" name="update" class="btn btn-success" value="Update">
                            <?php } else { ?>
                                <input type="submit" name="insert" class="btn btn-success" value="Submit">
                            <?php } ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <section class="table-display mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table" id="showMongoDBData">
                            <thead>
                                <th>Sr.no</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>DOB</th>
                                <th>City</th>
                                <th>Actions</th>
                            </thead>
                            <tbody>
                                <?php $srID = 1; foreach($get as $row) { //var_dump($row); ?>
                                    <tr>
                                        <td><?php echo $srID; ?></td>
                                        <td><?php echo $row['first_name']; ?></td>
                                        <td><?php echo $row['last_name']; ?></td>
                                        <td><?php echo $row['dob']; ?></td>
                                        <td><?php echo $row['city']; ?></td>
                                        <td><a href="?edit=<?php echo $row->_id; ?>" class="btn btn-primary">Edit</a> <a href="?delete=<?php echo $row->_id; ?>" class="btn btn-danger">Delete</a></td>
                                    </tr>
                                <?php $srID++; } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
<script>
$(document).ready( function () {
    $('#showMongoDBData').DataTable();
});
</script>