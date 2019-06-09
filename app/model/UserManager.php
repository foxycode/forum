<?php

namespace App\Model;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\SmartObject;
use Nette\Utils\ArrayHash;

final class UserManager implements IAuthenticator
{
    use SmartObject;

    /**
     * @var Context
     */
    private $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    /**
     * @param string[] $credentials
     *
     * @return Identity
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials): IIdentity
    {
        list($username, $password) = $credentials;

        $row = $this->database->table('user')->where('nick', $username)->fetch();

        if (!$row) {
            throw new AuthenticationException('Špatné uživatelské jméno.', self::IDENTITY_NOT_FOUND);

        } elseif (md5($password) != $row['password']) {
            throw new AuthenticationException('Špatné heslo.', self::INVALID_CREDENTIAL);

        }

        $this->database->table('user')
            ->where('user_id', $row->user_id)
            ->update(array('last_login' => new \DateTime));

        return new Identity($row['user_id'], NULL, $row->toArray());
    }

    public function get(int $userId): ?ActiveRow
    {
        return $this->database->table('user')->where('user_id', $userId)->fetch() ?: NULL;
    }

    public function update(int $id, ArrayHash $values): void
    {
        $this->database->table('user')->where('user_id', $id)->update($values);
    }
}
