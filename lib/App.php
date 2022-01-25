<?php

namespace Bidvestcli;

use Respect\Validation\Validator as v;

class App {

    protected $printer;

    protected $registry = [];

    protected $data = [];

    protected $digits = "";

    protected $file = null;

    public function __construct(){
        $this->printer = new CliPrinter();
        $this->student = new Student();
    }

    public function getPrinter(){
        return $this->printer;
    }

    public function getStudent(){
        return $this->student;
    }

    public function registerCommand($name, $callable){
        $this->registry[$name] = $callable;
    }

    public function getCommand($command){
        return isset($this->registry[$command]) ? $this->registry[$command] : null;
    }

    public function runCommand(array $argv = []){
        $command_name = null;

        if(isset($argv[1])) {
            $command_name = $argv[1];
        }

        $command = $this->getCommand($command_name);
       
        if($command === null) {
            $this->getPrinter()->display("ERROR: Command \"$command_name\" not found.");
            exit;
        }

        call_user_func($command, $argv);
    }

    public function add(){
        $this->id();
    }

    public function edit($id){
        $fileName  =  $id.'.json';
        $twoDigits =  substr($id, 0, 2);
        $this->file     = "student/".$twoDigits."/".$fileName;
        if (file_exists($this->file)) {
            $json = file_get_contents($this->file);
            $this->data = json_decode($json, true);
            $this->name();
        }else{
            $this->getPrinter()->display("Id was not found.");
        }
    }

    public function id(){
        $this->getPrinter()->display("Enter student id: ");
        $id = trim(fgets(STDIN, 1024));
        $twoDigits  =  substr($id, 0, 2);
        $this->file = "student/".$twoDigits."/".$id.".json";
        if (file_exists($this->file)){
            $this->getPrinter()->display("Id already exit.");
            $this->id();
        }else{  
            if (v::numericVal()->length(7, 7)->notEmpty()->validate(intval($id))) {
                $this->digits = $id;
                array_push($this->data, ['id'=>$id]);
                $this->name();
            } else {
                $this->id();
            }
        }
    }

    public function name(){
        $this->getPrinter()->display("Enter student name: ");
            $name = trim(fgets(STDIN, 1024));
            $currentName = isset($this->data[1]['name']) ? $this->data[1]['name']: "";
            $name = (empty($name)) ? $currentName:$name;
            if (v::stringType()->length(2, 100)->notEmpty()->validate($name)) {
                if($currentName){
                    $this->data[1]['name'] = $name;
                }else{
                    array_push($this->data, ['name'=>$name]);
                }
                $this->surname();
            } else {
                $this->name();
            }
    }

    public function surname(){
        $this->getPrinter()->display("Enter student surname: ");
        $surname = trim(fgets(STDIN, 1024));
        $currentSurname = isset($this->data[2]['surname']) ? $this->data[2]['surname']: "";
        $surname = (empty($surname)) ? $currentSurname:$surname;
        if (v::stringType()->length(2,100)->notEmpty()->validate($surname)) {
            if($currentSurname){
                $this->data[2]['surname'] = $surname;
            }else{
                array_push($this->data,['surname'=>$surname]);
            }
            $this->age();
        }else{
            $this->surname();
        }
    }

    public function age(){
        $this->getPrinter()->display("Enter student age: ");
        $age = trim(fgets(STDIN, 1024));
        $currentAge = isset($this->data[3]['age']) ? $this->data[3]['age']: "";
        $age = (empty($age)) ? $currentAge:$age;
        if (v::numericVal()->notEmpty()->validate(intval($age))) {
            if ($currentAge) {
                $this->data[3]['age'] = $age;        
            }else{
                array_push($this->data, ['age'=>$age]);
            }
            $this->curriculum();
        }else{
            $this->age();
        }
    }

    public function curriculum(){
        $this->getPrinter()->display("Enter student curriculum: ");
        $culum = trim(fgets(STDIN, 1024));
        $currentCulum = isset($this->data[4]['curriculum']) ? $this->data[4]['curriculum']: "";
        $culum = (empty($culum)) ? $currentCulum:$culum;
        if(v::stringType()->notEmpty()->validate($culum)) {
            if ($currentCulum) {
                $this->data[4]['curriculum'] = $culum;        
            }else{
                array_push($this->data,['curriculum'=>$culum]);
            }     
            $json      = json_encode($this->data);
            $fileName  =  $this->digits.'.json';
            $twoDigits =  substr($this->digits, 0, 2);
            $path      = "student/".$twoDigits;
            if($this->recursive_mkdir($path,0777,true)){
                if(!file_exists($this->file)){
                    $fp = fopen($path."/".$fileName, 'w');
                    fwrite($fp, $json);
                    fclose($fp);
                    $this->getPrinter()->display("Detail saved successfully.");
                }else{
                    file_put_contents($this->file, json_encode($this->data));
                    $this->getPrinter()->display("Detail updated successfully.");
                }
            }else{
                $this->getPrinter()->display("An error occured, please try again");
                $this->name();
            }
        }else{
            $this->curriculum();
        }
    }
    
    public function recursive_mkdir($path, $permissions, $create){
        if(!is_dir($path)){ 
            if(mkdir($path, $permissions, $create)){
               return true;
            }  
            return false;        
        }elseif(is_dir($path)){
            return true;
        }
    }

    
}