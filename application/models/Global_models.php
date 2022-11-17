<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Global_models extends CI_Model {
	
	// Datatables definition
	
	var $table;
	var $select_column;
	var $order_column;
	var $where_column = array();
	var $order_id = array();
	var $search_query = array();
	var $group_by = array();
	
	function __construct(){
		parent::__construct();
    }
    
    function setSelectColumn($data = array()){
		$this->select_column = $data;
	}
	
	function setGroupBy($data = array()){
		$this->group_by = $data;
	}
	
	function setSearchQuery($data = array()){
		$this->search_query = $data;
	}
	
	function setOrderId($data = array()){
		$this->order_id = $data;
	}
	
	function setTableDatabase($data = ''){
		$this->table = $data;
	}
	
	function setOrderColumn($data = array()){
		$this->order_column = $data;
	}
	
	function setWhereColumn($data = array()){
		$this->where_column = $data;
	}
	
	function generateDatatables(){
		$this->queryDatatables();
		if($_GET["length"] != -1){
			$this->db->limit($this->input->get('length', TRUE), $this->input->get('start', TRUE));
		}
		$query = $this->db->get();
		return $query->result();
	}
	
	function queryDatatables(){  
       $this->db->select($this->select_column);  
       $this->db->from($this->table);  
       if(count($this->where_column) != 0){
       		$this->db->where($this->where_column);
       }
       if(isset($_GET["search"]["value"]) and @$_GET["search"]["value"] != ''){
       		$a = 0;
       		if(count($this->search_query) != 0){
				foreach($this->search_query as $search){
					if($a == 0){
						$this->db->group_start();
						$this->db->like($search, $_GET["search"]["value"]);
					}
					else{
						$this->db->or_like($search, $_GET["search"]["value"]);
					}
					if(count($this->search_query) -1 == $a){
                    	$this->db->group_end();
					}
					$a++;
				}
			}
       }
       if(count($this->group_by) != 0){
       		$this->db->group_by($this->group_by);
       }
       if(isset($_GET["order"])){  
            $this->db->order_by($this->order_column[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);  
       }  
       else{  
            $this->db->order_by($this->order_id[0], $this->order_id[1]);  
       }
    }
    
    function get_filtered_data(){  
       $this->queryDatatables();  
       $query = $this->db->get();  
       return $query->num_rows();
    }
    
    function get_all_data(){
    	$this->db->select("*");
    	$this->db->from($this->table);
    	return $this->db->count_all_results();
    }
    
    function addData($data = array(),$table = NULL){
		$sql = $this->db->insert($table,$data);
		if($sql){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	function deleteData($table = NULL,$where = array()){
		$sql = $this->db->delete($table, $where);
		if($sql){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	function updateData($table = NULL, $data = array(), $where = array()){
		if(count($where) > 0){
			foreach($where as $key =>$val){
				$this->db->where($key,$val);
			}
		}
		$sql = $this->db->update($table, $data); 
		if($sql){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
    
	function selectData($nm_tbl = NULL, $select = NULL, $where = array(),$result = FALSE, $limit_start = NULL, $limit_end = NULL, $order = NULL){
		if(count($where) > 0){
			foreach($where as $key =>$val){
				$this->db->where($key,$val);
			}
		}
		if($select != NULL){
			$this->db->select($select);
		}
		if($order != NULL){
			$this->db->order_by($order);
		}
		if($limit_start != NULL){
			$this->db->limit($limit_start);
		}
		if($limit_start != NULL and $limit_end != NULL ){
			$this->db->limit($limit_start,$limit_end);
		}
		$query = $this->db->get($nm_tbl);
		if($query->num_rows() > 0){
			if($result == TRUE){
				$data = $query->row();
			}
			else{
				$data = $query->result();
			}
			return $data;
		}
	}
}
?>