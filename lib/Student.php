<?php

namespace Bidvestcli;

class Student{

    private $servername = "localhost";
    private $name   = "root";
    private $password   = "";
    private $database   = "Database Name here";
    public  $con;


    // Database Connection 
    public function __construct()
    {
       
    }

    // Add student data
    public function add($post)
    {
        $name = $this->con->real_escape_string($_POST['name']);
        $email = $this->con->real_escape_string($_POST['email']);
        $username = $this->con->real_escape_string($_POST['username']);
        $password = $this->con->real_escape_string(md5($_POST['password']));
        $query="INSERT INTO customers(name,email,username,password) VALUES('$name','$email','$username','$password')";
        $sql = $this->con->query($query);
        if ($sql==true) {
            header("Location:index.php?msg1=insert");
        }else{
            echo "Registration failed try again!";
        }
    }
    // Edit student data
    public function edit($postData)
    {
        $name = $this->con->real_escape_string($_POST['uname']);
        $email = $this->con->real_escape_string($_POST['uemail']);
        $username = $this->con->real_escape_string($_POST['upname']);
        $id = $this->con->real_escape_string($_POST['id']);
    if (!empty($id) && !empty($postData)) {
        $query = "UPDATE customers SET name = '$name', email = '$email', username = '$username' WHERE id = '$id'";
        $sql = $this->con->query($query);
        if ($sql==true) {
            header("Location:index.php?msg2=update");
        }else{
            echo "Registration updated failed try again!";
        }
        }
         
    }
    // Search student data
    public function search($postData)
    {
    }
    // Delete student data
    public function deleteRecord($id)
    {
        $query = "DELETE FROM customers WHERE id = '$id'";
        $sql = $this->con->query($query);
    if ($sql==true) {
        header("Location:index.php?msg3=delete");
    }else{
        echo "Record does not delete try again";
        }
    }

}