<?php
/**
 * Created by PhpStorm.
 * User: Ashk
 * Date: 15/09/2018
 * Time: 18:42
 */

namespace App\Controllers;

use App\Kernel\Abstracts\ControllerAbstract;
use App\Models\Photo;
use App\Models\User;
use Slim\Http\UploadedFile;

class PhotosController extends ControllerAbstract
{
    public function index()
    {
        return $this->render('pages/photos.twig',
            ['photos' => Photo::where('user_id', $_SESSION['user'])
                ->orderBy('created_at', 'desc')
                ->get()]);
    }

    public function save()
    {
        $count = Photo::where('user_id', $_SESSION['user'])->getValue('count(*)');
        if ($count === 5)
            return $this->writeJson(UPLOAD_MAX_PHOTOS, 403);
        // check data receive
        if (!isset($this->getRequest()->getUploadedFiles()['photo']))
            return $this->writeJson(UPLOAD_IMPOSSIBLE, 403);
        /**
         * @var $file UploadedFile
         */

        $file = $this->getRequest()->getUploadedFiles()['photo'];

        if ($file->getError() != 0)
            return $this->writeJson(UPLOAD_IMPOSSIBLE, 403);
        if (!in_array($file->getClientMediaType(), ['image/jpeg', 'image/png']))
            return $this->writeJson(UPLOAD_IMPOSSIBLE, 403);

        if(!@is_array(@getimagesize($file->file)))
            return $this->writeJson(UPLOAD_IMPOSSIBLE, 403);

        $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $photo = new Photo();
        $photo->user_id = $_SESSION['user'];
        $photo->fileName = $filename;
        $photo->profil = empty(Photo::where('user_id', $_SESSION['user'])->getOne()) ? true : false;
        $photo->save();
        $file->moveTo($this->getService('settings')['upload_dir'] . DIRECTORY_SEPARATOR . $filename);
        $this->addMessage(SUCCESS, UPLOAD_OK);
        return $this->writeJson(['photo' => $photo], 200);
    }

    public function delete(\Slim\Http\Request $request)
    {
        $id = $request->getAttribute('id');
        $count = Photo::where('user_id', $_SESSION['user'])->getValue('count(*)');
        if ($count === 1) {
            $this->addMessage(ERROR, UPLOAD_CAN_T_REMOVE);
            return $this->redirect('photos');
        }
        $photo = Photo::where('id', $id)->where('user_id', $_SESSION['user'])->getOne();
        if (!$photo) {
            $this->addMessage(ERROR, UPLOAD_NOT_EXIST);
            return $this->redirect('photos');
        }
        $profil = $photo->profil;
        $file = $this->getService('settings')['upload_dir'] . '/' . $photo->fileName;
        if (file_exists($file) && !strpos($photo->fileName, 'seeds'))
            unlink($file);
        $photo->delete();
        if ($profil === 1) {
            $newProfil = Photo::where('user_id', $_SESSION['user'])->getOne();
            $newProfil->profil = 1;
            $newProfil->save();
        }
        $this->addMessage(SUCCESS, ($profil) ? UPLOAD_DELETED_NEW :UPLOAD_DELETED);
        return $this->redirect('photos');
    }

    public function setProfil(\Slim\Http\Request $request)
    {
        $id = $request->getAttribute('id');
        $photo = Photo::where('id', $id)->where('user_id', $_SESSION['user'])->getOne();
        if (!$photo) {
            $this->addMessage(ERROR, UPLOAD_NOT_EXIST);
            return $this->redirect('photos');
        }
        if ($photo->profil == true) {
            $this->addMessage(ERROR, UPLOAD_NOT_EXIST);
            return $this->redirect('photos');
        }
        $oldPhoto = Photo::where('user_id', $_SESSION['user'])->where('profil', true)->getOne();
        if ($oldPhoto) {
            $oldPhoto->profil = false;
            $oldPhoto->save();
        }
        $photo->profil = true;
        $photo->save();
        $this->addMessage(SUCCESS, UPLOAD_SET_PROFIL);
        return $this->redirect('photos');
    }
}