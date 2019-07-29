<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 30/08/2018
 * Time: 11:55
 */

namespace App\Kernel\BaseServices;

use Anddye\Mailer\MessageBuilder;
use App\Kernel\Abstracts\BaseServiceAbstract;

/**
 * Class Mailer
 *
 * @package App\Kernel\BaseServices
 */
class Mailer extends BaseServiceAbstract
{
    /** @var \Anddye\Mailer\Mailer */
    private $mailer;

    /**
     * Init the mail service
     */
    private function init()
    {
        $settings = $this->getService('settings')['mailer'];
        $this->mailer = new \Anddye\Mailer\Mailer($this->container->view, $settings);
        $this->mailer->setDefaultFrom($settings['username'], $settings['name']);
        unset($settings);
    }

    /**
     * Send mail helper
     *
     * @param $to
     * @param $subject
     * @param $view
     * @param array $datas
     */
    public function send($to, $subject, $view, $datas = [])
    {
        if (!empty($to)) {
            $this->init();
            $this->mailer->sendMessage($view, $datas, function ($message) use ($subject, $to) {
                /** @var $message MessageBuilder */
                $message->setTo($to, $to);
                $message->setSubject($subject);
            });
        }
    }
}