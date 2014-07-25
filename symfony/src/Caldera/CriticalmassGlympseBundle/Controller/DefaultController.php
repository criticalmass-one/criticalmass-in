<?php

namespace Caldera\CriticalmassGlympseBundle\Controller;

use Caldera\CriticalmassGlympseBundle\Entity\Ticket;
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
                    echo "<br /><br />ID: ".$counter."<br />";
                    $body = imap_body($mbox, $counter);

                    $results = explode('----boundary', $body);

                    foreach ($results as $result)
                    {
                        if (strlen($result) > 0)
                        {
                            $result = '----boundary'.$result;

                            $string = base64_decode('----boundary'.$result);

                            //preg_match('/http:\/\/glympse.com\/([A-Z0-9]{4,4})-([A-Z0-9]{4,4})/U', $string, $results2);
                            preg_match('/([A-Z0-9]{4,4})-([A-Z0-9]{4,4})/U', $string, $results2);
                            if (count($results2) > 2)
                            {
                                $inviteId = $results2[1].'-'.$results2[2];

                                $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug('hamburg')->getCity();

                                $ticket = new Ticket();
                                $ticket->setCreationDateTime(new \DateTime());
                                $ticket->setInviteId($inviteId);
                                $ticket->setCity($city);

                                imap_mail_move($mbox, $counter, "INBOX.Done");

                                break;
                            }
                        }
                    }
                }
            }
        }

        imap_close($mbox);

        return new Response();
    }
}
