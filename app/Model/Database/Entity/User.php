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

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getSurname(): string
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     */
    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    /**
     * @param string $password_hash
     */
    public function setPasswordHash(string $password_hash): void
    {
        $this->password_hash = $password_hash;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getUuidRegistration(): string
    {
        return $this->uuid_registration;
    }

    /**
     * @param string $uuid_registration
     */
    public function setUuidRegistration(string $uuid_registration): void
    {
        $this->uuid_registration = $uuid_registration;
    }

    /**
     * @return string
     */
    public function getUuidLostPassword(): string
    {
        return $this->uuid_lost_password;
    }

    /**
     * @param string $uuid_lost_password
     */
    public function setUuidLostPassword(string $uuid_lost_password): void
    {
        $this->uuid_lost_password = $uuid_lost_password;
    }

    /**
     * @return bool
     */
    public function isIsActive(): bool
    {
        return $this->is_active;
    }

    /**
     * @param bool $is_active
     */
    public function setIsActive(bool $is_active): void
    {
        $this->is_active = $is_active;
    }

    /**
     * @return bool
     */
    public function isIsAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * @param bool $is_admin
     */
    public function setIsAdmin(bool $is_admin): void
    {
        $this->is_admin = $is_admin;
    }

    /**
     * @return bool
     */
    public function isRegistrationMailSended(): bool
    {
        return $this->registration_mail_sended;
    }

    /**
     * @param bool $registration_mail_sended
     */
    public function setRegistrationMailSended(bool $registration_mail_sended): void
    {
        $this->registration_mail_sended = $registration_mail_sended;
    }

    /**
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->created_on;
    }

    /**
     * @param \DateTime $created_on
     */
    public function setCreatedOn(\DateTime $created_on): void
    {
        $this->created_on = $created_on;
    }

    /**
     * @return mixed
     */
    public function getUpdatedOn()
    {
        return $this->updated_on;
    }

    /**
     * @param \DateTime $updated_on
     */
    public function setUpdatedOn(\DateTime $updated_on): void
    {
        $this->updated_on = $updated_on;
    }

    /**
     * @return mixed
     */
    public function getDeletedOn()
    {
        return $this->deleted_on;
    }

    /**
     * @param \DateTime $deleted_on
     */
    public function setDeletedOn(\DateTime $deleted_on): void
    {
        $this->deleted_on = $deleted_on;
    }

    ///////////////////////////////////
    /// functions
    ///////////////////////////////////

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {

        $dateTime = new \DateTime("now");
        $this->created_on = $dateTime;
        $this->updated_on = $dateTime;

        if ($this->id !== NULL) {
            throw new LogicException("Entity id field should be null during prePersistEvent");
        }
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onPreUpdate()
    {
        $dateTime = new \DateTime("now");
        $this->updated_on = $dateTime;
    }

}
