<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SyaratSurat extends CI_Controller
{

  private $data;

  public function __construct()
  {
    parent::__construct();
    is_login();
    $this->load->model('SyaratSurat_model');
    $this->load->model('Jenis_surat_model');
    $this->load->model('NotifikasiPermintaan_model');
    $this->data['page_title']  = 'Master Alur / Syarat Surat';
  }

  function _rules()
  {
    $this->form_validation->set_rules(
      'jenis_surat',
      'Jenis surat',
      'required',
      array(
        'required' => 'Data harus dipilih.'
      )
    );
    $this->form_validation->set_rules(
      'syarat_surat',
      'Syarat surat',
      'required',
      array(
        'required' => 'Data harus dipilih.'
      )
    );
  }

  public function index()
  {
    $this->data['data'] = $this->SyaratSurat_model->getAllData();
    $this->template->load('template', 'syarat_surat/index', $this->data);
  }

  public function create()
  {
    $this->data['jenis'] = $this->Jenis_surat_model->getAllData();
    $this->template->load('template', 'syarat_surat/create', $this->data);
  }

  public function store()
  {
    $this->_rules();

    if ($this->form_validation->run() == TRUE) {
      if ($this->SyaratSurat_model->insertData()) {
        $this->session->set_flashdata('pesan', 'data berhasil disimpan');
        redirect('syaratsurat/index');
      }
    } else {
      $this->template->load('template', 'syarat_surat/create');
    }
  }

  public function edit($id)
  {
    $this->data['jenis'] = $this->Jenis_surat_model->getAllData();
    $this->data['data'] = $this->SyaratSurat_model->editData($id);
    $this->template->load('template', 'syarat_surat/edit', $this->data);
  }

  public function update($id)
  {
    $this->_rules();

    if ($this->form_validation->run() == TRUE) {
      if ($this->SyaratSurat_model->updateData($id)) {
        $this->session->set_flashdata('pesan', 'data berhasil di edit!');
        redirect('syaratsurat/index');
      }
    } else {
      $this->data['dataJenis'] = $this->SyaratSurat_model->editData($id);
      $this->template->load('template', 'syarat_surat/edit', $this->data);
    }
  }

  public function delete($id)
  {
    $this->SyaratSurat_model->deleteData($id);
    $this->session->set_flashdata('pesan', 'data berhasil di hapus!');
    redirect('syaratsurat/index');
  }
}


/* End of file JenisSurat.php */
/* Location: ./application/controllers/JenisSurat.php */
