<?php
class Authentifikasi extends CI_Controller
{
    public function index()
    {
        //jika statusnya sudah login, maka tidak bisa mengakses
        //halaman login alias dikembalikan ke tampilan user
        if($this->session->userdata('email')){
        redirect('user');
        }
            $this->form_validation->set_rules('email', 'Alamat Email',
            'required|trim|valid_email', [
            'required' => 'Email Harus diisi!!',
            'valid_email' => 'Email Tidak Benar!!'
        ]);
            $this->form_validation->set_rules('password', 'Password',
            'required|trim', [
            'required' => 'Password Harus diisi'
        ]);
        if ($this->form_validation->run() == false) {
        $data['judul'] = 'Login';
        $data['user'] = '';
            //kata 'login' merupakan nilai dari variabel judul dalam array $data dikirimkan ke view aute_header
            $this->load->view('template/authe_header', $data);
            $this->load->view('Authentifikasi/login', $data);
            $this->load->view('template/authe_footer', $data);
            } else {
                $this->_login();
            }
    }
    
    private function _login()
    {
        $email = htmlspecialchars($this->input->post('email',
        true));
        $password = $this->input->post('password', true);
        $user = $this->ModelUser->cekData(['email' => $email])->row_array();
        //jika usernya ada
        if ($user) {
        //jika user sudah aktif
        if ($user['is_active'] == 1) {
        //cek password
        if (password_verify($password, $user['password'])) {
            $data = [
            'email' => $user['email'],
            'role_id' => $user['role_id']
            ];
            $this->session->set_userdata($data);
            if ($user['role_id'] == 1) {
            redirect('admin');
            } else {
            if ($user['image'] == 'default.jpg') {
            $this->session->set_flashdata('pesan',
            '<div class="alert alert-info alert-message" role="alert">Silahkan
            Ubah Profile Anda untuk Ubah Photo Profil</div>');
        }
        redirect('user');
        }
            } else {
                $this->session->set_flashdata('pesan', '<div
                class="alert alert-danger alert-message" role="alert">Password
                salah!!</div>');
                redirect('Authentifikasi');
            }
        } else {
            $this->session->set_flashdata('pesan', '<div
            class="alert alert-danger alert-message" role="alert">User belum
            diaktifasi!!</div>');
            redirect('Authentifikasi');
        }
            } else {
                $this->session->set_flashdata('pesan', '<div
                class="alert alert-danger alert-message" role="alert">Email tidak
                terdaftar!!</div>');
                redirect('Authentifikasi');
            }
    }
    public function blok() { $this->load->view('Authentifikasi/blok'); }
    public function gagal() { $this->load->view('Authentifikasi/gagal'); }


    public function registrasi()
    {
        if ($this->session->userdata('email')) { redirect('user'); }
        $this->form_validation->set_rules(
            'nama', 
            'Nama Lengkap',
            'required', [
                'required' => 'Nama Belum diisi!!'
        ]);
        $this->form_validation->set_rules(
            'email', 
            'Alamat Email',
            'required|trim|valid_email|is_unique[user.email]', [
                'valid_email' => 'Email Tidak Benar!!',
                'required' => 'Email Belum diisi!!',
                'is_unique' => 'Email Sudah Terdaftar!'
        ]);
        $this->form_validation->set_rules(
            'password1',
            'Password',
            'required|trim|min_length[3]|matches[password2]', [
                'matches' => 'Password Tidak Sama!!',
                'required' => 'Password Harus diisi',
                'min_length' => 'Password Terlalu Pendek'
        ]);
        $this->form_validation->set_rules(
            'password2', 
            'RepeatPassword',
            'required|trim|matches[password1]',[
                'matches' => 'Password Tidak Sama!!',
                'required' => 'Password Harus diisi',
                'min_length' => 'Password Terlalu Pendek'
            ]
        );
        if ($this->form_validation->run() == false) 
        {
            $data['judul'] = 'Registrasi Member';
            $this->load->view('template/authe_header', $data);
            $this->load->view('Authentifikasi/registrasi');
            $this->load->view('template/authe_footer');
        } else {
            $email = $this->input->post('email', true);
            $data = [
                'nama' => htmlspecialchars($this->input->post('nama', true)),
                'email' => htmlspecialchars($email),
                'image' => 'default.jpg',
                'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
                'role_id' => 2,
                'is_active' => 1,
                'tanggal_input' => time()
            ];
            $this->ModelUser->simpanData($data); //menggunakan model

            $this->session->set_flashdata('pesan', '<div class="alert alert-success alert-message" role="alert">Selamat!! akun member anda sudah dibuat. Silahkan Aktivasi Akun anda</div>');
            redirect('Authentifikasi');
        }
    }
    public function logout()
    {
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('role_id');

        $this->session->set_flashdata('pesan', '<div class="alert alert-success alert-message" role="alert">Anda telah logout!!</div>');
        redirect('Authentifikasi');
    }
}