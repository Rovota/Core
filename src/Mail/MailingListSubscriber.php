<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Mail;

use Rovota\Core\Database\Model;
use Rovota\Core\Support\Moment;

/**
 * @property int $id
 * @property int $mailing_list_id
 * @property int|null $user_id
 * @property string|null $name
 * @property string|null $address
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
class MailingListSubscriber extends Model
{

	protected string|null $table = 'mailing_list_subscribers';

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	protected bool $enable_composites = false;

}