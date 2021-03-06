<?php
// CLI Script
include_once ('marianaCLISetup.php');
use Mariana\Framework\DatabaseManager\DatabaseManager;

class CLI{

    private $cli_args = array();
    private $cli_options = array(
        "help",
        "server",
        "server:",
        "create:",
        "update:",
        "migrate",
        "seed"
    );
    public function __construct(Array $params= array()){

        $this->cli_args = $params;
        $this->checkForValidCommand();
    }

    # Templates
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private $tempalte_help = <<<USAGE
MARIANA COMMAND LINE HELPER
Have suggestions? Talk with us on pihh.rocks@gmail.com

OPTIONS:
help: Print this help

server: Start php server on this directory. You can customize the server settings by running: cli server XX - This will run http://localhost:XX

create: Creates a file with proper setup (replace NAME with it's name).
    - create:database NAME
    - create:table NAME - (keep it's name as plural EX: users - also creates a model)
    - create:controller NAME - (will be named as nameController)
    - create:model NAME - (keep it's name as plural EX: users)
    - create:middleware NAME

update: Updates database tables
    - seed
    - drop
\n
USAGE;

    private function template_controller($name){
        /**
         * @param string $name
         * @desc creates a database seed
         */
        return
            "<?php
/**
  * Created with love using Mariana Framework
  * Need help? Ask Pihh The Creator pihh.rocks@gmail.com
  */

use Mariana\\Framework;
use Mariana\\Framework\\Controller;
use Mariana\\Framework\\Database;

class $name extends Controller{

    /**
     * Default method;
     */
     public function index(){

     }

}
?>";
    }

    private function template_model($name, $table){
        /**
         * @param string $name
         * @desc creates a database seed
         */
        return
            "<?php
/**
  * Created with love using Mariana Framework
  * Need help? Ask Pihh The Creator pihh.rocks@gmail.com
  */

use \\Mariana\\Framework\\Model;

class $name extends Model{

//protected \$table = '$table';
//protected \$primary;

/* Database relationships:
public function something(\$how_many){
    \$this->has(
        'other_table',
        'other_table_key_that_matcher_this_table_key',
        'other_table_column_that_we_want_to_fetch',
        'this_table_key_we_want_to_match = false',
        'how_many_results_we_want_to_get = false');
    }

    //Example: supose this is users model and users made comments
    \$this->has( 'comments','user_id','comment_content','id',5);

    //Will Return: Join to this query, the content of the first 5 comments (comments.comment_content)
    //on wich the comments.user_id value is equal to the users.id value. Hope it makes sense :D )
}

public function observeSomething(\$class, \$method, \$params, \$array_of_observed = false){
    \$this->observe('email','send','params', array);

    //this would for example send and email, with some parameters, to the array of people that we specify
}
*/
}
?>";
    }

    private function template_middleware($name){
        /**
         * @param string $name
         * @desc creates a database seed
         */
        return
            "<?php
/**
  * Created with love using Mariana Framework
  * Need help? Ask Pihh The Creator pihh.rocks@gmail.com
  */

use Mariana\\Framework\\Middleware;

class $name extends Middleware{

    public function before(){
        /**
          * @desc: Runs before the request
          * @example: Authorization Middleware should check if the email and password requests are set before running the script
          */

    }

    public function after(){
        /**
         *  @desc: Runs after the request
         *  @example: Authorization Middleware should check if after the user logs in, for example check if the session was set
         */
    }
}
?>";
    }


    # AUXILIAR FUNCTIONS
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    private function checkIfFileExists($path){

        /**
         * @param $path
         * @return bool
         */

        if(file_exists($path)) {
            echo "\nFile allready exists in $path. Please delete it or rename your new file.\n";
            return false;
        }
        return true;
    }

    private function makeFile($path, $contents){
        /**
         * @param $path
         * @param $contents
         * @desc if file doesn't exist, creates a file and writes it's contents
         */

        $my_file = $path;
        $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$path);
        $data = $contents;
        fwrite($handle, $data);
        fclose($handle);

    }

    private function parseName($name){
        /**
         * @param $name
         * @param bool|false $parsingType
         * @desc: parses the name as the convention says
         * @return string $name
         */

        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $name)));
        $name = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));

        return $name;
    }

    private function checkForValidCommand(){

        /**
         * @param $commands
         * @param $options
         * @desc checks if command is valid, if not, returns help.
         * @return command() | help()
         */

        # Command String threatment
        $command = strtolower(trim($this->cli_args[0]));

        foreach ($this->cli_options as $o) {
            # Option String treatment
            $o = strtolower(trim($o));

            # Checks for commands that don't have :
            if($o == $command && strpos($o, ':') !== 0) {
                return $this->{$o}();
            }

            # Checks for composite commands ( the ones who have : )
            if(strpos($command, $o) === 0){

                # String threatment
                # Remove current command from the cli_args
                array_shift($this->cli_args);

                $decouple = explode(":",$command);
                $command = $decouple[0];
                $command_type = $decouple[1];
                $command_args = $this->cli_args;


                return $this->{$command}($command_type , $command_args);
            }
        }

        return $this->help();
    }

    # THE FUNCTIONS
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private function help(){

        echo $this->tempalte_help;
        exit;
    }

    private function server($port = false){
        /**
         * @param bool $port
         * @return string
         * @default port 314 (port called because of my name - pi 3.14)
         * @desc starts php server on this folder and opens browser
         */
        ($port == false)?
            $port = 314:
            $port = $port;

        echo ("\nServer running at http://localhost:$port.\n To quit press CTRL + C");

        $command_1 = "start /max http://localhost:$port";
        $command_2 = "php -S localhost:$port";

        return exec($command_1."&& ".$command_2);
    }

    public function create($what, Array $args = array()){
        /**
         * @param $what
         * @param array $args
         * @return bool|void
         * @desc creates files and runs composer dump-autoload function
         */

        $what = strtolower(trim($what));

        $possibleCreations = array(
            "database",
            "table",
            "model",
            "controller",
            "middleware"
        );

        if(!in_array($what,$possibleCreations)){
            echo "\nWARNING: It's not possible to create $what . More info: php mariana help\n";
            return true;
        }

        if(empty($args)){
            echo "\nWARNING: Cannot create $what whitout giving it a name. More info: php mariana help\n";
            return true;
        }else{
            $name = $args[0];
        }

        # THE FUNCTIONS

        # CREATE CONTROLLER
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if($what == "controller"){

            $file_name = strtolower($name);
            $name = $this->parseName($name);
            $path = ROOT.DS."mvc".DS."controllers".DS.$file_name.".controller.php";

            if($this->checkIfFileExists($path)== false){
                return false;
            }

            $contents = $this->template_controller($name);

            $this->makeFile($path, $contents);

            echo "\nCreated controller: $name in $path.\n";
            return $this->composer();

        }

        # CREATE MODEL
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if($what == "model") {
            $file_name = strtolower($name);
            $name = $this->parseName($name);
            $path = ROOT.DS."mvc".DS."models".DS.$file_name.".model.php";

            if($this->checkIfFileExists($path)== false){
                return false;
            }

            $contents = $this->template_model($name,$file_name);

            $this->makeFile($path, $contents);

            echo "\nCreated model: $name in $path.\n";
            return $this->composer();

        }

        # THE MIDDLEWARE
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if($what == "middleware") {
            $file_name = strtolower($name);
            $name = $this->parseName($name);
            $path = ROOT.DS."mvc".DS."middleware".DS.$file_name.".php";

            if($this->checkIfFileExists($path)== false){
                return false;
            }

            $contents = $this->template_middleware($name);

            $this->makeFile($path, $contents);

            echo "\nCreated middleware: $name in $path. \n";
            return $this->composer();
        }

        # CREATE DATABASE
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if($what == "database"){
            /**
             * @Should Do:
             *  1- Create database
             *  2- Create file at app/files/database/databases/$name/$name
             */
            $name = strtolower($this->parseName($name));

            DatabaseManager::createDatabase($name);
            echo "Created database: $name. The export database file is at: app/files/database/databases/$name";
            // Confirmations:

            return true;
        }   # Done and tested



        # CREATE TABLE
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        if($what == "table"){
            DatabaseManager::createTable($name);
            return true;
        }


        return help();
    }

    public function update($what, Array $args = array()){

        if(empty($args)){
            echo "\nWARNING: Cannot update table whitout proper naming it. More info: php mariana help\n";
            return true;
        }else{
            $name = $args[0];
        }
        DatabaseManager::updateTable($name);
        return true;
    }

    public function migrate($database= false){
        if($database == false){
            $database = Config::get('database');
        }
        DatabaseManager::migrate($database);
    }

    public function seed($table){
        DatabaseManager::seedTable($table);
    }

    private function composer(){
        /**
         * @desc runs composer dump-autoload
         */
        return exec("composer dump-autoload");
    }

}


# STARTUP
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$cli = new CLI($commands);

exit();
