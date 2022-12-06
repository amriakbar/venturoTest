<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Veturo extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('venturo_models', 'Venturo_models');
		$this->load->library('table');
	}
	public $path = 'http://tes-web.landa.id/intermediate/';

	public function tampil($value = null){
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
		return $this->table->set_template($template);
		//$this->table->set_heading('#', $bln);
		//$row = $this->table->add_row($key['menu'], $hasil);
		#$this->table->add_row($menu, $total);
		//$this->table->make_columns($row, 12);
		#var_dump($bln);
		#echo $this->table->generate();

	}

	public function buildData($result)
	{
		$this->tampil();
		$raw['menu'] = $result['menu'];
		$raw['transaksi'] = $result['transaksi'];
		$menu= $result['menu'];
		$transaksi = $result['transaksi'];

		$ttl = [];
		$ttlmn = [];
		foreach ($raw['menu'] as $a) {
			#$raw['menu'] = $a->menu;
			$this->table->add_row([$a->menu]);
			foreach ($raw['transaksi'] as $b) {
				if ($b->menu == $a->menu) {
					$mn = $b->menu;
					$bln = $this->Venturo_models->getMonth($b->tanggal);
					$ttl[$bln][] = $b->total;
					$ttlmn[$mn][] = $b->total;
				}
				#var_dump(strpos($b->menu, $a->menu));
			}
			$menu = $a->menu;
			$kategori[$menu][] = $ttl;

			foreach ($ttl as $c => $cd) {
				$hasil = $this->Venturo_models->hitung($cd);
			}

			foreach ($ttlmn as $d => $de){
				$sum = $this->Venturo_models->hitung($de);
			}
			$this->table->function = 'htmlspecialchars';
			
			$ttlmn = [];
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
