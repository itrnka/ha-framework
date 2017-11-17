<?php
declare(strict_types = 1);

namespace ha\Component\Password;


use ha\Component\Password\Exception\PasswordEmptyException;
use ha\Component\Password\Exception\PasswordHashException;
use ha\Component\Password\Exception\PasswordPolicyException;

class PasswordDefault implements Password
{

    private $rawValue;

    /**
     * Password constructor.
     *
     * @param string|null $password
     */
    public function __construct(string $password = null)
    {
        if (!is_null($password)) {
            $this->setValue($password);
        }
    }

    /**
     * Set password value.
     *
     * @param string $rawValue
     * @throws PasswordPolicyException
     */
    public function setValue(string $rawValue) : void
    {
        if (strlen($rawValue) < 8) {
            throw new PasswordPolicyException('Password is too short@' . __METHOD__);
        }
        $this->rawValue = $rawValue;
    }

    /**
     * Get hashed version (this version can be stored in DB).
     *
     * @param int $cost
     * @param string|null $salt
     *
     * @return string
     * @throws PasswordEmptyException
     * @throws PasswordHashException
     */
    public function getHash(int $cost = null, string $salt = null) : string
    {
        if (is_null($cost)) $cost = 10;
        if (is_null($this->rawValue)) {
            throw new PasswordEmptyException('Password value not found@' . __METHOD__);
        }
        $hash = password_hash($this->rawValue, PASSWORD_DEFAULT, ['cost' => $cost]);
        if (!$hash) {
            throw new PasswordHashException('Password hashing failed@' . __METHOD__);
        }
        return $hash;
    }

    /**
     * Verify password hash
     *
     * @param string $hash
     *
     * @return bool
     */
    public function verify(string $hash) : bool
    {
        if (is_null($this->rawValue)) {
            return false;
        }
        return password_verify($this->rawValue, $hash);
    }

    /**
     * Get raw password value.
     *
     * @return string
     */
    public function __invoke() : string
    {
        return $this->rawValue;
    }

    /**
     * Get password value as string.
     *
     * @return string
     * @throws PasswordEmptyException
     */
    public function __toString() : string
    {
        return strval($this->rawValue);
    }

    /**
     * Returns whether password is set or not.
     *
     * @return bool
     */
    public function isSet() : bool
    {
        return is_string($this->rawValue);
    }

}