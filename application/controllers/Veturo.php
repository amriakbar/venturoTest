<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Veturo extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('venturo_models', 'Venturo_models');
	}
	public $path = 'http://tes-web.landa.id/intermediate/';

	#public function __construct(){
	#	parent::__construct();
	#	$this->load->model('Venturo_models', 'venturo_models');
	#}

	public function hitung($key, $value){
		foreach ($value as $a => $ab) {
			$var['bulan'] = $a;
			$var['total'] = $ab;
			#$this->load->view('welcome_message', $var);
		}
	}

	public function buildData($result, $tahun = null)
	{
		$raw['menu'] = $result['menu'];
		$raw['transaksi'] = $result['transaksi'];
	}

	public function index()
	{
		$post = array(
			'tahun' => $this->input->post('tahun')
		);

		$menu = array(
			'url' => $this->path, 
			'perintah' => 'menu'
		);

		$data = array(
			'tahun' => $this->input->post('tahun')
		);

		if ($post['tahun'] == '2021') {
			$transaksi = array(
				'url' => $this->path,
				'perintah' => 'transaksi',
				'tahun' => '2021'
			);
			$jsonTransaksi = $this->Venturo_models->venturoApi($transaksi);
			$resultTransaksi = $this->Venturo_models->ekstrakData($jsonTransaksi, 'object');
		}elseif($data['tahun'] == '2022'){
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
		
		if ($post['tahun'] !== null) {
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
