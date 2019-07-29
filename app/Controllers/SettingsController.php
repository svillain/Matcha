<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 30/08/2018
 * Time: 17:25
 */

namespace App\Controllers;

use App\Kernel\Abstracts\ControllerAbstract;
use App\Kernel\Abstracts\ModelAbstract;
use App\Kernel\BaseServices\Database;
use App\Kernel\Utils\Validation\Validation;
use App\Models\Interest;
use App\Models\Like;
use App\Models\Orientation;
use App\Models\Sexe;
use App\Models\User;
use App\Models\UserInterest;
use Symfony\Component\VarDumper\Cloner\Data;


class SettingsController extends ControllerAbstract
{
    public function index()
    {
        return $this->render('pages/settings.twig', [
            'user' => User::where('users.id', $_SESSION['user'])
                ->with('orientation')
                ->with('sexe')
                ->with('interests')
                ->getOne(),
            'interests' => Interest::get(),
            'sexes' => Sexe::get(),
            'orientations' => Orientation::get()
        ]);
    }

    public function save()
    {

        $rules = filterKeys(User::$rules, ['password'], false);
        $v = new Validation($this->inputs());
        $v->mapFieldsRules($rules);
        $v->rule('emailAvailable', 'email');
        $v->labels(keyValuePrefix('', $rules));
        // validation check
        if (!$v->validate()) {
            $this->addMessage(ERROR, FORM_ERROR);
            return $this->redirect('settings');
        }
        $user = User::where('users.id', $_SESSION['user'])
            ->with('orientation')
            ->with('sexe')
            ->with('interests')
            ->getOne();
        $user->first_name = $this->input('first_name');
        $user->last_name = $this->input('last_name');
        $user->username = $this->input('username');
        $user->email = $this->input('email');
        $user->biography = $this->input('biography');
        $user->orientation_id = $this->input('orientation');
        $user->birthdate = (!empty($this->input('birthdate'))) ? $this->input('birthdate') : null;
        $user->longitude = $this->input('longitude');
        $user->latitude = $this->input('latitude');
        $user->sexe_id = $this->input('sexe');
        if ($userInterests = UserInterest::where('user_id', $user->id)->get())
            foreach ($userInterests as $k => $v)
                /**
                 * @var $v ModelAbstract
                 */
                $v->delete();
        if ($interests = $this->input('interests')) {
            foreach ($interests as $k => $v) {
                $userInterest = new UserInterest();
                $userInterest->user_id = $user->id;
                $userInterest->interest_id = $v;
                $userInterest->save();
            }
        }
        unset($userInterest);
        $user->save();
        $this->addMessage(SUCCESS, 'Settings updated!');
        return $this->redirect('settings');
    }

    public function changePassword()
    {
        $rules = [
            'old_password' => ['required', 'passwordUser'],
            'new_password' => User::$rules['password'],
            'new_password_verification' => ['required', ['passwordVerification', $this->input('new_password')]]
        ];
        $v = new Validation($this->inputs());
        $v->mapFieldsRules($rules);
        // validation check
        if (!$v->validate()) {
            $this->addMessage(ERROR, FORM_ERROR);
            return $this->redirect('settings', '?password');
        }
        if ($this->input('new_password') === $this->input('old_password')) {
            $this->addMessage(ERROR, SAME_PASSWORD);
            return $this->redirect('settings', '?password');
        }
        $user = User::where('id', $_SESSION['user'])->getOne();
        $user->password = $this->getService('auth')->hashPassword($this->input('new_password'));
        $user->save();
        $this->addMessage('success', PASSWORD_CHANGED);
        return $this->redirect('settings');
    }
}