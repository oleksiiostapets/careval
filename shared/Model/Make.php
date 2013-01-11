<?php
class Model_Make extends Model_Table {
	public $table="make";
	function init(){
		parent::init();
		
		$this->addField('name')->mandatory('Name cannot be empty');
		$this->addField('reviewed');
		
		$this->hasMany('Mod');
		
		$this->addExpression('models_count')->set(function($m,$q){
			return $m->refSQL('Mod')->count();
		});
		
		$this->addHook('beforeSave',function($m){
			$m['name']=ucfirst($m['name']);
			if($m->api->auth->model['is_admin']){
				$m['reviewed']=true;
			}
		});
		
	}

	// Returns array of all Makes ordered
	function tree($order='name'){
		$result=array();
		$makes=$this->setOrder($order);
		foreach($makes as $make){
			$result[$make['id']]=$make['name'];
		}
		return $result;
	}

	// Returns the first Make by order
	function first_make_id($order='name'){
		$result=0;
		$makes=$this->setOrder($order);
		foreach($makes as $make){
			$result=$make['id'];
			break;
		}
		return $result;
	}
	
	// Returns array of all Mods groupped by make_id and sorted by name
	function mods_tree(){
		$result=array();
		// Make joined select to get models with makes
		$rows=$this->add('Model_Mod')
				->dsql()
				->join('make','make_id','left')
				->field('make.id AS make_id, make.name AS make_name, mod.id AS model_id, mod.name AS model_name')
				->order('make_name, model_name')
				;

		// Making array to have the results of query in correct format
		$a_rows = array();
		foreach($rows as $row){
			$a_rows[]=array($row['make_id'],$row['model_id'],$row['model_name']);
		}
		// Making result array in format [make_id]=array(model_id=>model_name)
		$last_make_id='';
		$a_mods=array();
		for($i=0;$i<count($a_rows);$i++){
			if($last_make_id!=$a_rows[$i][0]){
				if($last_make_id!='') $result[$a_rows[$i-1][0]]=$a_mods;
				$a_mods=array();
				$last_make_id=$a_rows[$i][0];
			}
			$a_mods[$a_rows[$i][1]]=$a_rows[$i][2];
			if($i==count($a_rows)-1) $result[$a_rows[$i][0]]=$a_mods;
		}
		return $result;
	}

}