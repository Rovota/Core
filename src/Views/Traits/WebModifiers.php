<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Views\Traits;

use Rovota\Core\Auth\Interfaces\Identity;
use Rovota\Core\Partials\PartialManager;
use Rovota\Core\Storage\File;
use Rovota\Core\Storage\Media;

/**
 * @method meta(string $string, string[] $array)
 */
trait WebModifiers
{

	public function setTitle(string $title): static
	{
		$title = __($title);

		PartialManager::addOrUpdateVariable('*', 'page', [
			'title' => $title
		]);

		$this->meta('og:title', ['content' => $title]);
		$this->meta('twitter:title', ['content' => $title]);

		return $this;
	}

	public function setDescription(string $description): static
	{
		$description = __($description);

		PartialManager::addOrUpdateVariable('*', 'page', [
			'description' => $description,
		]);

		$this->meta('description', ['content' => $description]);
		$this->meta('og:description', ['content' => $description]);
		$this->meta('twitter:description', ['content' => $description]);

		return $this;
	}

	public function setKeywords(array $keywords): static
	{
		$keywords = implode(',', $keywords);

		PartialManager::addOrUpdateVariable('*', 'page', [
			'keywords' => $keywords,
		]);

		$this->meta('keywords', ['content' => $keywords]);

		return $this;
	}

	// -----------------

	public function setAuthor(Identity $author): static
	{
		PartialManager::addOrUpdateVariable('*', 'page', [
			'author' => $author,
		]);

		$this->meta('author', ['content' => $author->getName()]);

		return $this;
	}

	// -----------------

	public function setImage(Media|File|string $location): static
	{
		$public_url = match (true) {
			$location instanceof Media => $location->publicUrl(),
			$location instanceof File => $location->publicUrl(),
			default => $location,
		};

		PartialManager::addOrUpdateVariable('*', 'page', [
			'image' => $public_url,
		]);

		$this->meta('og:image', ['content' => $public_url]);
		$this->meta('og:image:secure_url', ['content' => $public_url]);
		$this->meta('twitter:image', ['content' => $public_url]);

		return $this;
	}

}