<?php

namespace Caldera\CriticalmassGlympseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $mbox = imap_open("{mail.caldera.cc:993/imap/ssl/novalidate-cert}", "malte@maltehuebner.com", "");

        echo "<h1>Postfächer</h1>\n";
        $folders = imap_listmailbox($mbox, "{mail.caldera.cc:993}", "*");

        if ($folders == false) {
            echo "Abruf fehlgeschlagen<br />\n";
        } else {
            foreach ($folders as $val) {
                echo $val . "<br />\n";
            }
        }

        echo "<h1>Nachrichten in INBOX</h1>\n";
        $headers = imap_headers($mbox);

        if ($headers == false) {
            echo "Abruf fehlgeschlagen<br />\n";
        } else {
            $counter = 0;

            foreach ($headers as $val) {
                ++$counter;
                $status = $val[1];

                if ($status == "D")
                {
                    $body = imap_body($mbox, $counter);

                    echo imap_qprint($body);
                }
            }
        }

        imap_close($mbox);

        return new Response();
    }
}
