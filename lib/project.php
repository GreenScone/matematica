<?php
/**
 * Created by PhpStorm.
 * User: Bohdan
 * Date: 05.05.14
 * Time: 13:40
 */

class Project{

    private static $table = "projects";
    private static $table_users = "user_to_project";
    private static $table_tags = "tag_to_project";
    private static $table_objects = "object_to_project";
    public $id;
    public $name_ua;
    public $name_ru;
    public $name_en;
    public $url;
    public $description_ua;
    public $description_ru;
    public $description_en;
    public $tags = array();
    public $users = array();
    public $active;
    public $objects = array();
    public $prev_image;



    public static function getProjectsList($type, $start = 0, $limit = '9999'){
        global $db;
        $list = array();

        switch($type){
            case "array":
                $result = $db->Select(self::$table,"*");
                foreach($result as $row){
                    $list[$row->id] = $row->name_ru;
                }
                break;
            case "objects":
            default:
                $result = $db->Select(self::$table." t1 LEFT JOIN ".self::$table_tags." t2 ON t1.id = t2.project_id","t1.*,GROUP_CONCAT(t2.tag_id) as tags","","t1.order DESC","",$start.','.$limit);

                foreach($result as $proj_fields){
                    $proj = new Project();
                    foreach($proj_fields as $key=>$value){
                        if(is_numeric($key)) continue;
                        $proj->$key = $value;
                    }
                    $list[] = $proj;
                }
                break;
        }

        return $list;
    }

    public function getProject($project_id){
        global $db;

        $result = $db->Select(
            self::$table." t1
            LEFT JOIN ".self::$table_tags." t2 ON t1.id=t2.project_id
            LEFT JOIN ".self::$table_users." t3 ON t1.id=t3.project_id
            ",
            array(
                "t1.*",
                "GROUP_CONCAT(t2.tag_id) as tags",
                "GROUP_CONCAT(t3.user_id) as users"
            ),
            "t1.id=$project_id"
        );
        if(!$result) return;
        foreach($result[0] as $key=>$value){
            if(is_numeric($key)) continue;
            if(in_array($key,array("description_ua","description_ru","description_en"))){
                $value = json_decode($value);
            }
            if($key == "tags" || $key == "users"){
                $value = explode(",",$value);
            }
            $this->$key = $value;
        }
        return $this;
    }

    function getName(){
        $field = "name_".$_COOKIE['lang'];
        return $this->$field;

    }

    function getDescription(){
        $field = "description_".$_COOKIE['lang'];
        return $this->$field;

    }

    public function getObjects(){
        if(!$this->id) return;

        global $db;

        $result = $db->Select(self::$table_objects,"*","project_id=".$this->id);
        if($result){
            foreach($result as $row){
                $this->objects[] = $row->object;
            }
        }

    }

    public function addProject($params){
        global $db;

        if($params['description_ua']) $params['description_ua'] = json_encode($params['description_ua']);
        if($params['description_ru']) $params['description_ru'] = json_encode($params['description_ru']);
        if($params['description_en']) $params['description_en'] = json_encode($params['description_en']);

        if($_FILES['project']['name']['prev_image']){
            $params['prev_image'] = date("Ymd").$_FILES['project']['name']['prev_image'];
            move_uploaded_file($_FILES['project']['tmp_name']['prev_image'] , $_SERVER['DOCUMENT_ROOT']."/images/tmp/".$params['prev_image']);
        }

        $this->id = $db->Insert(self::$table,array_keys($params),array_values($params));
        if($this->id){
            if(is_array($_REQUEST['team'])){
                foreach($_REQUEST['team'] as $value){
                    $db->Insert(self::$table_users,array("project_id","user_id"),array($this->id,$value));
                }
            }
            if(is_array($_REQUEST['tags'])){
                foreach($_REQUEST['tags'] as $value){
                    $db->Insert(self::$table_tags,array("project_id","tag_id"),array($this->id,$value));
                }
            }
        }

        if($params['prev_image'] && $this->id){
            if(!is_dir($_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id)){
                mkdir($_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id);
            }
            rename($_SERVER['DOCUMENT_ROOT']."/images/tmp/".$params['prev_image'],$_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id."/".$params['prev_image']);
        }

        switch($params['view_type']){
            case "1":

                if($_FILES['object_one_image']['name']){
                    if(!is_dir($_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id)){
                        mkdir($_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id);
                    }
                    $filename = date("Ymd").$_FILES['object_one_image']['name'];
                    move_uploaded_file($_FILES['object_one_image']['tmp_name'] , $_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id.DIRECTORY_SEPARATOR.$filename);
                    $db->Insert(self::$table_objects,array("project_id","object","object_type"),array($this->id,$filename,1));
                }
                break;
            case "2":
                if(!is_dir($_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id)){
                    mkdir($_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id);
                }
                if($_FILES['object_gallery']['name']){
                    for($k=0;$k<count($_FILES['object_gallery']['name']);$k++){
                        $filename = date("Ymd").$_FILES['object_gallery']['name'][$k];
                        move_uploaded_file($_FILES['object_gallery']['tmp_name'][$k] , $_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id.DIRECTORY_SEPARATOR.$filename);
                        $db->Insert(self::$table_objects,array("project_id","object","object_type"),array($this->id,$filename,2));
                    }
                }
                break;
            case "3":
                $db->Insert(self::$table_objects,array("project_id","object","object_type"),array($this->id,$_REQUEST['object_html'],3));
                break;
            case "4":
                if(count($_FILES['object_gallery_v']['name'])>0){
                    if(!is_dir($_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id)){
                        mkdir($_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id);
                    }
                    $db->Delete(self::$table_objects,"project_id=".$this->id);
                    for($k=0;$k<count($_FILES['object_gallery_v']['name']);$k++){
                        $filename = date("Ymd").$_FILES['object_gallery_v']['name'][$k];
                        move_uploaded_file($_FILES['object_gallery_v']['tmp_name'][$k] , $_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id.DIRECTORY_SEPARATOR.$filename);
                        $db->Insert(self::$table_objects,array("project_id","object","object_type"),array($this->id,$filename,4));
                    }
                }
                break;
        }
    }

    public function updateProject($params){
        global $db;

        $this->id = $params['id'];
        unset($params['id']);
        if($params['description_ua']) $params['description_ua'] = json_encode($params['description_ua']);
        if($params['description_ru']) $params['description_ru'] = json_encode($params['description_ru']);
        if($params['description_en']) $params['description_en'] = json_encode($params['description_en']);

        foreach($params as $key=>$param){
            if(empty($param)){
                unset($params[$key]);
            }
        }

        if($_FILES['project']['name']['prev_image']){
            if(!is_dir($_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id)){
                mkdir($_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id);
            }
            $params['prev_image'] = date("Ymd").$_FILES['project']['name']['prev_image'];
            move_uploaded_file($_FILES['project']['tmp_name']['prev_image'] , $_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id."/".$params['prev_image']);
        }
        $db->Update(self::$table,array_keys($params),array_values($params),"id=".$this->id);
        $db->Delete(self::$table_users,"project_id=".$this->id);
        $db->Delete(self::$table_tags,"project_id=".$this->id);
        if(is_array($_REQUEST['team'])){
            foreach($_REQUEST['team'] as $value){
                $db->Insert(self::$table_users,array("project_id","user_id"),array($this->id,$value));
            }
        }
        if(is_array($_REQUEST['tags'])){
            foreach($_REQUEST['tags'] as $value){
                $db->Insert(self::$table_tags,array("project_id","tag_id"),array($this->id,$value));
            }
        }


        switch($params['view_type']){
            case "1":
                if(count($_FILES['object_one_image']['name'])>0){
                    $db->Delete(self::$table_objects,"project_id=".$this->id);
                    if(!is_dir($_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id)){
                        mkdir($_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id);
                    }
                    $filename = date("Ymd").$_FILES['object_one_image']['name'];
                    move_uploaded_file($_FILES['object_one_image']['tmp_name'] , $_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id.DIRECTORY_SEPARATOR.$filename);
                    $db->Insert(self::$table_objects,array("project_id","object","object_type"),array($this->id,$filename,1));
                }
                break;
            case "2":
                if(count($_FILES['object_gallery']['name'])>0){
                    if(!is_dir($_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id)){
                        mkdir($_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id);
                    }
                    $db->Delete(self::$table_objects,"project_id=".$this->id);
                    for($k=0;$k<count($_FILES['object_gallery']['name']);$k++){
                        $filename = date("Ymd").$_FILES['object_gallery']['name'][$k];
                        move_uploaded_file($_FILES['object_gallery']['tmp_name'][$k] , $_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id.DIRECTORY_SEPARATOR.$filename);
                        $db->Insert(self::$table_objects,array("project_id","object","object_type"),array($this->id,$filename,2));
                    }
                }
                break;
            case "3":
                $db->Update(self::$table_objects,array("object","object_type"),array($_REQUEST['object_html'],3),"project_id=".$this->id);
                break;
            case "4":
                if(count($_FILES['object_gallery_v']['name'])>0){
                    if(!is_dir($_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id)){
                        mkdir($_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id);
                    }
                    $db->Delete(self::$table_objects,"project_id=".$this->id);
                    for($k=0;$k<count($_FILES['object_gallery_v']['name']);$k++){
                        $filename = date("Ymd").$_FILES['object_gallery_v']['name'][$k];
                        move_uploaded_file($_FILES['object_gallery_v']['tmp_name'][$k] , $_SERVER['DOCUMENT_ROOT']."/images/projects/".$this->id.DIRECTORY_SEPARATOR.$filename);
                        $db->Insert(self::$table_objects,array("project_id","object","object_type"),array($this->id,$filename,2));
                    }
                }
                break;
        }

    }




}