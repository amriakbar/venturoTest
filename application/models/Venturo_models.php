<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Venturo_models extends CI_Model {
	
	var $url;
	var $th;
	#var $bulan;

	function venturoApi($content = array()){
		$url = $content['url'];
		$command = $content['perintah'];

		if (key_exists('tahun', $content)) {
			# code...
			$th = $content['tahun'];
			$result = file_get_contents($url.$command.'?tahun='.$th);
			$hasil = $this->ekstrakData($result);
		} else {
			# code...
			$result = file_get_contents($url.$command);
			$hasil = $this->ekstrakData($result);
		}		
		return $result;
	}

	function ekstrakData($json, $method = null){
		if ($method == 'array') {
			return json_decode($json, true);
		} else {
			return json_decode($json);
		}		
	}

	function hitung($int = []){
		#var_dump(count($int));
		return array_sum($int);
	}

	function getMonth($timestamp, $perintah = null){
		$time = strtotime($timestamp);
		$month = date('F', $time);
		if (strlen($month) > 3) {
			$len = 3 - strlen($month);
			$show = substr($month, 0, $len);
		}else{
			$show = $month;
		}
		return $show; //nama bulan dalam bentuk formal.
	}

	function buildTable($column = null, $rows = null, $data = null){
		$col = $column['col'];
		$col_limit = $column['limit'];
		$template = array(
			'table_open'            => '<table border="0" cellpadding="4" cellspacing="0">',

			'thead_open'            => '<thead>',
			'thead_close'           => '</thead>',

			'heading_row_start'     => '<tr>',
			'heading_row_end'       => '</tr>',
			'heading_cell_start'    => '<th>',
			'heading_cell_end'      => '</th>',

			'tbody_open'            => '<tbody>',
			'tbody_close'           => '</tbody>',

			'row_start'             => '<tr>',
			'row_end'               => '</tr>',
			'cell_start'            => '<td>',
			'cell_end'              => '</td>',

			'row_alt_start'         => '<tr>',
			'row_alt_end'           => '</tr>',
			'cell_alt_start'        => '<td>',
			'cell_alt_end'          => '</td>',

			'table_close'           => '</table>'
		);

		$this->table->set_template($template);

		$this->table->set_heading([$col]);
		$this->table->add_row([$data], $rows);

		$this->table->function = 'htmlspecialchars';
		echo $this->table->generate();
	}
}
?>