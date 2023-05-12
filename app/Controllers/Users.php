<?php namespace App\Controllers;

use App\Models\UserModel;


class Users extends BaseController
{
    public function login()
    {
        $data = [];
        helper(['form']);

		
        if ($this->request->getMethod() == 'post') {
            //let's do the validation here
            $rules = [
                'username' => 'required|min_length[3]|max_length[20]',
                'email' => 'required|min_length[6]|max_length[50]|valid_email',
                'password' => 'required|min_length[8]|max_length[255]|validateUser[email,password]',
            ];

			// $uname = $this -> input -> post('username');
			// print($uname);

            $errors = [
                'password' => [
                    'validateUser' => 'Email or Password don\'t match'
                ]
            ];

            if (! $this->validate($rules, $errors)) {
                $data['validation'] = $this->validator;
				return view('login', $data);
            }else{
                $model = new UserModel();

                $model = new UserModel();
				$user = $model->where('email', $this->request->getVar('email'))->first();
				$this->setUserSession($user);
				return redirect()->to('/adminhome/');
				

            }
        }

        echo view('login', $data);
    }

    private function setUserSession($user){
        $data = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'isLoggedIn' => true,
        ];

        session()->set($data);
        return true;
    }

    public function register(){
        $data = [];
        helper(['form']);

        if ($this->request->getMethod() == 'post') {
            //let's do the validation here
            $rules = [
                'username' => 'required|min_length[3]|max_length[20]',
                'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[8]|max_length[255]',
                'password_confirm' => 'matches[password]',
            ];

            if (! $this->validate($rules)) {
                $data['validation'] = $this->validator;
            }else{
                $model = new UserModel();

                $newData = [
                    'username' => $this->request->getVar('username'),
                    'email' => $this->request->getVar('email'),
                    'password' => $this->request->getVar('password'),
                ];
                $model->save($newData);
                $session = session();
                $session->setFlashdata('success', 'Successful Registration');
                return redirect()->to('/login');


            }
        }


        echo view('register', $data);
    }

    public function profile(){
        
        $data = [];
        helper(['form']);
        $model = new UserModel();

        if ($this->request->getMethod() == 'post') {
            //let's do the validation here
            $rules = [
                'username' => 'required|min_length[3]|max_length[20]',
            ];

            if($this->request->getPost('password') != ''){
                $rules['password'] = 'required|min_length[8]|max_length[255]';
                $rules['password_confirm'] = 'matches[password]';
            }


            if (! $this->validate($rules)) {
                $data['validation'] = $this->validator;
            }else{

                $newData = [
                    'id' => session()->get('id'),
                    'username' => $this->request->getPost('username'),
                    ];
                    if($this->request->getPost('password') != ''){
                        $newData['password'] = $this->request->getPost('password');
                    }
                $model->save($newData);

                session()->setFlashdata('success', 'Successfuly Updated');
                return redirect()->to('/profile');

            }
        }

        $data['user'] = $model->where('id', session()->get('id'))->first();
        echo view('profile', $data);
    }

    public function logout(){
        session()->destroy();
        return redirect()->to('/');
    }

    //--------------------------------------------------------------------

}