<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Veturo extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('venturo_models', 'Venturo_models');
		$this->load->library('table');
	}
	public $path = 'https://tes-web.landa.id/intermediate/';

	public function tampil($value = null){
		if($value !== null){
			for ($i = 1; $i <= 12; $i++){
				echo $i . ' ' . $value['ttl'].'<br>';
				//var_dump($value['ttl']);
			}
		}
	}

	public function buildData($result)
	{
		$raw['menu'] = $result['menu'];
		$raw['transaksi'] = $result['transaksi'];
		$menu= $result['menu'];
		$transaksi = $result['transaksi'];

		$ttl = [];
		$ttlmn = [];
		foreach ($raw['menu'] as $a) {
			foreach ($raw['transaksi'] as $b) {
				if ($b->menu == $a->menu) {
					$mn = $b->menu;
					$bln = $this->Venturo_models->getMonth($b->tanggal);
					$ttl[$bln][] = $b->total;
					$ttlmn[$mn][] = $b->total;
				}
			}

			foreach ($ttl as $c => $cd) {
				$raw['ttl'] = $this->Venturo_models->hitung($cd);
			}

			foreach ($ttlmn as $d => $de){
				$raw['ttlmn'] = $this->Venturo_models->hitung($de);
			}
			
			$ttlmn = [];
			$ttl = [];
		}

		$this->tampil($raw);
	}

	public function index()
	{
		$post = $this->input->post('tahun');

		$menu = array(
			'url' => $this->path, 
			'perintah' => 'menu'
		);

		//$data = array(
		//	'tahun' => $this->input->post('tahun')
		//);

		if ($post== '2021') {
			$transaksi = array(
				'url' => $this->path,
				'perintah' => 'transaksi',
				'tahun' => '2021'
			);
			$jsonTransaksi = $this->Venturo_models->venturoApi($transaksi);
			$resultTransaksi = $this->Venturo_models->ekstrakData($jsonTransaksi, 'object');
		}elseif($post == '2022'){
			$transaksi = array(
				'url' => $this->path,
				'perintah' => 'transaksi',
				'tahun' => '2022'
			);
			$jsonTransaksi = $this->Venturo_models->venturoApi($transaksi);
			$resultTransaksi = $this->Venturo_models->ekstrakData($jsonTransaksi, 'object');
		}else{
			$this->load->view('VenturoTest', null);
		}

		$jsonMenu = $this->Venturo_models->venturoApi($menu);
		$resultMenu = $this->Venturo_models->ekstrakData($jsonMenu, 'object');
		
		if ($post !== null) {
			# code...
			$result = array(
				'menu' => $resultMenu, 
				'transaksi' => $resultTransaksi
			);
			$this->buildData($result);
			$this->load->view('welcome_message', $result);
		}
	}
}
