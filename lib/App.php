<?php


namespace Bidvestcli;

use Respect\Validation\Validator as v;
use LucidFrame\Console\ConsoleTable;
use RecursiveTreeIterator;

class App
{
    protected $printer;

    protected $registry = [];

    protected $data = [];

    protected $digits = "";

    protected $file = null;

    public function __construct()
    {
        $this->printer = new CliPrinter();
        $this->table = new ConsoleTable();
    }

    public function getPrinter()
    {
        return $this->printer;
    }

    public function getStudent()
    {
        return $this->student;
    }

    public function getable()
    {
        return $this->table;
    }

    public function registerCommand($name, $callable)
    {
        $this->registry[$name] = $callable;
    }

    public function getCommand($command)
    {
        return isset($this->registry[$command]) ? $this->registry[$command] : null;
    }

    public function runCommand(array $argv = [])
    {
        $command_name = null;

        if (isset($argv[1])) {
            $command_name = $argv[1];
        }

        $command = $this->getCommand($command_name);
       
        if ($command === null) {
            $this->getPrinter()->display("ERROR: Command \"$command_name\" not found.");
            exit;
        }

        call_user_func($command, $argv);
    }

    public function add()
    {
        $this->id();
    }

    public function edit($id)
    {
        $fileName  =  $id.'.json';
        $twoDigits =  substr($id, 0, 2);
        $this->file     = "student/".$twoDigits."/".$fileName;
        if (file_exists($this->file)) {
            $json = file_get_contents($this->file);
            $this->data = json_decode($json, true);
            $this->getPrinter()->display("Leave fields blank to keep previous value");
            $this->name();
        } else {
            $this->getPrinter()->display("Student id was not found.");
        }
    }

    public function delete($id)
    {
        $fileName  =  $id.'.json';
        $twoDigits =  substr($id, 0, 2);
        $this->file     = "student/".$twoDigits."/".$fileName;
        if (file_exists($this->file)) {
            if (unlink($this->file)) {
                rmdir("student/".$twoDigits);
                $this->getPrinter()->display("Student deleted successfully.");
            }
        } else {
            $this->getPrinter()->display("Student id was not found.");
        }
    }

    public function search()
    {
        $this->getPrinter()->display("Enter search criteria: ");
        $search = trim(fgets(STDIN, 1024));
        
        $jsonFiles = $this->getDirContents('student');
        if (empty($jsonFiles)) {
            $this->getPrinter()->display("No student data available");
        } else {               
                $count = sizeof($jsonFiles);   
                foreach ($jsonFiles as $i=>$file) {
                    foreach (json_decode(file_get_contents($file), true) as $index=>$datas) {
                        foreach ($datas as $key=>$data) {
                            $table[] = $data;
                        }                  
                    }
                }                             
                $cols = array_chunk($table, ceil(count($table)/$count), true);  
                if (isset($search) ? $search: null) { 
                    list($key, $val) = explode('=', $search);               
                    $cols = $this->searchBy($cols, $val);            
                    if(empty($cols)){
                        $this->getPrinter()->display("No student $key result was found.");
                    }else{
                        $this->table->setHeaders(['Id','Name','Surname','Age','Curriculum' ]);
                        foreach ($cols as $col) {
                            $this->table->addRow(
                                $col
                            );
                        }
                        $this->table->display();
                    }
            } else {            
                $this->table->setHeaders(['Id','Name','Surname','Age','Curriculum' ]);
                foreach ($cols as $col) {
                    $this->table->addRow(
                        $col
                    );
                }
                $this->table->display();
            }
        }
    }

    public function id()
    {
        $this->getPrinter()->display("Enter student id: ");
        $id = trim(fgets(STDIN, 1024));
        $twoDigits  =  substr($id, 0, 2);
        $this->file = "student/".$twoDigits."/".$id.".json";
        if (file_exists($this->file)) {
            $this->getPrinter()->display("Student id already exit.");
            $this->id();
        } else {
            if (v::numericVal()->length(7, 7)->notEmpty()->validate(intval($id))) {
                $this->digits = $id;
                array_push($this->data, ['id'=>$id]);
                $this->name();
            } else {
                $this->id();
            }
        }
    }

    public function name()
    {
        $currentName = isset($this->data[1]['name']) ? $this->data[1]['name']: "";
        $label = $currentName ?"[$currentName]:":":";
        $this->getPrinter()->display("Enter student name".$label);
        $name = trim(fgets(STDIN, 1024));
        $name = (empty($name)) ? $currentName:$name;
        if (v::stringType()->length(2, 100)->notEmpty()->validate($name)) {
            if ($currentName) {
                $this->data[1]['name'] = $name;
            } else {
                array_push($this->data, ['name'=>$name]);
            }
            $this->surname();
        } else {
            $this->name();
        }
    }

    public function surname()
    {
        $currentSurname = isset($this->data[2]['surname']) ? $this->data[2]['surname']: "";
        $label = $currentSurname ?"[$currentSurname]:":":";
        $this->getPrinter()->display("Enter student surname".$label);
        $surname = trim(fgets(STDIN, 1024));
        $surname = (empty($surname)) ? $currentSurname:$surname;
        if (v::stringType()->length(2, 100)->notEmpty()->validate($surname)) {
            if ($currentSurname) {
                $this->data[2]['surname'] = $surname;
            } else {
                array_push($this->data, ['surname'=>$surname]);
            }
            $this->age();
        } else {
            $this->surname();
        }
    }

    public function age()
    {
        $currentAge = isset($this->data[3]['age']) ? $this->data[3]['age']: "";
        $label = $currentAge ?"[$currentAge]:":":";
        $this->getPrinter()->display("Enter student age".$label);
        $age = trim(fgets(STDIN, 1024));
        $age = (empty($age)) ? $currentAge:$age;
        if (v::numericVal()->notEmpty()->validate(intval($age))) {
            if ($currentAge) {
                $this->data[3]['age'] = $age;
            } else {
                array_push($this->data, ['age'=>$age]);
            }
            $this->curriculum();
        } else {
            $this->age();
        }
    }

    public function curriculum()
    {
        $currentCulum = isset($this->data[4]['curriculum']) ? $this->data[4]['curriculum']: "";
        $label = $currentCulum ?"[$currentCulum]:":":";
        $this->getPrinter()->display("Enter student curriculum".$label);
        $culum = trim(fgets(STDIN, 1024));
        $culum = (empty($culum)) ? $currentCulum:$culum;
        if (v::stringType()->notEmpty()->validate($culum)) {
            if ($currentCulum) {
                $this->data[4]['curriculum'] = $culum;
            } else {
                array_push($this->data, ['curriculum'=>$culum]);
            }
            $json      = json_encode($this->data);
            $fileName  =  $this->digits.'.json';
            $twoDigits =  substr($this->digits, 0, 2);
            $path      = "student/".$twoDigits;
            if ($this->recursive_mkdir($path, 0777, true)) {
                if (!file_exists($this->file)) {
                    $fp = fopen($path."/".$fileName, 'w');
                    fwrite($fp, $json);
                    fclose($fp);
                    $this->getPrinter()->display("Detail saved successfully.");
                } else {
                    file_put_contents($this->file, json_encode($this->data));
                    $this->getPrinter()->display("Detail updated successfully.");
                }
            } else {
                $this->getPrinter()->display("An error occured, please try again");
                $this->name();
            }
        } else {
            $this->curriculum();
        }
    }
    
    public function recursive_mkdir($path, $permissions, $create)
    {
        if (!is_dir($path)) {
            if (mkdir($path, $permissions, $create)) {
                return true;
            }
            return false;
        } elseif (is_dir($path)) {
            return true;
        }
    }

    public function getDirContents($dir, $filter = '', &$results = array())
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);

            if (!is_dir($path)) {
                if (empty($filter) || preg_match($filter, $path)) {
                    $results[] = $path;
                }
            } elseif ($value != "." && $value != "..") {
                $this->getDirContents($path, $filter, $results);
            }
        }

        return $results;
    }

    public function searchBy($array, $val){
        $cols = [];
        $search = false;
        foreach($array as $key => $arr) {
            foreach($arr as $k=>$v){
                if ($v == $val) {
                    $search = true;
                }
            }               
        }      
        return $search ? $array:$cols;
    }
}