<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Venturo extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('venturo_models', 'Venturo_models');
	}

	public $path = 'http://tes-web.landa.id/intermediate/';

	public function tampil($var = array()){
		$var['ttl'] = [];
		$var['ttlmenu'] = [];
		foreach ($var['menu'] as $a) {
			foreach ($var['ttl'] as $b => $bc) {
			var_dump($b);
			}
			$var['ttlmenu'] = [];
			$var['ttl'] = [];
			#var_dump($var['ttl']);
		}		
	}

	public function buildData($result){
		$raw['menu'] = $result['menu'];
		$raw['transaksi'] = $result['transaksi'];
		
		$total = [];
		$ttlmenu = [];
		
		foreach ($raw['menu'] as $a) {
			#echo '<br>'.$a->menu.'<br><hr>';
			foreach ($raw['transaksi'] as $b) {
				if ($b->menu === $a->menu) {
					# code...
					$mnu = $b->menu;
					$bulan = $this->Venturo_models->getMonth($b->tanggal);
					$total[$bulan][] = $b->total;
					$ttlmenu[$mnu][] = $b->total;
				}
			}
			
			foreach ($total as $c => $cd) {
				$sum = $this->Venturo_models->hitung($cd);
				if (strlen($c) > 3) {
					# code...
					$len = 3 - strlen($c);
					$bln = substr($c, 0, $len);
					$data = array(
						$bln => $sum
					);
					$raw['bulan'] = $data;
					echo '+ bulan '.$bln. '<br>';
				}else{
					$data = array(
						$c => $sum
					);
					$raw['bulan'] = $data;
					echo '+ bulan '.$c. '<br>';
				}
				$raw['res_month'] = $sum;
				echo '  '.$sum.'<br>';
			}
			
			foreach ($ttlmenu as $d => $de) {
				# code...
				$ttltahun = $this->Venturo_models->hitung($de);
				echo '<hr><b>penjualan menu '.$a->menu.' dalam setahun = '.$ttltahun. '</b><br><br>';
			}

			$raw['ttl'] = $total;
			$raw['ttlmenu'] = $ttlmenu;

			$ttlmenu = [];
			$total = [];
		}

		$this->tampil($raw);
		$this->load->view('venturo', $raw);
	}

	public function index()
	{
		// original code.
		#$this->getData('menu', '2021');
		// original code.
		$post = array(
			'tahun' => $this->input->post('tahun')
		);

		$menu = array(
			'url' => $this->path,
			'perintah' => 'menu'
		);

		if ($post['tahun'] == '2021') {
			# code...
			$transaksi = array(
				'url' => $this->path,
				'perintah' => 'transaksi',
				'tahun' => '2021'
			);
			$jsonTransaksi = $this->Venturo_models->venturoApi($transaksi);
			$resultTransaksi = $this->venturo_models->ekstrakData($jsonTransaksi, 'object');
		}elseif($post['tahun'] == '2022') {
			$transaksi = array(
				'url' => $this->path,
				'perintah' => 'transaksi',
				'tahun' => '2022'
			);
			$jsonTransaksi = $this->Venturo_models->venturoApi($transaksi);
			$resultTransaksi = $this->venturo_models->ekstrakData($jsonTransaksi, 'object');
		}else{
			$this->load->view('VenturoTest', null);
		}
		
		$jsonMenu = $this->Venturo_models->venturoApi($menu);
		$resultMenu = $this->venturo_models->ekstrakData($jsonMenu, 'object');

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
?>