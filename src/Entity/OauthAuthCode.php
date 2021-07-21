<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;

/**
 * @ORM\Entity
 * @ORM\Table(name="oauth_authcode")
 */
class OauthAuthCode extends BaseAuthCode
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @todo Add typed property
     * @var int id
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\OauthClient")
     * @ORM\JoinColumn(nullable=false)
     * @todo Add typed property
     * @var OauthClient $client
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * @todo Add typed property
     * @var User $user
     */
    protected $user;
}
