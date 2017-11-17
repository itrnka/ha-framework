<?php
declare(strict_types = 1);

namespace ha\Component\Password;


use ha\Component\Password\Exception\PasswordEmptyException;
use ha\Component\Password\Exception\PasswordHashException;
use ha\Component\Password\Exception\PasswordPolicyException;

/**
 * Interface Password
 * @package ha\Component\Password
 */
interface Password
{

    /**
     * Password constructor.
     *
     * @param string|null $password
     */
    public function __construct(string $password = null);

    /**
     * Set password value.
     *
     * @param string $rawValue
     * @throws PasswordPolicyException
     */
    public function setValue(string $rawValue) : void;

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
    public function getHash(int $cost = null, string $salt = null) : string;

    /**
     * Verify password hash
     *
     * @param string $hash
     *
     * @return bool
     * @throws PasswordHashException
     */
    public function verify(string $hash) : bool;

    /**
     * Get raw password value.
     *
     * @return string
     */
    public function __invoke() : string;

    /**
     * Get password value as string.
     *
     * @return string
     */
    public function __toString() : string;

    /**
     * Returns whether password is set or not.
     *
     * @return bool
     */
    public function isSet() : bool;

}