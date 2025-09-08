<?php

namespace App\Constants;

class LeaveTypes
{
	public const SICK = 'sick';
	public const CASUAL = 'casual';
	public const PAID = 'paid';
	public const UNPAID = 'unpaid';

	public static function all(): array
	{
		return [
			self::SICK,
			self::CASUAL,
			self::PAID,
			self::UNPAID,
		];
	}
}

