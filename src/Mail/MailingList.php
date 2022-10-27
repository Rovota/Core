<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Mail;

use Rovota\Core\Database\Model;
use Rovota\Core\Facades\Cache;
use Rovota\Core\Support\Collection;
use Rovota\Core\Support\Moment;

/**
 * @property int $id
 * @property string $name
 * @property string $label
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 *
 * @property-read Collection $subscribers
 */
class MailingList extends Model
{

	protected string|null $table = 'mailing_lists';

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	// -----------------

	protected function getSubscribersAttribute(): Collection
	{
		return Cache::remember('mailing_list_subscribers_'.$this->id, function () {
			return MailingListSubscriber::where('mailing_list_id', $this->id)->get();
		});
	}

	// -----------------

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public function createQueue(): MailingQueue
	{
		return MailingQueue::createFromList($this);
	}

}