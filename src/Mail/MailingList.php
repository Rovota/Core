<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Mail;

use Envms\FluentPDO\Exception;
use Rovota\Core\Database\Model;
use Rovota\Core\Facades\Cache;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Moment;

/**
 * @property int $id
 * @property string $name
 * @property string $label
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 *
 * @property-read Bucket $subscribers
 */
class MailingList extends Model
{

	protected string|null $table = 'mailing_lists';

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	// -----------------

	protected function getSubscribersAttribute(): Bucket
	{
		return Cache::remember('mailing_list_subscribers_'.$this->id, function () {
			return MailingListSubscriber::where('mailing_list_id', $this->id)->get();
		});
	}

	// -----------------

	/**
	 * @throws Exception
	 */
	public function createQueue(): MailingQueue
	{
		return MailingQueue::createFromList($this);
	}

}