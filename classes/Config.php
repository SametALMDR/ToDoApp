<?php

class Config {
    private $appName = 'TODO App';
    private $appDescription = 'This is a simple TODO app that coded using PHP language.';
    private $appVersion = 'v1.0';
    private $appAuthor = 'Samet ALEMDAROĞLU';

    public function __get($name){
        return $this->$name;
    }

    public function __set($name, $value){
        if(!empty($name)){
            $this->$name = $value;
        }
    }

    /**
     * Returns all properties of the class in an array
     */
    public function getAllConfig() : array {
        return [
            'appName'           => $this->appName,
            'appDescription'    => $this->appDescription,
            'appVersion'        => $this->appVersion,
            'appAuthor'         => $this->appAuthor
        ];
    }
}

class DBConfig extends Config {

    private $DBPath = 'DB'.DS;
    private $DBFile;

    public function __construct(string $DBFile){
        $this->DBFile = $DBFile;
        $dirCheck   = is_dir($this->DBPath);
        $fileCheck  = file_exists($this->DBPath . $this->DBFile . '.json');
        $confCheck  = file_exists($this->DBPath . 'conf.json');
        if (!$dirCheck){
            $this->dirCreate();
        }
        if (!$fileCheck){
            $this->dbCreate($DBFile);
        }
        if(!$confCheck){
            $this->confCreate();
        }
    }

    /**
     * Creates a new directory and returns its status
     */
    private function dirCreate() : bool {
        return mkdir($this->DBPath);
    }

    /**
     * Creates a new empty file area(such as database) to save our data.
     */
    private function dbCreate(string $DBName): bool {
        return file_put_contents($this->DBPath . $DBName . '.json', json_encode([]));
    }

    /**
     * Returns the full path of the database file
     */
    public function getDBFile() : string {
        return $this->DBPath . $this->DBFile . '.json';
    }

    /**
     * Creates a new empty file area to save last record index.
     */
    private function confCreate() : bool {
        return file_put_contents($this->DBPath . 'conf.json', json_encode(['LastID'=>'1']));
    }

    /**
     * Returns the content of the database file
     */
    private function getConfFileContent() : object {
        return json_decode(file_get_contents($this->DBPath . 'conf.json'));
    }

    /**
     * Returns the last record index
     */
    public function getLastID() {
        return $this->getConfFileContent()->LastID;
    }

    /**
     * Sets the last record index
     */
    public function setLastID($id) : bool{
        return file_put_contents($this->DBPath . 'conf.json', json_encode(['LastID'=>$id]));
    }


}
