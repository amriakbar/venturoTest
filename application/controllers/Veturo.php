<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Veturo extends CI_Controller {
	public $path = 'http://tes-web.landa.id/intermediate/';

	#public function __construct(){
	#	parent::__construct();
	#	$this->load->model('Venturo_models', 'venturo_models');
	#}

	public function table($value='')
	{
		$template = array(
        'table_open'            => '<table class="table table-hover table-bordered" style="margin: 0;">',

        'thead_open'            => '<thead>',
        'thead_close'           => '</thead>',

        'heading_row_start'     => '<tr class="table-dark">',
        'heading_row_end'       => '</tr>',
        'heading_cell_start'    => '<th style="text-align: center;width: 75px;">',
        'heading_cell_end'      => '</th>',

        'tbody_open'            => '<tbody>',
        'tbody_close'           => '</tbody>',

        'row_start'             => '<tr>',
        'row_end'               => '</tr>',
        'cell_start'            => '<td class="table-secondary" colspan="14">',
        'cell_end'              => '</td>',

        'row_alt_start'         => '<tr>',
        'row_alt_end'           => '</tr>',
        'cell_alt_start'        => '<td class="table-secondary" colspan="14">',
        'cell_alt_end'          => '</td>',

        'table_close'           => '</table>'
		);

		$this->table->set_template($template);
		// code...

		$this->table->function = 'htmlspecialchars';
		echo $this->table->generate();
	}

	public function hitung($key, $value){
		foreach ($value as $a => $ab) {
			$var['bulan'] = $a;
			$var['total'] = $ab;
			#$this->load->view('welcome_message', $var);
		}
	}
	public function getData($perintah, $tahun = null)
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
		$data = array(
			'tahun' => $this->input->post('tahun')
		);
		$a = null;
		if ($data['tahun'] == '2021') {
			$this->getData('menu','2021');
		}elseif($data['tahun'] == '2022'){
			$this->getData('menu','2022');
		}else{
			$this->load->view('VenturoTest', null);
		}
	}
}
