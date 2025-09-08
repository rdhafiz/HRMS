<?php

namespace App\Constants;

class AttendanceStatus
{
	public const PRESENT = 'present';
	public const ABSENT = 'absent';
	public const LATE = 'late';
	public const HALF_DAY = 'half_day';
	public const LEAVE = 'leave';
	public const HOLIDAY = 'holiday';

	public static function all(): array
	{
		return [
			self::PRESENT,
			self::ABSENT,
			self::LATE,
			self::HALF_DAY,
			self::LEAVE,
			self::HOLIDAY,
		];
	}
}

