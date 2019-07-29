<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 30/08/2018
 * Time: 15:14
 */

namespace App\Controllers;

use App\Kernel\Abstracts\ControllerAbstract;
use App\Kernel\Utils\Validation\Validation;
use App\Models\Orientation;
use App\Models\User;
use Slim\Http\Request;

class AuthController extends ControllerAbstract
{
    public function register()
    {
        $prefix = 'r_';
        $rules = User::$rules;
        $v = new Validation($this->inputs());
        $v->mapFieldsRules(prefixArray($prefix, $rules));
        $v->labels(keyValuePrefix($prefix, $rules));
        $v->rule('usernameAvailable', $prefix . 'username');
        $v->rule('emailAvailable', $prefix . 'email');
        // validation check
        if (!$v->validate()) {
            $this->addMessage(ERROR, FORM_ERROR);
            return $this->redirect('home', '?register');
        }
        // build user
        $user = new User();
        $user->username = $this->input($prefix . 'username');
        $user->email = $this->input($prefix . 'email');
        $user->first_name = $this->input($prefix . 'first_name');
        $user->last_name = $this->input($prefix . 'last_name');
        $user->password = $this->getService('auth')->hashPassword($this->input($prefix . 'password'));
        $user->sexe_id = $this->input($prefix . 'sexe_id');
        $user->orientation_id = Orientation::where('name', 'Bisexual')->getOne()->id;
        $user->confirmation = bin2hex(random_bytes(24));
        // add a record
        $user->save();
        // TODO: CHEKC IF new request resolve bug
        $this->addMessage(SUCCESS, 'Welcome to Matcha <b>' . $user->first_name . '</b> ! An email has been sent to <b>' . $user->email . '</b>.');
        $this->getService('mailer')->send($user->email, 'Welcome !', 'mails/welcome.twig', ['user' => $user]);
        return $this->redirect('home');
    }

    public function connexion()
    {
        // validation settings
        $prefix = 'c_';
        $rules = filterKeys(User::$rules, ['username', 'password']);
        $v = new Validation($this->inputs());
        $v->mapFieldsRules(prefixArray($prefix, $rules));
        $v->labels(keyValuePrefix($prefix, $rules));
        // validation check
        if (!$v->validate()) {
            $this->addMessage(ERROR, FORM_ERROR);
            return $this->redirect('home', '?connexion');
        }
        $user = User::where('username', $this->input($prefix . 'username'))->getOne();
        if (!$user) {
            $this->addMessage(ERROR, FORM_CANT_CONNECT);
            return $this->redirect('home', '?connexion');
        }
        if ($user->confirmation) {
            $this->addMessage(ERROR, NEED_TO_BE_CONFIRM);
            return $this->redirect('home', '?connexion');
        }
        // auth check
        if (!$this->getService('auth')->attempt($this->input($prefix . 'username'), $this->input($prefix . 'password'))) {
            $this->addMessage(ERROR, FORM_CANT_CONNECT);
            return $this->redirect('home', '?connexion');
        }

        $this->addMessage(SUCCESS, WELCOME);
        return $this->redirect('home');
    }

    public function logout()
    {
        $this->getService('auth')->logout();
        $this->addMessage(SUCCESS, LOGOUT);
        return $this->redirect('home');
    }

    public function reset(Request $request)
    {
        $method = $this->getRequest()->getMethod();

        // GET
        if ($method === "GET") {
            $id = $request->getAttribute('id');
            $user = User::where('reset', $id)->getOne();
            if ($user)
                return $this->render('pages/reset.twig', ['user' => $user]);
            $this->addMessage('error', ACTION_IMPOSSIBLE);
            return $this->redirect('home');
        }
        // POST
        $id = $request->getAttribute('id');
        $user = User::where('reset', $id)->getOne();
        if (!$user) {
            $this->addMessage('error', ACTION_IMPOSSIBLE);
            return $this->redirect('home');
        }
        $prefix = 'r_';
        $rules = [
            'password' => User::$rules['password'],
            'password_verify' => ['required', ['passwordVerification', $this->input($prefix . 'password')]]
        ];

        $v = new Validation($this->inputs());
        $v->mapFieldsRules(prefixArray($prefix, $rules));
        $v->labels(keyValuePrefix($prefix, $rules));
        // validation check
        if (!$v->validate()) {
            $this->addMessage(ERROR, FORM_ERROR);
            return $this->redirect('reset', ['id' => $id]);
        }

        $user->reset = null;
        $user->password = $this->getService('auth')->hashPassword($this->input($prefix . 'password'));
        $user->save();
        $this->addMessage('success', PASSWORD_CHANGED);
        $this->getService('mailer')->send($user->email, 'Password has been changed', 'mails/reset.twig', ['user' => $user]);
        return $this->redirect('home');
    }

    public function confirmation(Request $request)
    {
        $id = $request->getAttribute('id');
        if (!$id) {
            $this->addMessage(ERROR, CAN_T_ACCESS);
            return $this->redirect('home');
        }

        if (!($user = User::where('confirmation', $id)->getOne())) {
            $this->addMessage(ERROR, CAN_T_ACCESS);
            return $this->redirect('home');
        }

        $user->confirmation = null;
        $user->save();

        $this->addMessage(SUCCESS, "Your account has been confirmed!");
        return $this->redirect('home');


    }

    public function forgot()
    {
        $method = $this->getRequest()->getMethod();
        // GET
        if ($method === "GET")
            return $this->render('pages/forgot.twig');
        // POST
        $prefix = 'f_';
        $rules = filterKeys(User::$rules, ['email']);
        $v = new Validation($this->inputs());
        $v->mapFieldsRules(prefixArray($prefix, $rules));
        $v->labels(keyValuePrefix($prefix, $rules));
        // validation check
        if (!$v->validate()) {
            $this->addMessage(ERROR, FORM_ERROR);
            return $this->redirect('forgot');
        }

        $user = User::where('email', $this->input($prefix . 'email'))->with('sexe')->getOne();

        if (!$user) {
            $this->addMessage(ERROR, USER_NOT_EXIST);
            return $this->redirect('forgot');
        }

        if ($user->reset) {
            $this->addMessage(ERROR, ALREADY_RESET);
            return $this->redirect('forgot');
        }
        try {
            $user->reset = bin2hex(random_bytes(24));
        } catch (\Exception $e) {
        }

        $user->save();
        $this->getService('mailer')->send($user->email, 'Forgot password', 'mails/forgot.twig', ['user' => $user]);
        $this->addMessage('success', 'Instructions has been sent to <b>' . $user->email . '</b>!');
        return $this->redirect('home');
    }
}