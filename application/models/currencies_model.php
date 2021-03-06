<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Currencies_model extends CI_Model {

	public function __construct() { 
		parent::__construct();
		$this->load->library('bw_config');
	}
	
	// Load information about all currencies, or just one.
	public function get($id = NULL) {
		
		if($id == NULL) {
			$this->db->select('id, code, name, symbol');
			$query = $this->db->get('currencies');
		} else {
			$this->db->select('id, code, name, symbol');
			$query = $this->db->get_where('currencies', array('id' => "$id"));
		}
		
		if($query->num_rows() > 0){
			if($id == NULL)
				return $query->result_array();
			
			$row = $query->row_array();
			$row['rate'] = $this->get_exchange_rate(strtolower($row['code']));
			return $row;
		}
		
		return FALSE;
	}

	public function get_exchange_rates() {
		$this->db->select('time, usd, eur, gbp');
		$this->db->order_by('id desc');
		$this->db->limit('1');
		
		$query = $this->db->get('exchange_rates');
		if($query->num_rows() > 0) {
			$row = $query->row_array();
			$row['time_f'] = $this->general->format_time($row['time']);
			
			$result = array('bpi' => array('usd' => $this->get_exchange_rate('usd'),
											'eur' => $this->get_exchange_rate('eur'),
											'gbp' => $this->get_exchange_rate('gbp')),
							'time' => $row['time'],
							'time_f' => $this->general->format_time($row['time']));
			return $result;
		}
		return FALSE;
	}
	
	public function get_exchange_rate($code) {
		$this->db->order_by('id desc');
		$this->db->limit('1');
		
		$query = $this->db->get('exchange_rates');
		if($query->num_rows() > 0) {
			$row = $query->row_array();
			return $row[$code];
		}
		return FALSE;
	}
	
	public function update_exchange_rates($update) {
		if($this->db->insert('exchange_rates', $update) == TRUE)
			return TRUE;
		
		return FALSE;
	}
};
