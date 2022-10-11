<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Mail;

use Rovota\Core\Database\Model;
use Rovota\Core\Support\Moment;

/**
 * @property int $id
 * @property string $email
 * @property string|null $view
 * @property int $language_id
 * @property string $subject
 * @property string $type
 * @property string|null $code
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
class EmailEntry extends Model
{

	protected string|null $table = 'email_log';

	protected array $attributes = [
		'type' => 'regular',
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	protected bool $enable_composites = false;

}