<?php 

include "../includes/config.php"; 

session_start();

$method = $_POST['method'];

if(function_exists($method)){ 
    call_user_func($method);
}
else{
    echo "Function not exists";
}

function fnSave(){
    global $con;
    $username = $_POST['username'];
    $fullname = $_POST['fullname'];
    $password = md5($_POST['password']);
    $address = $_POST['address'];
    $email = $_POST['email'];
    $userid = $_POST['userid'];

    $query = $con->prepare('call sp_reg(?,?,?,?,?,?)');
    $query->bind_param('isssss',$userid,$fullname,$username,$password,$address,$email);
    
    if($query->execute()){
        echo 1;
    }
    else{
        echo json_encode(mysqli_error($con));
    }

}

function fnGetUsers(){
    global $con;
    $userid = $_POST['userid'];
    if($userid == 0){
        $query = $con->prepare("SELECT * FROM users");
    }
    else{
        $query = $con->prepare("SELECT * FROM users where userid = $userid");
    }
    
    $query->execute();
    $result = $query->get_result();
    $data = array();
    while($row = $result->fetch_array()){
        $data[] = $row;
    }

    echo json_encode($data);

}

 function DeleteUser(){
        global $con;
        $userid = $_POST['userid'];
        $query = $con->prepare("DELETE FROM users where userid = ?");
        $query->bind_param('i',$userid);
        $query->execute();
        $query->close();
        $con->close();
    }

function fnLogin(){
    global $con;
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    
    $query = $con->prepare("call sp_login(?,?)");
    $query->bind_param('ss',$username,$password);
    $query->execute();
    $result = $query->get_result();
    $ret = '';
    while($row = $result->fetch_array()){
        
        if($row['ret'] == 1){
            $_SESSION['userid'] = $row['userid'];
            $_SESSION['fullname'] = $row['fullname'];
            $_SESSION['role'] = $row['user_role'];
        }
        $ret = $row['ret'];
    }

    echo $ret;

}

function fnUnlockAccount(){
    global $con;
    $userid = $_POST['userid'];
    $query = $con->prepare("UPDATE users SET counterlock = 0 where userid = $userid");
    $query->execute();
    echo 1;

}


?>