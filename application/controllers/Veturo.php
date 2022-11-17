<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Veturo extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('venturo_models', 'Venturo_models');
	}
	public $path = 'http://tes-web.landa.id/intermediate/';

	public function tampil($key, $value = null){
		var_dump($key['hasil']);
	}

	public function buildData($result, $tahun = null)
	{
		$raw['menu'] = $result['menu'];
		$raw['transaksi'] = $result['transaksi'];

		$ttl = [];
		foreach ($raw['menu'] as $a) {
			echo '<br><hr>';
			foreach ($raw['transaksi'] as $b) {
				if ($b->menu == $a->menu) {
					$bln = $this->Venturo_models->getMonth($b->tanggal);
					$ttl[$bln][] = $b->total;
				}
			}

			foreach ($ttl as $c => $cd) {
				$d = $this->Venturo_models->hitung($cd);
				echo $c.' => '.$d.' <br>';
				$raw['hasil'] = $d;
			}
			$this->tampil($raw);
			$ttl = [];
		}
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
