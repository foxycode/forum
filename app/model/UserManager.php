<?php

namespace App\Model;

use Nette;

class UserManager implements Nette\Security\IAuthenticator
{
    use Nette\SmartObject;

    /**
     * @var Nette\Database\Context
     */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    /**
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;

        $row = $this->database->table('user')->where('nick', $username)->fetch();

        if (!$row) {
            throw new Nette\Security\AuthenticationException('Špatné uživatelské jméno.', self::IDENTITY_NOT_FOUND);

        } elseif (md5($password) != $row['password']) {
            throw new Nette\Security\AuthenticationException('Špatné heslo.', self::INVALID_CREDENTIAL);

        }

        $this->database->table('user')
            ->where('user_id', $row->user_id)
            ->update(array('last_login' => new \DateTime));

        return new Nette\Security\Identity($row['user_id'], NULL, $row->toArray());
    }

    public function get($userId)
    {
        return $this->database->table('user')->where('user_id', $userId)->fetch();
    }

    public function update($id, $values)
    {
        $this->database->table('user')->where('user_id', $id)->update($values);
    }
}
