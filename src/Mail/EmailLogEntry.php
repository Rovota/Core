<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Mail;

use Rovota\Core\Database\Model;
use Rovota\Core\Support\Moment;

/**
 * @property int $id
 * @property string $receiver
 * @property string|null $view
 * @property int $language_id
 * @property string $subject
 * @property string $type
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
class EmailLogEntry extends Model
{

	protected string|null $table = '_email_log';

	protected array $attributes = [
		'type' => 'regular',
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	protected bool $enable_composites = false;

}