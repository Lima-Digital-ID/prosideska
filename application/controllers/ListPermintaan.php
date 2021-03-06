<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ListPermintaan extends CI_Controller
{

    private $data;

    public function __construct()
    {
        parent::__construct();
        is_login();
        $this->load->library('Pdf');
        $this->load->model('PermintaanSurat_model');
        $this->load->model('Jenis_surat_model');
        $this->load->model('CetakSurat_model');
        $this->load->model('Penduduk_model');
        $this->load->model('NotifikasiPermintaan_model');
        $this->data['page_title']  = $_SESSION['level'] == 'kepaladesa' ? 'Pengesahan Surat' : 'Permintaan Surat';
    }

    public function index()
    {
        $jenis = $this->input->get('jenis');

        $this->data['data'] = $this->PermintaanSurat_model->getAllData($jenis);
        $this->data['jenisSurat'] = $this->Jenis_surat_model->getAllData();

        $this->template->load('template', 'list_permintaan/index', $this->data);
    }

    public function terima($id, $nik)
    {
        $this->db->select('permintaan_surat.*, jenis_surat.jenis');
        $this->db->join('jenis_surat', 'jenis_surat.id = permintaan_surat.id_jenis_surat');
        $jenisSurat = $this->db->get_where('permintaan_surat', array('permintaan_surat.id' => $id))->row_array();

        if ($jenisSurat['jenis'] == 'Surat Keterangan Perjalanan') {
            $this->data['id'] = $id;
            $this->data['jenis'] = $jenisSurat['jenis'];
            $this->data['nik'] = $nik;

            $this->template->load('template', 'list_permintaan/form-surat', $this->data);
        } else {
            if ($this->PermintaanSurat_model->terima($id, $nik)) {
                $this->session->set_flashdata('pesan', 'surat berhasil diterima');
                redirect('listpermintaan/index');
            } else {
                $this->template->load('template', 'list_permintaan/index');
            }
        }
    }

    public function submitSuratBepergian($id)
    {
        $this->db->select('permintaan_surat.form_data');
        $surat = $this->db->get_where('permintaan_surat', array('id' => $id))->row_array();
        // var_dump($surat);
        $formData = json_decode($surat['form_data']);
        $formData->berlaku_hingga = $this->input->post('berlaku_hingga');

        $formData = json_encode($formData);
        $this->data = array(
            'status' => 'diproses',
            'id_admin' => $_SESSION['id'],
            'form_data' => $formData,
        );

        $this->db->where('id', $id);

        $this->db->update('permintaan_surat', $this->data);

        if ($this->PermintaanSurat_model->storeNotification($this->input->post('nik'), 'Pengesahan Surat anda sudah diterima.')) {
            $this->session->set_flashdata('pesan', 'surat berhasil diterima');
            redirect('listpermintaan/index');
        } else {
            $this->template->load('template', 'list_permintaan/index');
        }
    }

    public function tolak($id, $nik)
    {
        if ($this->PermintaanSurat_model->tolak($id, $nik)) {
            $this->session->set_flashdata('pesan', 'surat berhasil diterima');
            redirect('listpermintaan/index');
        } else {
            $this->template->load('template', 'listpermintaan/index');
        }
    }

    public function done($id, $nik)
    {
        if ($this->PermintaanSurat_model->done($id, $nik)) {
            $this->session->set_flashdata('pesan', 'surat berhasil diterima');
            redirect('listpermintaan/index');
        } else {
            $this->template->load('template', 'listpermintaan/index');
        }
    }

    public function printPdf($id, $jenisSurat, $nik)
    {
        $penduduk = $this->Penduduk_model->getPenduduk($nik);

        $jenisSurat = str_replace('-', ' ', $jenisSurat);

        $currentNo = $this->PermintaanSurat_model->getNoUrut($id);
        $newNo = 0;

        if ($currentNo == 0) {
            // update
            $newNo = $this->PermintaanSurat_model->generateNoUrut();

            $this->PermintaanSurat_model->setNoUrut($id, $newNo);
        } else
            $newNo = $currentNo;

        if ($jenisSurat == 'surat pindah')
            $this->CetakSurat_model->suratPindah($jenisSurat, $newNo);
        elseif ($jenisSurat == 'surat tidak mampu')
            $this->CetakSurat_model->suratTidakMampu($id, $penduduk, $newNo);
        elseif ($jenisSurat == 'surat kematian')
            $this->CetakSurat_model->suratKematian($id, $this->data, $penduduk, $newNo);
        elseif ($jenisSurat == 'surat kuasa')
            $this->CetakSurat_model->suratKuasa($id, $penduduk, $newNo);
        elseif ($jenisSurat == 'surat usaha')
            $this->CetakSurat_model->suratUsaha($id, $penduduk, $newNo);
        elseif ($jenisSurat == 'surat kelahiran') {
            // $page1 = $this->load->view('cetak/surat-kelahiran', '', true);
            // $html = [$page1];
            // $this->CetakSurat_model->printFromView($html, count($html));
            $this->CetakSurat_model->suratKelahiran($id, $penduduk, $newNo);
            // } elseif ($jenisSurat == 'surat kematian') {
            //     $page1 = $this->load->view('cetak/surat-kematian', '', true);
            //     $page2 = $this->load->view('cetak/surat-kematian2', '', true);
            //     $html = [$page1, $page2];

            //     $this->CetakSurat_model->printFromView($html, count($html));
        } elseif ($jenisSurat == 'surat keterangan cacatan kepolisian') {
            // $page1 = $this->load->view('cetak/surat-skck', '', true);
            // $page2 = $this->load->view('cetak/surat-skck2', '', true);
            // $page3 = $this->load->view('cetak/surat-skck3', '', true);
            // $html = [$page1, $page2, $page3];

            // $this->CetakSurat_model->printFromView($html, count($html));
            $this->CetakSurat_model->skck($id, $nik, $penduduk, $newNo);
        } elseif ($jenisSurat == 'surat keterangan perjalanan') {
            $this->CetakSurat_model->suketPerjalanan($id, $nik, $penduduk, $newNo);
        } elseif ($jenisSurat == 'surat keterangan belum menikah') {
            $this->CetakSurat_model->suketBelumMenikah($id, $nik, $penduduk, $newNo);
        } elseif ($jenisSurat == 'surat keterangan kehilangan') {
            $this->CetakSurat_model->suketPengantarKehilangan($id, $nik, $penduduk, $newNo);
        } elseif ($jenisSurat == 'surat perwalian') {
            $this->CetakSurat_model->suketPerwalian($id, $nik, $penduduk, $newNo);
        } elseif ($jenisSurat == 'surat keterangan pindah') {
            $this->CetakSurat_model->suratPindah($id, $nik, $penduduk, $newNo);
        }
    }

    // public function edit($id){
    //     $data = $this->PermintaanSurat_model->edit($id);
    //     $this->template->load('template', 'list_permintaan/form-surat', $data);
    // }
    public function update($id)
    {
        // $id =  $this->uri->segment(3);
        $this->data['data'] = $this->PermintaanSurat_model->edit($id);
        $this->template->load('template', 'list_permintaan/edit', $this->data);
        // echo json_encode($data);
    }
    public function updatedata($id)
    {
        $id = $this->uri->segment(3);
        foreach ($_POST as $key => $value) {
            $form[$key] = isset($value) ? $value : '-';
        }

        $json = json_encode($form);
        if ($this->PermintaanSurat_model->updateData($id, $json)) {
            $this->session->set_flashdata('pesan', 'Pengajuan berhasil dikirim');

            echo '<script type="text/javascript">alert("Berhasil mengirim permintaan");window.location="' . base_url() . 'listpermintaan/index"</script>';
        }
    }
}
