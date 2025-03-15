<?php

namespace Domain\Exceptions;

class InvalidTotpSecretException extends \InvalidArgumentException {}
class InvalidTotpIssuerException extends \InvalidArgumentException {}
class InvalidTotpAccountException extends \InvalidArgumentException {} 