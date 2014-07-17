<?php

namespace Caldera\CriticalmassGlympseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $mbox = @imap_open("{mail.caldera.cc:993/imap/ssl/novalidate-cert}", "glympse-invitations@criticalmass.in", "qwd32rf") or die(imap_last_error());

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

                if (true)
                {
                    echo "ID: ".$counter."<br />";
                    $body = imap_body($mbox, $counter);

                    preg_match_all('|----(.*) base64 (.*)==|U', $body, $results);

                    foreach ($results as $result)
                    {
                        preg_match('|http:\/\/glympse.com\/([A-Z0-9]{4,4})-([A-Z0-9]{4,4})|U', base64_decode($result[1]), $results2);

                        $inviteId = $results2[1].'-'.$results2[2];

                        echo $inviteId;
                        break;
                    }
                }
            }
        }

        imap_close($mbox);

        return new Response();
    }
}
