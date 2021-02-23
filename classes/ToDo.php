<?php

class ToDo {
    public  $appConfig;
    private $DB;
    private $DBName;
    private $ToDoOrderBy = 1;

    public function __construct($DBName){
        $this->DBName   = $DBName;
        $this->DB       = new DBConfig($DBName);
        $this->appConfig = $this->DB->getAllConfig();
    }

    /**
     * Gets the jobs from the selected file.
     */
    public function getTodos(): array {
        $ToDos = json_decode(file_get_contents($this->DB->getDBFile()));
        $ToDos = $this->ToDos_sort($ToDos,$this->ToDoOrderBy);
        return $ToDos;
    }

    /**
     * Updates the selected job.
     */
    public function setTodo($id,$job,$priority,$date){
        $ToDos = $this->getTodos();
        foreach ($ToDos as $key => $toDo) {
            if($toDo->id == $id){
                $ToDos[$key]->job        = $job;
                $ToDos[$key]->priority   = $priority;
                $ToDos[$key]->date       = date('d.m.Y',strtotime($date));
                break;
            }
        }
        $this->save(array_values($ToDos));
    }

    /**
     * Inserts a new job.
     */
    public function insertTodo($job,$priority,$date){
        $ToDos = $this->getTodos();
        $ToDos[] = [
            'id'        => $this->DB->getLastID(),
            'job'       => $job,
            'completed' => false,
            'priority'  => $priority,
            'date'      => date('d.m.Y',strtotime($date))
        ];
        $this->DB->setLastID($this->DB->getLastID() + 1);
        $this->save(array_values($ToDos));
    }

    /**
     * Deletes the selected job
     */
    public function deleteTodo($id){
        $ToDos = $this->getTodos();
        foreach ($ToDos as $key => $toDo) {
            if($toDo->id == $id){
                unset($ToDos[$key]);
                break;
            }
        }
        $this->save(array_values($ToDos));
    }

    /**
     * Sets the job as completed or not.
     */
    public function setStatus($id,$status){
        $ToDos = $this->getTodos();
        foreach ($ToDos as $key => $toDo) {
            if($toDo->id == $id){
                $ToDos[$key]->completed  = $status;
            }
        }
        $this->save(array_values($ToDos));
    }

    /**
     * Saves the inserted to do job.
     */
    public function save($ToDos){
        file_put_contents($this->DB->getDBFile(),json_encode($ToDos));
        header('Location: '.CURR_URL);
    }

    /**
     * Returns the current order value
     */
    public function getOrderBy(){
        return $this->ToDoOrderBy;
    }

    /**
     * Sets the current order value with the given value
     */
    public function setOrderBy($order){
        $this->ToDoOrderBy = $order;
    }

    /**
     * Sorts the given array with an order
     */
    public function ToDos_sort($arr, $orderBy){
        $keyVal     = [];
        $returnArr  = [];
        foreach ($arr as $key => $value){
            $keyVal[$key] = $value->priority;
        }
        if($orderBy == 2){
            arsort($keyVal);
        }else{
            asort($keyVal);
        }
        foreach ($keyVal as $key => $val) {
            $returnArr[] = $arr[$key];
        }
        return $returnArr;
    }

    /**
     * Returns the priority mean of the number
     */
    public function getPriorityMean($num): string {
        if($num === "1"){
            return 'Low';
        }
        if($num === "2"){
            return 'Medium';
        }
        if($num === "3"){
            return 'High';
        }
        return 'NaN';
    }

}

