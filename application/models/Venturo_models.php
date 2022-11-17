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

		return $month; //nama bulan dalam bentuk formal.
	}
}
?>