<?php

declare(strict_types=1);

namespace App\Model\Database\Entity;


use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;
use LogicException;


/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 **/
class User
{
    use Id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $firstname;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $surname;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $password_hash;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $language;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $uuid_registration;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $uuid_lost_password;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $is_active;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $is_admin;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $registration_mail_sended;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_on;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_on;

    /**
     * @ORM\Column(type="datetime", nullable = true)
     */
    private $deleted_on;



}
