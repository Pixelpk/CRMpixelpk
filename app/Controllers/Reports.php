<?php

namespace App\Controllers;

class Reports extends Security_Controller {

	function __construct() {
        parent::__construct();
        $this->access_only_admin();
    }


    public function index()
    {

        $list_data = $this->Projects_model->get_details()->getResult();
       	$view_data['projects'] = $list_data;
        return $this->template->rander("reports/index", $view_data);
    }

    public function detail(){
    	$project_id = $this->request->getPost("project_id");
    	$project_detail = $this->Projects_model->project_cost($project_id)->getRow();
    	$project_time = $this->Projects_model->project_time($project_id)->getResult();
  		$project_member = $this->Project_members_model->project_members($project_id)->getResult();
  		$members = array();
  		foreach ($project_member as $member) {
  			$members[$member->user_id] = $member;
  		}
  		foreach ($members as $member) {
  			foreach ($project_time as $key => $time) {
  				if($member->user_id == $time->user_id){
  					$members[$member->user_id]->time[] = $time;
  				}
  			}
  		}
  		$user_ids = array_keys($members);
  		//print_r($project_member);
  		$data['project_detail'] = $project_detail;
  		$data['members'] = $members;
  		$data['users'] = $user_ids;
		//die();
    	return json_encode($data);
    }
    
}