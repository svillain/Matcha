<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 27/08/2018
 * Time: 01:32
 */

namespace App\Controllers;

use App\Kernel\Abstracts\ControllerAbstract;
use App\Kernel\BaseServices\Database;
use App\Models\Interest;
use App\Models\Photo;
use App\Models\Sexe;
use App\Models\User;
use App\Models\UserInterest;
use Faker\Factory;


class HomeController extends ControllerAbstract
{
    public function index(\Slim\Http\Request $request)
    {
        // USER NOT CONNETED
        if (!$this->getService('auth')->check())
            return $this->render('pages/home.twig', ['sexes' => Sexe::get()]);

        // USER CONNECTED
        $user = User::where('users.id', $_SESSION['user'])
            ->with('photos')
            ->with('interests')
            ->getOne();
        $uncompleted = [];

        if ($user->biography === null || empty($user->biography))
            $uncompleted['biography'] = 'Empty biography';
        if (empty($user->interests))
            $uncompleted['interests'] = 'No interests';
        if (empty($user->birthdate))
            $uncompleted['birthdate'] = 'Empty birthdate';
        if (!$user->photos)
            $uncompleted['photos_1'] = 'No photos upload';
        if (!$user->hasPhotoProfil())
            $uncompleted['photos_2'] = 'No profil photo set';

        if (count($uncompleted) > 0)
            return $this->render('pages/app.twig', ['uncompleted' => $uncompleted]);

        $b = explode('-', $user->birthdate);
        $age = (date("md", date("U", (int)mktime(0, 0, 0, (int)$b[0]))) > (int)date("md")
            ? ((int)(date("Y") - (int)$b[0]) - 1)
            : ((int)date("Y") - (int)$b[0]));
        return $this->render('pages/app.twig', ['interests' => Interest::get(), 'userInfos' => ['age' => $age, 'interests' => array_column($user->interests, 'data')]]);
    }

    private function _setup_database()
    {
        $db = \App\Kernel\BaseServices\Database::getInstance();

        if (env('SEED_FILE')) {
            $db->executeFile(base_path() . '/seed.sql');
            d('File seed.sql has been executed on database ' . env('DB_BASE'));
        } else {
            $db->executeFile(base_path() . '/matcha.sql');
            d('File matcha.sql has been executed on database ' . env('DB_BASE'));
        }
    }

    private function _setup_seeds()
    {
        function ashuffle(&$arr)
        {
            uasort($arr, function ($a, $b) {
                return rand(-1, 1);
            });
        }

        $sexes = [1, 2];
        $orientations = [1, 2, 3];
        $faker = Factory::create();
        $gps_range = [
            // Paris
            [
                'lng' => [2.247535, 2.421441],
                'lat' => [48.808405, 48.959216]
            ],
            // Lyon
            [
                'lng' => [4.73643, 4.997355],
                'lat' => [45.673333, 45.81565]
            ],
            // Toulouse
            [
                'lng' => [0.747418, 2.208599],
                'lat' => [43.195858, 43.255898]
            ]
        ];

        $pwd = "cd62cb0157a8d09a8c864df39cba7c8fede23541d636f6cf81edf20858b39ef61bcb04e9002eaa6304f01869be00e73c359a96a157c83fc25c2a90ab80194717"; // @Zerty92

        for ($i = 0; $i < 750; $i++) {
            // USERS TABLE
            shuffle($sexes);
            shuffle($orientations);
            $city_id = mt_rand(0, 2);

            $user = new User();
            $user->username = $faker->userName;
            $user->email = $faker->email;
            $user->password = $pwd;
            $user->first_name = $faker->firstName(($sexes[0] === 1) ? 'male' : 'female');
            $user->last_name = $faker->lastName;
            $user->biography = $faker->realText();
            $user->sexe_id = $sexes[0];
            $user->orientation_id = $orientations[0];
            $user->birthdate = date('Y-m-d', mt_rand(0, 946684825));

            $user->latitude = $faker->latitude($gps_range[$city_id]['lat'][0], $gps_range[$city_id]['lat'][1]);
            $user->longitude = $faker->longitude($gps_range[$city_id]['lng'][0], $gps_range[$city_id]['lng'][1]);
            $user->online = 0;
            $user->ip = $faker->ipv4;
            $user->disconnected_at = date('Y-m-d H:i:s');
            $user->save();

            // PHOTOS TABLE
            $sexe_label = $user->sexe_id === 1 ? "man" : "woman";
            // profil
            $profil = new Photo();
            $profil->user_id = $user->id;
            $profil->fileName = 'seeds/' . $sexe_label . '/' . mt_rand(0, 94) . '.jpg';
            $profil->profil = 1;
            $profil->save();
            unset($profil);
            // 4
            $photos_nb = mt_rand(1, 4);
            for ($k = 0; $k < $photos_nb; $k++) {
                $photo = new Photo();
                $photo->user_id = $user->id;
                $photo->fileName = 'seeds/random/' . mt_rand(0, 29) . '.jpg';
                $photo->profil = 0;
                $photo->save();
                unset($photo);
            }

            $interests_id = Database::getInstance()->rawQueryValue("SELECT DISTINCT(id) FROM interests ORDER BY RAND() LIMIT 1, ?", [mt_rand(1, 12)]);
            foreach ($interests_id as $v) {
                $user_interest = new UserInterest();
                $user_interest->user_id = $user->id;
                $user_interest->interest_id = $v;
                $user_interest->save();
                unset($user_interest);
            }
            unset($user);
        }

        d('Seed has been push in the database');
    }

    public function setup()
    {
        $path = base_path() . '/public/' . env('UPLOAD_DIR');
        $files = glob($path . '/*'); // get all file names
        $user = $this->getService('auth')->user();
        if ($user) {
            unset($_SESSION['user']);
        }

        foreach ($files as $file) { // iterate files
            if (is_file($file))
                unlink($file); // delete file
        }
        $this->_setup_database();
        if (!env('SEED_FILE'))
            $this->_setup_seeds();

        return $this->getResponse()->getBody()->write('Setup done, <a href="/">back</a>');
    }
}