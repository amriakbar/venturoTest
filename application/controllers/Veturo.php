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
	public function buildData($perintah, $tahun = null)
	{
		$th = $tahun;
		$method = $perintah;
		if ($th != null) {
			$ekstrak_datanya['menu'] = file_get_contents($this->path. $method);
			$ekstrak_datanya['transaksi'] = file_get_contents($this->path. 'transaksi?tahun='. $th);
			$this->load->view('welcome_message', $ekstrak_datanya);

			$jsonMenu = json_decode($ekstrak_datanya['menu']);
			$jsonTransaksi = json_decode($ekstrak_datanya['transaksi']);
			foreach ($jsonTransaksi as $b) {
				$time = strtotime($b->tanggal);
				$bl = date('F', $time);
				$tb_transaksi = array(
					'menu' => $b->menu,
					'tanggal' => $bl,
					'total' => $b->total
				);
			}

			$total = [];
			foreach ($jsonMenu as $menu) {
				foreach ($jsonTransaksi as $transaksi) {
					if ($transaksi->menu === $menu->menu) {
						$tgl = strtotime($transaksi->tanggal);
						$bulan = date('F', $tgl);
						$total[$bulan][] = $transaksi->total;					
					}
				}
				$tb_mnu = array(
					'menu' => $menu->menu,
					'kategori' => $menu->kategori
				);
				foreach ($total as $key => $val) {
					$mnu = $menu->menu;
					if (strlen($key) > 3) {
						$len = 3 - strlen($key);
						$bln = substr($key, 0, $len);
						$data = array(
							$bln => array_sum($val)
						);
						$this->hitung($mnu, $data);
					} else {
						$data = array(
							$key => array_sum($val)
						);
						$this->hitung($mnu, $data);
					}
				}
				$total = [];
			}
		}else{
			$ambilData = file_get_contents($this->path. $method);
			$ekstrak_datanya = json_decode($ambilData);
			$this->load->view('welcome_message', $ekstrak_datanya);
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
		}
	}
}
