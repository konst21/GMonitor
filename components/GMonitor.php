<?php
namespace app\components;

use yii\base\Component;

use Yii;

/**
 * Class GearmanControl
 * @inheritdoc
 * @property \app\components\GMonitor
 * @package app\components
 */
class GMonitor extends Component
{

    /**
     * Gearman host
     * default localhost
     * @var string
     */
    public $host = '127.0.0.1';

    /**
     * @inheritdoc
     *
     */
    public $port = 4730;


    private $timeout = 10;

    /**
     * @var bool|\resource
     */
    public $connection_handler = false;

    /**
     * @throws \Exception
     * When the object is created immediately open a socket to the server,
     * failure - generated exception
     * Exception should be handled with each object creation
     *
     * The most common situation generate this exception is not running the server
     * or not connections to it
     */
    public function __construct(){
        $this->connection_handler = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        if(!$this->connection_handler){
            throw new \Exception("Error! Msg: " . $errstr . " ; Code: ". $errno);
        }
        parent::__construct();
    }

    /**
     * close socket after work
     */
    public function __destruct(){
        if(is_resource($this->connection_handler)){
            fclose($this->connection_handler);
        }
    }

    /**
     * Send command to Gearman server
     * in this web-application use only commands 'status' and 'workers'
     * @param  $cmd
     * @return void
     */
    private function send_cmd($cmd){
        fwrite($this->connection_handler, $cmd."\r\n");
    }

    /**
     * Receive data from Gearman server after command send
     * @return string
     */
    private function receive_cmd_data(){
        $full_response = '';
        while (true) {
            $data = fgets($this->connection_handler , 4096);
            if ($data == ".\n") {
                break;
            }
            $full_response .= $data;
        }

        return $full_response;
    }


    /**
     * This functions will added to Gearman Job Server
     * Must contain a some or all names of functions defined in /commands/functions.php
     * @return array
     */
    public static function functionsList()
    {
        return [
            'test1',
            'test2',
        ];
    }

    /**
     * @return int
     */
    public function worker_count ()
    {
        $command_string = 'ps ax | grep "worker/main"';
        ob_start();
        system($command_string);
        $raw_out = ob_get_contents();
        ob_clean();
        return count(explode("\n", $raw_out)) - 3;
    }

    /**
     * @param string $worker - 'main' or 'fake'
     * @return string
     */
    private function worker_command_string ($worker = 'main')
    {
        return Yii::getAlias('@app') . "/yii worker/$worker ";
    }



    /**
     * The full list of functions that are registered on the server
     * Treatment done in the situation when the function was once registered
     * and Gearman responds that such a function is, but she 0 queue 0 in process and 0 for workers
     * @return array
     */
    public function all_functions_statuses ()
    {
        $this->send_cmd('status');
        $raw_data =  $this->receive_cmd_data();

        $status = [
            'result' => 'fail',
        ];
        $temp_array = explode("\n", $raw_data);
        if (is_array($temp_array) && count($temp_array) > 0) {
            $status = [
                'result' => 'ok',
            ];
            foreach ($temp_array as $item) {
                $raw_array = explode("\t", $item);
                if(is_array($raw_array) && count($raw_array) == 4){
                    //this check need becase after stop of worker your function
                    //can be registered at Gearman Job server, this is a feature or fake )
                    if($raw_array[0] && ($raw_array[1] != 0 || $raw_array[2] !=0 || $raw_array[3] != 0)){
                        $status['data'][$raw_array[0]] = array(
                            'function_name' => $raw_array[0],
                            'in_queue' => $raw_array[1],
                            'jobs_running' => $raw_array[2],
                            'capable_workers' => $raw_array[3]
                        );
                    }

                }
            }

        }
        return $status;
    }

    /**
     * function status, false if none
     * @param  $function_name
     * @return mixed
     */
    public function function_status($function_name)
    {
        $all_func_array = $this->all_functions_statuses();
        if (!array_key_exists('data', $all_func_array)) return false;
        if(!array_key_exists($function_name, $all_func_array['data'])){
            return false;
        }
        return $all_func_array['data'][$function_name]['in_queue'];
    }

    /**
     * reset the task queue for the function
     * @param  $function_name
     * @return string
     */
    public function reset_function_queue($function_name)
    {
        $number_of_func = $this->function_status($function_name);
        if ($number_of_func > 0) {
            $this->fake_worker_start($function_name);

            $counter = 1000;
            //wait until the queue is cleared
            while($number_of_func !=0 && $counter > 0){
                $number_of_func = $this->function_status($function_name);
                //small delay while Fake worker handle function queue
                usleep(10000);
                $counter--;
            }
            $this->fake_worker_stop();
            //queue is reset correctly
            if ($counter > 0) {
                return 'ok';
            }
            return 'fail';
        }
        return 'ok';
    }

    /**
     * Reset the entire queue - reset each task
     * @return string
     */
    public function reset_all_queue(){
        $functions_list = array_keys($this->all_functions_statuses()['data']);
        if (is_array($functions_list)) {
            foreach($functions_list as $function){
                //@todo check correctness of each function's reset
                $this->reset_function_queue($function);
            }
        }
        return 'ok';
    }

    /**
     * All workers, registered on Gearman server
     * This method not used and really not check
     * @todo check all data and functions as array
     * @return array
     */
    public function workers_list(){
        $this->send_cmd('workers');
        $raw_workers = $this->receive_cmd_data();
        $workers = array();
        $temp_array = explode("\n", $raw_workers);
        foreach ($temp_array as $item) {

            $z = explode(" : ", $item);
            if(is_array($z) && count($z) > 1){
                $info = $z[0];
                $functions = $z[1];
                list($fd, $ip, $id) = explode(' ', $info);

                $functions = explode(' ', trim($functions));

                if(is_array($functions) && count($functions) > 0){
                    $workers[] = array(
                        'fd' => $fd,
                        'ip' => $ip,
                        'id' => $id,
                        'functions' => $functions
                    );
                }
            }
        }
        return $workers;
    }

    /**
     * Start worker for parser
     */
    public function main_worker_start(){
        exec("php " . $this->worker_command_string('main') ." > /dev/null &");
    }

    /**
     * Stop all workers for parser
     */
    public function main_worker_stop(){
        exec("ps ax | grep worker/main | awk '{print $1}' | xargs kill");
    }

    /**
     * fake worker for reset $function queue
     * @param $function_name
     */
    public function fake_worker_start($function_name){
        exec("php " . $this->worker_command_string('fake') ." $function_name > /dev/null &");
    }

    /**
     * Stop fake worker
     */
    private function fake_worker_stop(){
        exec("ps ax | grep worker/fake | awk '{print $1}' | xargs kill");
    }

}
 
