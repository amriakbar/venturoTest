<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function index()
	{
		$path = 'https://tes-web.landa.id/intermediate/';
		$ambilData = file_get_contents($path.'menu');
		$transaksi = file_get_contents($path.'transaksi?tahun='.'2021');
		$ekstrak_datanya['ekstrak_menu'] = json_decode($ambilData);
		$ekstrak_datanya['ekstrak_transaksi'] = json_decode($transaksi);
		#var_dump($ekstrak_datanya);

		$this->load->view('welcome_message', $ekstrak_datanya);
	}
}
